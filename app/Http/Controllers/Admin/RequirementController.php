<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Moi;
use App\Models\PVCompany;
use App\Models\Requirement;
use App\Models\Submission;
use App\Models\RequirementDocuments;
use App\Models\EntityHistory;
use App\Models\Visa;
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
            return getListHtml($data);
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
            return getListHtml($data);
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
            $data['visa'] = Visa::where('status','active')->pluck('name','id')->prepend('Please Select','');
        }else{
            $data['category'] = Category::where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
            $data['moi'] = Moi::where('user_id',Auth::user()->id)->where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
            $data['pv_company'] = PVCompany::where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
            $data['visa'] = Visa::where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
        }
        $data['recruiter'] = Admin::where('role','recruiter')->pluck('name','id');
        return view("admin.requirement.create",$data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            /*'job_title' => 'required',
            'no_of_position' => 'required',
            'experience' => 'required',
            'location' => 'required',
            'work_type' => 'required',
            'duration' => 'required',
            'visa' => 'required',
            //'client' => 'required',
            'vendor_rate' => 'required',
            'my_rate' => 'required',
            //'priority' => 'required',
            'term' => 'required',
            'category' => 'required',
            'moi' => 'required',
            'job_keyword' => 'required',
            'description' => 'required',
            'document' => 'required',*/
            'pv_company_name' => 'required',
            'poc_name' => 'required',
            'poc_email' => 'required',
            'poc_phone_number' => 'required',
            'client_name' => 'required',
        ]);

        $input = $request->all();
        $input['user_id'] = Auth::user()->id;
        $input['job_id'] = 0;
        unset($input['recruiter']);
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

            if(!empty($request['document'])){
                if($files = $request->file('document')){
                    foreach ($files as $file) {
                        $documentData['requirement_id'] = $req['id'];
                        $documentData['document'] = $this->fileMove($file,'user_documents');
                        RequirementDocuments::create($documentData);
                    }
                }
            }

            if(!empty($request['recruiter']) && $requirements){
                $recruiter['recruiter'] = $this->getAllRecruiter($request['recruiter']);
                $requirements->update($recruiter);
            }
        }

        \Session::flash('success', 'Requirement has been inserted successfully!');
        return redirect(route('requirement.edit',['requirement'=>$req['id']]));
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
            $data['visa'] = Visa::where('status','active')->pluck('name','id')->prepend('Please Select','');
        }else{
            if($user['id'] != $data['requirement']['user_id']){
                \Session::flash('danger',"You can not update other's requirement.");
                return redirect(route('requirement.index'));
            }

            $data['category'] = Category::where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
            $data['moi'] = Moi::where('user_id',Auth::user()->id)->where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
            $data['pv_company'] = PVCompany::where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
            $data['visa'] = Visa::where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
        }
        $data['requirementDocuments'] = RequirementDocuments::where('requirement_id',$id)->pluck('document','id');
        $data['recruiter'] = Admin::where('role','recruiter')->pluck('name','id');
        $data['selectedRecruiter'] = !empty($data['requirement']) && !empty($data['requirement']['recruiter']) ? explode(',',$data['requirement']['recruiter']) : [];
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
            //'client' => 'required',
            'vendor_rate' => 'required',
            'my_rate' => 'required',
            // 'priority' => 'required',
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
        unset($input['recruiter']);
        if(isset($input['display_client']) && $input['display_client'] == 'on'){
            $input['display_client'] = 1;
        } else {
            $input['display_client'] = 0;
        }
        $requirement = Requirement::where('id',$id)->first();
        $requirement->update($input);

        if($requirement){
            if(!empty($request['document'])){
                if($files = $request->file('document')){
                    foreach ($files as $file) {
                        $documentData['requirement_id'] = $requirement['id'];
                        $documentData['document'] = $this->fileMove($file,'user_documents');
                        RequirementDocuments::create($documentData);
                    }
                }
            }

            if(!empty($request['recruiter'])){
                $recruiter['recruiter'] = $this->getAllRecruiter($request['recruiter']);
                $requirement->update($recruiter);
            }

        }

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

            $inputData['submission_id']  = $id;
            $inputData['requirement_id'] = $submission->requirement_id;
            $inputData['entity_type']    = EntityHistory::ENTITY_TYPE_BDM_STATUS;
            $inputData['entity_value']   = $request->status;

            EntityHistory::create($inputData);

            return 1;
        }else{
           return 0;
        }
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

        $inputData['submission_id']  = $submission->id;
        $inputData['requirement_id'] = $submission->requirement_id;
        $inputData['entity_type']    = EntityHistory::ENTITY_TYPE_BDM_STATUS;
        $inputData['entity_value']   = $submission->status;

        EntityHistory::create($inputData);

        \Session::flash('success', 'Candidate status has been updated successfully!');
        return redirect()->back();
    }

    public function get_pocName(Request $request){
        if(Auth::user()->role == 'bdm'){
            $allReqs = Requirement::where('pv_company_name',$request['pv_company_name'])->where('user_id',Auth::user()->id)->whereNotNull('pv_company_name')->groupBy('poc_name')->select('poc_name','id')->get();
        }else{
            $allReqs = Requirement::where('pv_company_name',$request['pv_company_name'])->whereNotNull('pv_company_name')->groupBy('poc_name')->select('poc_name','id')->get();
        }
        $data['status'] = 0;
        $data['pocName'] = '';
        if(count($allReqs) > 0){
            $data['status'] = 1;
            $option = '<option value="">Please Select POC Name</option>';
            foreach ($allReqs as $list){
                $option .= '<option value="'.$list['poc_name'].'" data-id="'.$list['id'].'">'.$list['poc_name'].'</option>';
            }
            $option .= '<option value="0">Add New POC</option>';
            $data['pocName'] .= ' <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label col-md-12" for="pocName">POC Name</label>
                                            <select class="form-control select2 col-md-12" id="pocSelection" style="width: 100%" onChange="checkData(event)">
                                                '.$option.'
                                            </select>
                                        </div>
                                    </div>';
        }
        return $data;
    }

    public function get_pvDetails(Request $request){
        $requs = Requirement::orderBy('id', 'DESC')->where('pv_company_name',$request['pv_company_name'])->where('poc_name',$request['poc_name'])->first();
        $data['status'] = 0;
        $data['requs'] = [];
        if(!empty($requs)){
            $data['status'] = 1;
            $data['requs'] = $requs;
        }
        return $data;
    }

    public function removeDocument($id) {
        $data = [];
        if(!$id){
            $data['status'] = 0;
            return $data;
        }
        RequirementDocuments::where('id', $id)->delete();
        $data['status'] = 1;

        return $data;
    }

    public function getAllRecruiter($recruiters) {
        return ','.implode(',',$recruiters).',';
    }
}
