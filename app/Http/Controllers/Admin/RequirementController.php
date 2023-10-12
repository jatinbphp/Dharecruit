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
            $data = Requirement::select();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('user_id', function($row){
                    return $row->BDM->name;
                })
                ->addColumn('category', function($row){
                    return $row->Category->name;
                })
                ->addColumn('created_at', function($row){
                    return date('d/m/Y', strtotime($row->created_at));
                })
                ->addColumn('recruiter', function($row){
                    $rId = !empty($row->recruiter) ? explode(',',$row->recruiter) : [];
                    $recName = '';
                    if(count($rId)>0){
                        foreach ($rId as $uid){
                            $recUser = Admin::where('id',$uid)->first();
                            if(!empty($recUser)){
                                $submission = Submission::where('user_id',$uid)->where('requirement_id',$row->id)->count();
                                $recName .= $submission.'-'.$recUser['name'].'<br>';
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
                                                    <button class="btn btn-success assign ladda-button" data-style="slide-left" id="assign" uid="'.$row->id.'" url="'.route('requirement.assign').'" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Unhold</span></button>
                                                </div>';
                    }
                    if ($row->status == "unhold") {
                        $statusBtn .= '<div class="btn-group-horizontal" id="assign_add_"'.$row->id.'">
                                                    <button class="btn btn-success assign ladda-button" id="assign" data-style="slide-left" uid="'.$row->id.'" url="'.route('requirement.assign').'" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Unhold</span></button>
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
                    $candidate = '';
                    if(count($allSubmission) > 0){
                        foreach ($allSubmission as $list){
                            $candidate .= $list['name'].'-'.$list['id'].'<br>';
                        }
                    }
                    return $candidate;
                })
                ->addColumn('action', function($row){
                    $user = Auth::user();
                    $btn = '';
                    if($user['role'] == 'bdm' && $user['id'] == $row->user_id){
                        $btn .= '<div class="btn-group btn-group-sm mr-2"><a href="'.url('admin/requirement/'.$row->id.'/edit').'"><button class="btn btn-sm btn-info tip" data-toggle="tooltip" title="Edit Requirement" data-trigger="hover" type="submit" ><i class="fa fa-edit"></i></button></a></div>';
                    }
                    if($user['role'] == 'admin'){
                        $btn .= '<span data-toggle="tooltip" title="Delete Requirement" data-trigger="hover">
                                    <button class="btn btn-sm btn-danger deleteRequirement mr-2" data-id="'.$row->id.'" type="button"><i class="fa fa-trash"></i></button>
                                </span>';
                    }
                    $btn .= '<div class="btn-group btn-group-sm"><a href="'.url('admin/requirement/'.$row->id).'"><button class="btn btn-sm btn-warning tip" data-toggle="tooltip" title="View Submission" data-trigger="hover" type="submit" ><i class="fa fa-eye"></i></button></a></div>';
                    return $btn;
                })
                ->rawColumns(['user_id','category','created_at','recruiter','status','color','candidate','action'])
                ->make(true);
        }
        $data['type'] = 1;
        return view('admin.requirement.index', $data);
    }

    public function my_requirement(Request $request){
        $data['menu'] = "My Requirements";
        $data['search'] = $request['search'];

        if ($request->ajax()) {
            $data = Requirement::where('user_id',Auth::user()->id)->select();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('user_id', function($row){
                    return $row->BDM->name;
                })
                ->addColumn('category', function($row){
                    return $row->Category->name;
                })
                ->addColumn('created_at', function($row){
                    return date('d/m/Y', strtotime($row->created_at));
                })
                ->addColumn('recruiter', function($row){
                    $rId = !empty($row->recruiter) ? explode(',',$row->recruiter) : [];
                    $recName = '';
                    if(count($rId)>0){
                        foreach ($rId as $uid){
                            $recUser = Admin::where('id',$uid)->first();
                            if(!empty($recUser)){
                                $submission = Submission::where('user_id',$uid)->where('requirement_id',$row->id)->count();
                                $recName .= $submission.'-'.$recUser['name'].'<br>';
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
                                                    <button class="btn btn-success assign ladda-button" data-style="slide-left" id="assign" uid="'.$row->id.'" url="'.route('requirement.assign').'" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Unhold</span></button>
                                                </div>';
                    }
                    if ($row->status == "unhold") {
                        $statusBtn .= '<div class="btn-group-horizontal" id="assign_add_"'.$row->id.'">
                                                    <button class="btn btn-success assign ladda-button" id="assign" data-style="slide-left" uid="'.$row->id.'" url="'.route('requirement.assign').'" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Unhold</span></button>
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
                    $candidate = '';
                    if(count($allSubmission) > 0){
                        foreach ($allSubmission as $list){
                            $candidate .= $list['name'].'-'.$list['id'].'<br>';
                        }
                    }
                    return $candidate;
                })
                ->addColumn('action', function($row){
                    $user = Auth::user();
                    $btn = '';
                    if($user['role'] == 'bdm' && $user['id'] == $row->user_id){
                        $btn .= '<div class="btn-group btn-group-sm mr-2"><a href="'.url('admin/requirement/'.$row->id.'/edit').'"><button class="btn btn-sm btn-info tip" data-toggle="tooltip" title="Edit Requirement" data-trigger="hover" type="submit" ><i class="fa fa-edit"></i></button></a></div>';
                    }
                    if($user['role'] == 'admin'){
                        $btn .= '<span data-toggle="tooltip" title="Delete Requirement" data-trigger="hover">
                                    <button class="btn btn-sm btn-danger deleteRequirement mr-2" data-id="'.$row->id.'" type="button"><i class="fa fa-trash"></i></button>
                                </span>';
                    }
                    $btn .= '<div class="btn-group btn-group-sm"><a href="'.url('admin/requirement/'.$row->id).'"><button class="btn btn-sm btn-warning tip" data-toggle="tooltip" title="View Submission" data-trigger="hover" type="submit" ><i class="fa fa-eye"></i></button></a></div>';
                    return $btn;
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
            $data = Submission::where('requirement_id',$id)->select();

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
            $data['moi'] = Moi::where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
            $data['pv_company'] = PVCompany::where('status','active')->pluck('name','id')->prepend('Please Select','');
        }else{
            if($user['id'] != $data['requirement']['user_id']){
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
}
