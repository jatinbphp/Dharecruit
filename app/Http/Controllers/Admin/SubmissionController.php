<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Requirement;
use App\Models\Submission;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('accessright:manage_submission');
    }

    public function index(Request $request){
        $data['menu'] = "Requirements";
        $data['search'] = $request['search'];

        if ($request->ajax()) {
            $data = $this->Filter($request);
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('user_id', function($row){
                    return $row->BDM->name;
                })
                ->addColumn('category', function($row){
                    return $row->Category->name;
                })
                ->addColumn('created_at', function($row){
                    return '<div class="border border-dark floar-left p-1 mt-2" style="
                    border-radius: 5px; width: auto"><span>'.$row->created_at->diffForHumans().'</span></div>';
                })
                ->addColumn('recruiter', function($row){
                    $rId = !empty($row->recruiter) ? explode(',',$row->recruiter) : [];
                    $recName = '';
                    if(count($rId)>0){
                        foreach ($rId as $uid){
                            $recUser = Admin::where('id',$uid)->first();
                            if(!empty($recUser)){
                                $submission = Submission::where('user_id',$uid)->where('requirement_id',$row->id)->count();
                                $recName .='<div class="border border-dark floar-left p-1 mt-2" style="
                                border-radius: 5px; width: auto"><span>'. $submission.' '.$recUser['name']. '</span></div>';
                            }
                        }
                    }
                    return $recName;
                })
                ->addColumn('status', function($row){
                    $statusBtn = '';
                    $user = Auth::user();
                    if($user['role'] == 'admin' || $user['id'] == $row->user_id){
                        if ($row->status == "hold") {
                            $statusBtn .= '<div class="btn-group-horizontal" id="assign_remove_"'.$row->id.'">
                                                <button class="btn btn-danger unassign ladda-button" data-style="slide-left" id="remove" url="'.route('requirement.unassign').'" ruid="'.$row->id.'" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Hold</span> </button>
                                           </div>';
                            $statusBtn .= '<div class="btn-group-horizontal" id="assign_add_"'.$row->id.'"  style="display: none"  >
                                                    <button class="btn btn-success assign ladda-button" data-style="slide-left" id="assign" uid="'.$row->id.'" url="'.route('requirement.assign').'" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Need</span></button>
                                                </div>';
                        }
                        if ($row->status == "unhold") {
                            $statusBtn .= '<div class="btn-group-horizontal" id="assign_add_"'.$row->id.'">
                                                    <button class="btn btn-success assign ladda-button" id="assign" data-style="slide-left" uid="'.$row->id.'" url="'.route('requirement.assign').'" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Need</span></button>
                                                </div>';
                            $statusBtn .= '<div class="btn-group-horizontal" id="assign_remove_"'.$row->id.'" style="display: none" >
                                                    <button class="btn  btn-danger unassign ladda-button" id="remove" ruid="'.$row->id.'" data-style="slide-left" url="'.route('requirement.unassign').'" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Hold</span></button>
                                                </div>';
                        }
                    }else{
                        if ($row->status == "hold") {
                            $statusBtn .= '<div class="btn-group-horizontal">
                                                <button class="btn btn-danger noChange ladda-button" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Hold</span></button>
                                           </div>';
                        }
                        if ($row->status == "unhold") {
                            $statusBtn .= '<div class="btn-group-horizontal">
                                                <button class="btn btn-success noChange ladda-button" data-style="slide-left" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Need</span></button>
                                            </div>';
                        }
                    }
                    return $statusBtn;
                })
                ->addColumn('color', function($row){
                    $color = '';
                    if(!empty($row->recruiter)){
                        Log::info('Status Color Yellow==>');
                        $color = '<div style="width:50px; background-color: yellow;">&nbsp;</div>';
                    }
                    $submission = Submission::where('requirement_id',$row->id)->count();
                    if($submission > 0){
                        Log::info('Status Color Green==>');
                        $color = '<div style="width:50px; background-color: green;">&nbsp;</div>';
                    }
                    $rejectionCount = Submission::where('requirement_id',$row->id)->where('status','rejected')->count();
                    if($rejectionCount > 0){
                        Log::info('Status Color Red==>');
                        $color = '<div style="width:50px; background-color: red;">&nbsp;</div>';
                    }
                    return $color;
                })
                ->addColumn('candidate', function($row){
                    $allSubmission = Submission::where('requirement_id',$row->id)->where('status','!=','reject')->get();
                    $user = Auth::user();
                    $candidate = '<br>';
                    if(count($allSubmission) > 0){
                        foreach ($allSubmission as $list){
                            $textColor = $list['status'] == 'rejected' ? 'text-danger' : '';
                            $candidate .= '<span class="candidate '.$textColor.'"  data-cid="'.$list['id'].'">'.$list['name'].'-'.$list['id'].'</span><br>';
                        }
                    }
                    return $candidate;
                })
                ->addColumn('action', function($row){
                    if($row['submissionCounter'] < 3){
                        $rId = !empty($row->recruiter) ? explode(',',$row->recruiter) : [];
                        if(!empty($rId) && in_array(Auth::user()->id,$rId)){
                            $btn = '<div class="btn-group btn-group-sm mr-2"><a href="'.url('admin/submission/'.$row->id).'"><button class="btn btn-sm btn-default tip" data-toggle="tooltip" title="View Submission" data-trigger="hover" type="submit" ><i class="fa fa-eye"></i></button></a></div>';
                            $btn .= '<div class="btn-group btn-group-sm"><a href="'.url('admin/submission/new/'.$row->id).'"><button class="btn btn-sm btn-default tip" data-toggle="tooltip" title="Add New Submission" data-trigger="hover" type="submit" ><i class="fa fa-upload"></i></button></a></div>';
                        }else{
                            $btn = '';
                            if($row->status != "hold"){
                                $btn = '<span data-toggle="tooltip" title="Assign Requirement" data-trigger="hover">
                                    <button class="btn btn-sm btn-default assignRequirement mr-2" data-id="'.$row->id.'" type="button"><i class="fa fa-plus-square"></i></button>
                                </span>';
                            }
                        }
                    }else{
                        $btn = '';
                    }
                    return $btn;
                })
                ->addColumn('client', function($row) {
                    $clientName = '';
                    if($row->display_client == '1'){
                        $clientName = $row->client_name;
                    }
                    return $clientName;
                })
                ->rawColumns(['user_id','category','created_at','recruiter','status','color','candidate','action','client'])
                ->make(true);
        }
        $data['type'] = 1;
        return view('admin.submission.index', $data);
    }

    public function myRequirement(Request $request){
        $data['menu'] = "My Requirements";
        $data['search'] = $request['search'];

        if ($request->ajax()) {
            $request['authId'] = Auth::user()->id;
            $data = $this->Filter($request);
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('user_id', function($row){
                    return $row->BDM->name;
                })
                ->addColumn('category', function($row){
                    return $row->Category->name;
                })
                ->addColumn('created_at', function($row){
                    return '<div class="border border-dark floar-left p-1 mt-2" style="
                    border-radius: 5px; width: auto"><span>'.$row->created_at->diffForHumans().'</span></div>';
                })
                ->addColumn('recruiter', function($row){
                    $rId = !empty($row->recruiter) ? explode(',',$row->recruiter) : [];
                    $recName = '';
                    if(count($rId)>0){
                        foreach ($rId as $uid){
                            $recUser = Admin::where('id',$uid)->first();
                            if(!empty($recUser)){
                                $submission = Submission::where('user_id',$uid)->where('requirement_id',$row->id)->count();
                                $recName .='<div class="border border-dark floar-left p-1 mt-2" style="
                                border-radius: 5px; width: auto"><span>'. $submission.' '.$recUser['name']. '</span></div>';
                            }
                        }
                    }
                    return $recName;
                })
                ->addColumn('status', function($row){
                    $statusBtn = '';
                    $user = Auth::user();
                    if($user['role'] == 'admin' || $user['id'] == $row->user_id){
                        if ($row->status == "hold") {
                            $statusBtn .= '<div class="btn-group-horizontal" id="assign_remove_"'.$row->id.'">
                                                <button class="btn btn-danger unassign ladda-button" data-style="slide-left" id="remove" url="'.route('requirement.unassign').'" ruid="'.$row->id.'" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Hold</span> </button>
                                           </div>';
                            $statusBtn .= '<div class="btn-group-horizontal" id="assign_add_"'.$row->id.'"  style="display: none"  >
                                                    <button class="btn btn-success assign ladda-button" data-style="slide-left" id="assign" uid="'.$row->id.'" url="'.route('requirement.assign').'" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Need</span></button>
                                                </div>';
                        }
                        if ($row->status == "hold") {
                            $statusBtn .= '<div class="btn-group-horizontal" id="assign_add_"'.$row->id.'">
                                                    <button class="btn btn-success assign ladda-button" id="assign" data-style="slide-left" uid="'.$row->id.'" url="'.route('requirement.assign').'" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Need</span></button>
                                                </div>';
                            $statusBtn .= '<div class="btn-group-horizontal" id="assign_remove_"'.$row->id.'" style="display: none" >
                                                    <button class="btn  btn-danger unassign ladda-button" id="remove" ruid="'.$row->id.'" data-style="slide-left" url="'.route('requirement.unassign').'" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Hold</span></button>
                                                </div>';
                        }
                    }else{
                        if ($row->status == "hold") {
                            $statusBtn .= '<div class="btn-group-horizontal">
                                                <button class="btn btn-danger noChange ladda-button" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Hold</span></button>
                                           </div>';
                        }
                        if ($row->status == "unhold") {
                            $statusBtn .= '<div class="btn-group-horizontal">
                                                <button class="btn btn-success noChange ladda-button" data-style="slide-left" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Need</span></button>
                                            </div>';
                        }
                    }
                    return $statusBtn;
                })
                ->addColumn('color', function($row){
                    $color = '';
                    if(!empty($row->recruiter)){
                        Log::info('Status Color Yellow==>');
                        $color = '<div style="width:50px; background-color: yellow;">&nbsp;</div>';
                    }
                    $submission = Submission::where('requirement_id',$row->id)->count();
                    if($submission > 0){
                        Log::info('Status Color Green==>');
                        $color = '<div style="width:50px; background-color: green;">&nbsp;</div>';
                    }
                    $rejectionCount = Submission::where('requirement_id',$row->id)->where('status','rejected')->count();
                    if($rejectionCount > 0){
                        Log::info('Status Color Red==>');
                        $color = '<div style="width:50px; background-color: red;">&nbsp;</div>';
                    }
                    return $color;
                })
                ->addColumn('candidate', function($row){
                    $allSubmission = Submission::where('requirement_id',$row->id)->where('status','!=','reject')->get();
                    $user = Auth::user();
                    $candidate = '<br>';
                    if(count($allSubmission) > 0){
                        foreach ($allSubmission as $list){
                            $textColor = $list['status'] == 'rejected' ? 'text-danger' : '';
                            $candidate .= '<span class="candidate '.$textColor.'"  data-cid="'.$list['id'].'">'.$list['name'].'-'.$list['id'].'</span><br>';
                        }
                    }
                    return $candidate;
                })
                ->addColumn('action', function($row){
                    if($row->submissionCounter < 3){
                        $rId = !empty($row->recruiter) ? explode(',',$row->recruiter) : [];
                        if(!empty($rId) && in_array(Auth::user()->id,$rId)){
                            $btn = '<div class="btn-group btn-group-sm mr-2"><a href="'.url('admin/submission/'.$row->id).'"><button class="btn btn-sm btn-default tip" data-toggle="tooltip" title="View Submission" data-trigger="hover" type="submit" ><i class="fa fa-eye"></i></button></a></div>';
                            $btn .= '<div class="btn-group btn-group-sm"><a href="'.url('admin/submission/new/'.$row->id).'"><button class="btn btn-sm btn-default tip" data-toggle="tooltip" title="Add New Submission" data-trigger="hover" type="submit" ><i class="fa fa-upload"></i></button></a></div>';
                        }else{
                            $btn = '';
                            if($row->status != "hold"){
                                $btn = '<span data-toggle="tooltip" title="Assign Requirement" data-trigger="hover">
                                    <button class="btn btn-sm btn-default assignRequirement mr-2" data-id="'.$row->id.'" type="button"><i class="fa fa-plus-square"></i></button>
                                </span>';
                            }
                        }
                    }else{
                        $btn = '';
                    }
                    return $btn;
                })
                ->addColumn('client', function($row) {
                    $clientName = '';
                    if($row->display_client == '1'){
                        $clientName = $row->client_name;
                    }
                    return $clientName;
                })
                ->rawColumns(['user_id','category','created_at','recruiter','status','color','candidate','action','client'])
                ->make(true);
        }
        $data['type'] = 2;
        return view('admin.submission.index', $data);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'location' => 'required',
            'phone' => 'required|numeric',
            'employer_detail' => 'required',
            'work_authorization' => 'required',
            //'recruiter_rate' => 'required',
            'last_4_ssn' => 'required',
            'education_details' => 'required',
            'resume_experience' => 'required',
            'linkedin_id' => 'required',
            'relocation' => 'required',
            'vendor_rate' => 'required',
        ]);

        $requirement = Requirement::where('id',$request['requirement_id'])->first();
        $existSubmission = Submission::where('requirement_id',$request['requirement_id'])->where('email',$request['email'])->first();
        if(!empty($existSubmission)){
            $msg = $existSubmission['name'].':'.$existSubmission['id'].' has been already submitted to the '.$requirement['job_title'];
            \Session::flash('danger', $msg);
            return redirect()->back();
        }

        $input = $request->all();
        $input['user_id'] = Auth::user()->id;

        if(empty($request['existResume'])){
            $this->validate($request, [
                'resume' => 'required|mimes:doc,docx,pdf',
            ]);
            if($file = $request->file('resume')){
                $input['documents'] = $this->fileMove($file,'user_documents');
            }
        }else{
            $input['documents'] = $request['existResume'];
        }
        Submission::create($input);

        if($requirement['submissionCounter'] < 3){
            $in['submissionCounter'] = $requirement['submissionCounter'] + 1;
            $requirement->update($in);
        }

        if($requirement['submissionCounter'] == 3){
            $in['status'] = 'hold';
            $requirement->update($in);
        }

        \Session::flash('success', 'New submission has been inserted successfully!');
        return redirect(route('submission.show',['submission'=>$request['requirement_id']]));
    }

    public function show(Request $request, $id)
    {
        $data['menu'] = "Requirements";
        $data['sub_menu'] = "Submission";
        $data['all_submission'] = Submission::where('requirement_id',$id)->get();

        if ($request->ajax()) {
            $data = $this->submissionFilter($request,$id);
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('user_id', function($row){
                    return $row->Recruiters->name;
                })
                ->addColumn('documents', function($row){
                    return '<a href="'.asset('storage/'.$row->documents).'" target="_blank"><img src="'.url('assets/dist/img/resume.png').'" height="50"></a>';
                })
                ->addColumn('status', function($row){
                    return ucfirst($row->status);
                })
                ->addColumn('action', function($row){
                    $btn = '<div class="btn-group btn-group-sm view" data-cid="'.$row->id.'"><button class="btn btn-sm btn-default tip" data-toggle="tooltip" title="View Submission" data-trigger="hover"><i class="fa fa-eye"></i></button></a></div>';
                    return $btn;
                })
                ->rawColumns(['user_id','documents','status','action'])
                ->make(true);
        }

        $data['requirement'] = Requirement::where('id',$id)->first();
        return view('admin.submission.list',$data);
    }

    public function submissionAdd($id){
        $data['menu'] = "Requirements";
        $data['sub_menu'] = "Submission";
        $data['requirement'] = Requirement::where('id',$id)->first();
        if(!empty($data['requirement'])){
            $user = Auth::user();
            $recruiter = explode(',',$data['requirement']['recruiter']);
            if($user['role'] == 'recruiter' && !empty($recruiter)){
                if(!in_array($user['id'],$recruiter)){
                    \Session::flash('danger', 'Requirement is not assigned to you');
                    return redirect(route('submission.index'));
                }
            }
        }
        $requirementCount = Requirement::where('id',$id)->first();
        if($requirementCount['submissionCounter'] == 3){
            \Session::flash('danger', 'Submission is hold!');
            return redirect(route('submission.index'));
        }
        return view('admin.submission.create',$data);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        $submission = Submission::findOrFail($id);
        if(!empty($submission)){
            Storage::delete($submission['documents']);
            $submission->delete();
            return 1;
        }else{
            return 0;
        }
    }

    public function assignSubmission($id){
        $requirement = Requirement::where('id',$id)->first();
        if(!empty($requirement)){
            $input['recruiter'] = !empty($requirement['recruiter']) ? $requirement['recruiter'].Auth::user()->id.',' : ','.Auth::user()->id.',';
            $requirement->update($input);
            return 1;
        }else{
            return 0;
        }
    }

    public function getAlreadyAddedUserDetail(Request $request){
        if(!$request || !$request->get('email')){
            return '';
        }

        $email = $request->get('email');
        $data = Submission::where('email',$email)->latest()->first();

        if(!$data) {
            return '';
        }

        return response()->json(['data'=> $data], 200);
    }
}
