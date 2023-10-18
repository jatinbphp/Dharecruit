<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Moi;
use App\Models\PVCompany;
use App\Models\Requirement;
use App\Models\Submission;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RequirementController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('accessright:manage_requirement');
    }

    public function index(Request $request)
    {
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
                    return '<span class="border border-dark floar-left p-1 mt-2" style="
                    border-radius: 5px; width: auto">'.$row->created_at->diffForHumans().'</span>';
                })
                ->addColumn('recruiter', function($row){
                    $rId = !empty($row->recruiter) ? explode(',',$row->recruiter) : [];
                    $recName = '';
                    if(count($rId)>0){
                        foreach ($rId as $uid){
                            $recUser = Admin::where('id',$uid)->first();
                            if(!empty($recUser)){
                                $submission = Submission::where('user_id',$uid)->where('requirement_id',$row->id)->count();
                                $recName .='<span class="border border-dark float-left p-1 mt-2" style="
                                border-radius: 5px;">'. $submission.' '.$recUser['name']. '</span>';
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
                                                <button class="btn btn-success noChange ladda-button" data-style="slide-left" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Unhold</span></button>
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
                    $user = Auth::user();
                    $btn = '';
                    if(($user['role'] == 'admin') || ($user['role'] == 'bdm' && $user['id'] == $row->user_id)){
                        $btn .= '<div class="btn-group btn-group-sm mr-2"><a href="'.url('admin/requirement/'.$row->id.'/edit').'"><button class="btn btn-sm btn-default tip" data-toggle="tooltip" title="Edit Requirement" data-trigger="hover" type="submit" ><i class="fa fa-edit"></i></button></a></div>';
                    }
                    if($user['role'] == 'admin'){
                        $btn .= '<span data-toggle="tooltip" title="Delete Requirement" data-trigger="hover">
                                    <button class="btn btn-sm btn-default deleteRequirement mr-2" data-id="'.$row->id.'" type="button"><i class="fa fa-trash"></i></button>
                                </span>';
                    }
                    $btn .= '<div class="btn-group btn-group-sm"><a href="'.url('admin/requirement/'.$row->id).'"><button class="btn btn-sm btn-default tip" data-toggle="tooltip" title="View Submission" data-trigger="hover" type="submit" ><i class="fa fa-eye"></i></button></a></div>';
                    return $btn;
                })
                ->addColumn('client', function($row) {
                    $clientName = '';
                    if($row->display_client == '1'){
                        $clientName = $row->client_name;
                    }
                    return $clientName;
                })
                ->rawColumns(['user_id','category','created_at','recruiter','status','color','candidate','action'])
                ->make(true);
        }
        $data['type'] = 1;
        return view('admin.requirement.index', $data);
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
                    return '<span class="border border-dark floar-left p-1 mt-2" style="
                    border-radius: 5px; width: auto">'.$row->created_at->diffForHumans().'</span>';
                })
                ->addColumn('recruiter', function($row){
                    $rId = !empty($row->recruiter) ? explode(',',$row->recruiter) : [];
                    $recName = '';
                    if(count($rId)>0){
                        foreach ($rId as $uid){
                            $recUser = Admin::where('id',$uid)->first();
                            if(!empty($recUser)){
                                $submission = Submission::where('user_id',$uid)->where('requirement_id',$row->id)->count();
                                $recName .='<span class="border border-dark float-left p-1 mt-2" style="
                                border-radius: 5px;">'. $submission.' '.$recUser['name']. '</span>';
                            }
                        }
                    }
                    return $recName;
                })
                ->addColumn('status', function($row){
                    $statusBtn = '';
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
                    $candidate = '<br>';
                    if(count($allSubmission) > 0){
                        foreach ($allSubmission as $list){
                            $textColor = $list['status'] == 'rejected' ? 'text-danger' : '' ;
                            $candidate .= '<span class="candidate '.$textColor.'" data-cid="'.$list['id'].'">'.$list['name'].'-'.$list['id'].'</span><br>';
                        }
                    }
                    return $candidate;
                })
                ->addColumn('action', function($row){
                    $user = Auth::user();
                    $btn = '';
                    if($user['role'] == 'bdm' && $user['id'] == $row->user_id){
                        $btn .= '<div class="btn-group btn-group-sm mr-2"><a href="'.url('admin/requirement/'.$row->id.'/edit').'"><button class="btn btn-sm btn-default tip" data-toggle="tooltip" title="Edit Requirement" data-trigger="hover" type="submit" ><i class="fa fa-edit"></i></button></a></div>';
                    }
                    if($user['role'] == 'admin'){
                        $btn .= '<span data-toggle="tooltip" title="Delete Requirement" data-trigger="hover">
                                    <button class="btn btn-sm btn-default deleteRequirement mr-2" data-id="'.$row->id.'" type="button"><i class="fa fa-trash"></i></button>
                                </span>';
                    }
                    $btn .= '<div class="btn-group btn-group-sm"><a href="'.url('admin/requirement/'.$row->id).'"><button class="btn btn-sm btn-default tip" data-toggle="tooltip" title="View Submission" data-trigger="hover" type="submit" ><i class="fa fa-eye"></i></button></a></div>';
                    return $btn;
                })
                ->addColumn('client', function($row) {
                    $clientName = '';
                    if($row->display_client == '1'){
                        $clientName = $row->client_name;
                    }
                    return $clientName;
                })
                ->rawColumns(['user_id','category','created_at','recruiter','status','color','candidate','action'])
                ->make(true);
        }
        $data['type'] = 2;
        return view('admin.requirement.index', $data);
    }

    public function create()
    {
        $data['menu'] = "Requirements";
        $user = $this->getUser();
        if($user['role'] == 'admin'){
            $data['category'] = Category::where('status','active')->pluck('name','id')->prepend('Please Select','');
            $data['moi'] = Moi::where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
            $data['pv_company'] = PVCompany::where('status','active')->pluck('name','id')->prepend('Please Select','');
        }else{
            $data['category'] = Category::where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
            $data['moi'] = Moi::where('user_id',Auth::user()->id)->where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
            $data['pv_company'] = PVCompany::where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
        }
        return view("admin.requirement.create",$data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'job_title' => 'required',
            'no_of_position' => 'required',
            'experience' => 'required',
            'location' => 'required',
            'work_type' => 'required',
            'duration' => 'required',
            'visa' => 'required',
            'client' => 'required',
            'vendor_rate' => 'required',
            'my_rate' => 'required',
            'priority' => 'required',
            'term' => 'required',
            'category' => 'required',
            'moi' => 'required',
            'job_keyword' => 'required',
            'description' => 'required',
            'pv_company_name' => 'required',
            'poc_name' => 'required',
            'poc_email' => 'required',
            'poc_phone_number' => 'required',
            'client_name' => 'required',
        ]);

        $input = $request->all();
        $input['user_id'] = Auth::user()->id;
        $input['job_id'] = 0;
        if(isset($input['display_client']) && $input['display_client'] == 'on'){
            $input['display_client'] = 1;
        } else {
            $input['display_client'] = 0;
        }
        $req = Requirement::create($input);
        if($req){
            $requirements = Requirement::where('id',$req['id'])->first();
            $in['job_id'] = $requirements['id'];
            $requirements->update($in);
        }

        \Session::flash('success', 'Requirement has been inserted successfully!');
        return redirect()->route('requirement.index');
    }

    public function show(Request $request, $id){
        $data['menu'] = "Requirements";
        if ($request->ajax()) {
            $data = Submission::where('requirement_id',$id)->where('status','accepted')->select();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('user_id', function($row){
                    return $row->Recruiters->name;
                })
                ->addColumn('documents', function($row){
                    if(Storage::disk('public')->exists($row->documents)) {
                        return '<a href="'.asset('storage/'.$row->documents).'" target="_blank"><img src="'.url('assets/dist/img/resume.png').'" height="50"></a>';
                    } else {
                        return '';
                    }
                })
                ->addColumn('status', function($row){
                    $status = '<select name="status" class="form-control select2 submissionStatus" data-id="'.$row->id.'">';
                    $submissionStatus = Submission::$status;
                    foreach ($submissionStatus as $key => $val){
                        $selected = $row->status == $key ? 'selected' : '';
                        $status .= '<option value="'.$key.'" '.$selected.'>'.$val.'</option>';
                    }
                    $status .= '</select>';
                    return $status;
                })
                ->rawColumns(['user_id','documents','status'])
                ->make(true);
        }
        $data['requirement'] = Requirement::where('id',$id)->first();
        return view('admin.requirement.submission', $data);
    }

    public function edit($id)
    {
        $data['menu'] = "Requirements";
        $data['requirement'] = Requirement::where('id',$id)->first();
        $user = $this->getUser();
        if($user['role'] == 'admin'){
            $data['category'] = Category::where('status','active')->pluck('name','id')->prepend('Please Select','');
            $data['moi'] = Moi::where('status','active')->pluck('name','id')->prepend('Please Select','');
            $data['pv_company'] = PVCompany::where('status','active')->pluck('name','id')->prepend('Please Select','');
        }else{
            if($user['id'] != $data['requirement']['user_id']){
                \Session::flash('danger',"You can not update other's requirement.");
                return redirect(route('requirement.index'));
            }

            $data['category'] = Category::where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
            $data['moi'] = Moi::where('user_id',Auth::user()->id)->where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
            $data['pv_company'] = PVCompany::where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
        }
        return view('admin.requirement.edit',$data);
    }

    public function update(Request $request, $id)
    {
        \Log::info($request);
        $this->validate($request, [
            'job_title' => 'required',
            'no_of_position' => 'required',
            'experience' => 'required',
            'location' => 'required',
            'work_type' => 'required',
            'duration' => 'required',
            'visa' => 'required',
            'client' => 'required',
            'vendor_rate' => 'required',
            'my_rate' => 'required',
            'priority' => 'required',
            'term' => 'required',
            'category' => 'required',
            'moi' => 'required',
            'job_keyword' => 'required',
            'description' => 'required',
            'pv_company_name' => 'required',
            'poc_name' => 'required',
            'poc_email' => 'required',
            'poc_phone_number' => 'required',
            'client_name' => 'required',
        ]);

        $input = $request->all();
        if(isset($input['display_client']) && $input['display_client'] == 'on'){
            $input['display_client'] = 1;
        } else {
            $input['display_client'] = 0;
        }
        $requirement = Requirement::findorFail($id);
        $requirement->update($input);

        \Session::flash('success','Requirement has been updated successfully!');
        return redirect()->route('requirement.index');
    }

    public function destroy($id)
    {
        $requirements = Requirement::findOrFail($id);
        if(!empty($requirements)){
            $requirements->delete();
            return 1;
        }else{
            return 0;
        }
    }

    public function assign(Request $request){
        $requirement = Requirement::findorFail($request['id']);
        $requirement['status'] = "hold";
        $requirement->update($request->all());
    }

    public function unassign(Request $request){
        $requirement = Requirement::findorFail($request['id']);
        $requirement['status'] = "unhold";
        $requirement['submissionCounter'] = 0;
        $requirement->update($request->all());
    }

    public function changeStatus(Request $request, $id){
        $submission = Submission::where('id',$id)->first();
        if(!empty($submission)){
            $input['status'] = $request['status'];
            $submission->update($input);
            return 1;
        }else{
           return 0;
        }
    }

    public function getCandidate(Request $request){
        $submission = Submission::with('Recruiters','Requirement','Requirement.BDM','Requirement.Category','Requirement.PvCompany')->where('id',$request['cId'])->first();
        $rData = '';
        $cData = '';
        $jobTitle = '';
        $candidateStatus = '';
        $commSkills = '';
        $skiilsMatch = '';
        $reason = '';
        $status = 0;
        if(!empty($submission)){
            $status = 1;
            $candidateStatus = $submission['status'];
            $commSkills = $submission['common_skills'];
            $skiilsMatch = $submission['skills_match'];
            $reason = $submission['skills_match'];
            $jobTitle = $submission['Requirement']['job_title'];
            $rData .= '<h3>Requirement</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>BDM:</strong> '.$submission['Requirement']['BDM']['name'].'
                            </div>
                            <div class="col-md-6">
                                <strong>Recruiter:</strong> '.$submission['Recruiters']['name'].'
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <strong>Duration:</strong> '.$submission['Requirement']['duration'].'
                            </div>
                            <div class="col-md-6">
                                <strong>Location:</strong> '.$submission['Requirement']['location'].'
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <strong>Category:</strong> '.$submission['Requirement']['Category']['name'].'
                            </div>
                            <div class="col-md-6">
                                <strong>Work Type:</strong> '.$submission['Requirement']['work_type'].'
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <strong>Pv Company:</strong> '.$submission['Requirement']['PvCompany']['name'].'
                            </div>
                            <div class="col-md-6">
                                <strong>Poc Email:</strong> '.$submission['Requirement']['poc_email'].'
                            </div>
                        </div>';

            $cData .= '<h3>Candidate</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Recruiter:</strong> '.$submission['Recruiters']['name'].'
                            </div>
                            <div class="col-md-6">
                                <strong>Name:</strong> '.$submission['name'].'
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <strong>Email:</strong> '.$submission['email'].'
                            </div>
                            <div class="col-md-6">
                                <strong>Location:</strong> '.$submission['location'].'
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <strong>Phone:</strong> '.$submission['phone'].'
                            </div>
                            <div class="col-md-6">
                                <strong>Employer Detail:</strong> '.$submission['employer_detail'].'
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <strong>Work Authorization:</strong> '.$submission['work_authorization'].'
                            </div>
                            <div class="col-md-6">
                                <strong>Recruiter Rate:</strong> '.$submission['recruiter_rate'].'
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <strong>Last 4 SSN:</strong> '.$submission['last_4_ssn'].'
                            </div>
                            <div class="col-md-6">
                                <strong>Education Detail:</strong> '.$submission['education_details'].'
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <strong>Resume Experience:</strong> '.$submission['resume_experience'].'
                            </div>
                            <div class="col-md-6">
                                <strong>Linkedin Id:</strong> '.$submission['linkedin_id'].'
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <strong>Vendor Rate:</strong> '.$submission['vendor_rate'].'
                            </div>
                            <div class="col-md-6">
                                <strong>Resume:</strong><a href="'.asset('storage/'.$submission['documents']).'" target="_blank"><img src="'.url('assets/dist/img/resume.png').'" height="50"></a>
                            </div>
                        </div>';
        }
        $data['requirementData'] = $rData;
        $data['candidateData'] = $cData;
        $data['submission'] = $submission;
        $data['candidateStatus'] = $candidateStatus;
        $data['status'] = $status;
        return $data;
    }

    public function candidateUpdate(Request $request){
        $submission = Submission::where('id',$request['submissionId'])->first();
        $requirement = Requirement::where('user_id',Auth::user()->id)->where('id',$submission['requirement_id'])->first();
        if(empty($requirement)){
            \Session::flash('danger', 'You can not update the status');
            return redirect()->route('requirement.index');
        }
        $input = $request->all();
        $submission->update($input);
        \Session::flash('success', 'Candidate status has been updated successfully!');
        return redirect()->back();
    }
}