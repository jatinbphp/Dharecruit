<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Moi;
use App\Models\POCTransfer;
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
            $query = $this->Filter($request,'all');
            return $this->getListHtml($query, $request, 'all_requirement');
        }
        $data['type'] = 1;
        $data['filterFile'] = 'requirement_filter';
        $data['pvCompanyName'] = $this->getPvCompanyName();
        return view('admin.requirement.index', $data);
    }

    public function myRequirement(Request $request){
        $data['menu'] = "My Requirements";
        $data['search'] = $request['search'];

        if ($request->ajax()) {
            $request['authId'] = Auth::user()->id;
            $data = $this->Filter($request);
            return $this->getListHtml($data, $request);
        }
        $data['type'] = 2;
        $data['filterFile'] = 'common_filter';
        $data['pvCompanyName'] = $this->getPvCompanyName();
        return view('admin.requirement.index', $data);
    }

    public function create()
    {
        $data['menu'] = "Requirements";
        $user = $this->getUser();
        if($user['role'] == 'admin'){
            // $data['category'] = Category::where('status','active')->pluck('name','id')->prepend('Please Select','');
            // $data['moi'] = Moi::where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
            $data['pv_company'] = PVCompany::where('status','active')->pluck('name','id')->prepend('Please Select','');
            // $data['visa'] = Visa::where('status','active')->pluck('name','id')->prepend('Please Select','');
        }else{
            // $data['category'] = Category::where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
            // $data['moi'] = Moi::where('user_id',Auth::user()->id)->where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
            $data['pv_company'] = PVCompany::where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
            // $data['visa'] = Visa::where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
        }
        $data['recruiter'] = Admin::where('role','recruiter')->pluck('name','id');
        $data['pvCompanyName'] = $this->getPvCompanyName();
        $data['category'] = Category::where('status','active')->pluck('name','id')->prepend('Please Select','');
        $data['moi'] = Moi::where('status','active')->pluck('name','id');
        $data['visa'] = Visa::where('status','active')->pluck('name','id');

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
            //'client' => 'required',
            'vendor_rate' => 'required',
            'my_rate' => 'required',
            //'priority' => 'required',
            'term' => 'required',
            'category' => 'required',
            'moi' => 'required',
            'job_keyword' => 'required',
            'description' => 'required',
            //'document' => 'required',
            'pv_company_name' => 'required',
            'poc_name' => 'required',
            'poc_email' => 'required',
            'poc_phone_number' => 'required',
            //'client_name' => 'required',
        ]);

        $input = $request->all();
        $input['user_id'] = Auth::user()->id;
        $input['job_id'] = 0;
        unset($input['recruiter']);
        unset($input['visa']);
        unset($input['moi']);
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
                $recruiter['recruiter'] = $this->getCommaSeperatedValues($request['recruiter']);
                $requirements->update($recruiter);

                if($requirements->id){
                    $this->assignRecruiterToRequirement($requirements->id, $request['recruiter']);
                }

            }

            if(!empty($request['visa']) && $requirements){
                $visa['visa'] = $this->getCommaSeperatedValues($request['visa']);
                $requirements->update($visa);
            }

            if(!empty($request['moi']) && $requirements){
                $moi['moi'] = $this->getCommaSeperatedValues($request['moi']);
                $requirements->update($moi);
            }

            $this->addPvCompanyDetails($req);
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
        $requirement = Requirement::where('id',$id)->first();
        $data['requirement'] = $requirement;
        $user = $this->getUser();
        if($user['role'] == 'admin'){
            //$data['category'] = Category::where('status','active')->pluck('name','id')->prepend('Please Select','');
            //$data['moi'] = Moi::where('status','active')->pluck('name','id')->prepend('Please Select','');
            $data['pv_company'] = PVCompany::where('status','active')->pluck('name','id')->prepend('Please Select','');
            //$data['visa'] = Visa::where('status','active')->pluck('name','id')->prepend('Please Select','');
        }else{
            if($user['id'] != $data['requirement']['user_id']){
                \Session::flash('danger',"You can not update other's requirement.");
                return redirect(route('requirement.index'));
            }

            //$data['category'] = Category::where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
            //$data['moi'] = Moi::where('user_id',Auth::user()->id)->where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
            $data['pv_company'] = PVCompany::where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
            //$data['visa'] = Visa::where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
        }
        $data['category'] = Category::where('status','active')->pluck('name','id')->prepend('Please Select','');
        $data['moi'] = Moi::where('status','active')->pluck('name','id');
        $data['visa'] = Visa::where('status','active')->pluck('name','id');
        $data['requirementDocuments'] = RequirementDocuments::where('requirement_id',$id)->pluck('document','id');
        $data['recruiter'] = Admin::where('role','recruiter')->pluck('name','id');
        $data['selectedRecruiter'] = !empty($data['requirement']) && !empty($data['requirement']['recruiter']) ? explode(',',$data['requirement']['recruiter']) : [];
        $data['selectedVisa'] = !empty($data['requirement']) && !empty($data['requirement']['visa']) ? explode(',',$data['requirement']['visa']) : [];
        $data['selectedMoi'] = !empty($data['requirement']) && !empty($data['requirement']['moi']) ? explode(',',$data['requirement']['moi']) : [];
        $data['pvCompanyName'] = $this->getPvCompanyName();

        $data = $this->getLinkingPocDetail($data, $requirement->poc_email);

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
            //'client_name' => 'required',
        ]);

        $requirement = Requirement::where('id',$id)->first();
        $input = $request->all();
        $oldData = $requirement->toArray();

        $differentValueKeys = [];
        foreach ($input as $key => $value) {
            if(is_array($value)){
                $value = ','. implode(',',$value).',';
            }
            if($value == 'on'){
                $value = '1';
            }elseif($value == 'off'){
                $value = '0';
            }
            if (isset($oldData[$key]) && $oldData[$key] != $value) {
                $differentValueKeys[] = $key;
            }
        }

        unset($input['recruiter']);
        unset($input['visa']);
        unset($input['moi']);
        if(isset($input['display_client']) && $input['display_client'] == 'on'){
            $input['display_client'] = 1;
        } else {
            $input['display_client'] = 0;
        }
        $input['is_update_requirement'] = 1;
        $input['is_show_recruiter_after_update'] = null;
        $input['updated_fileds'] = implode(',',$differentValueKeys);
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
                $recruiter['recruiter'] = $this->getCommaSeperatedValues($request['recruiter']);
                $requirement->update($recruiter);

                if($requirement->id){
                    $this->assignRecruiterToRequirement($requirement->id, $request['recruiter']);
                }
            } else {
                $recruiter['recruiter'] = '';
                $requirement->update($recruiter);

                if($requirement->id){
                    $this->assignRecruiterToRequirement($requirement->id, $request['recruiter']);
                }
            }

            if(!empty($request['visa'])){
                $visa['visa'] = $this->getCommaSeperatedValues($request['visa']);
                $requirement->update($visa);
            }

            if(!empty($request['moi'])){
                $moi['moi'] = $this->getCommaSeperatedValues($request['moi']);
                $requirement->update($moi);
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
            $input['bdm_status_updated_at'] = \Carbon\Carbon::now();
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
        if(empty($request->candidatesubmissionId)){
            \Session::flash('danger', 'You can not update the status');
            return redirect()->back();
        }
        $submission = Submission::where('id',$request['candidatesubmissionId'])->first();
        $requirement = Requirement::where('user_id',Auth::user()->id)->where('id',$submission['requirement_id'])->first();
        if(empty($requirement)){
            \Session::flash('danger', 'You can not update the status');
            return redirect()->route('requirement.index');
        }
        $input = $request->all();

        $bdmStatusUpdate = 0;
        if(isset($input['status']) && $input['status'] != $submission->status) {
            $bdmStatusUpdate = 1;
            $input['bdm_status_updated_at'] = \Carbon\Carbon::now();

            $inputData['submission_id']  = $submission->id;
            $inputData['requirement_id'] = $submission->requirement_id;
            $inputData['entity_type']    = EntityHistory::ENTITY_TYPE_BDM_STATUS;
            $inputData['entity_value']   = $submission->status;

            EntityHistory::create($inputData);
        }

        $pvStatusUpdate = 0;
        if(isset($input['pv_status']) && $input['pv_status'] != $submission->pv_status) {
            $pvStatusUpdate = 1;
            $input['pv_status_updated_at'] = \Carbon\Carbon::now();

            $inputData['submission_id']  = $submission->id;
            $inputData['requirement_id'] = $submission->requirement_id;
            $inputData['entity_type']    = EntityHistory::ENTITY_TYPE_PV_STATUS;
            $inputData['entity_value']   = $submission->pv_status;

            EntityHistory::create($inputData);
        }

        if(isset($input['status']) && $input['status'] != 'rejected'){
            $input['reason'] = '';
        }

        if(isset($input['pv_status']) && !in_array($input['pv_status'], ['rejected_by_pv', 'rejected_by_end_client'])){
            $input['pv_reason'] = '';
        }

        $submission->update($input);

        if($bdmStatusUpdate){
            $statusText = Submission::$status;
            $submissionData = Submission::with('Requirement')
                ->with('Requirement.BDM')
                ->with('Recruiters')
                ->where('id',$request['candidatesubmissionId'])
                ->first();

            $submissionData->status_text = isset($statusText[$input['status']]) ? $statusText[$input['status']] : '';
            $submissionData->status_type = 'bdm_status';
            $this->createDataForSentMail($submissionData, 'submission');
        }

        if($pvStatusUpdate) {
            $pvStatusText = Submission::$pvStatus;
            $submissionData = Submission::with('Requirement')
                ->with('Requirement.BDM')
                ->with('Recruiters')
                ->where('id',$request['candidatesubmissionId'])
                ->first();
            $submissionData->status_text = isset($pvStatusText[$input['pv_status']]) ? $pvStatusText[$input['pv_status']] : '';
            $submissionData->status_type = 'pv_status';
            $this->createDataForSentMail($submissionData, 'submission');
        }

        \Session::flash('success', 'Candidate status has been updated successfully!');
        return redirect()->back();
    }

    public function get_pocName(Request $request){
        if(Auth::user()->role == 'admin'){
            $allReqs = PVCompany::where('name',$request['pv_company_name'])->whereNotNull('name')->groupBy('poc_name')->select('poc_name','id')->get();
        }else{
            $allReqs = PVCompany::where('name',$request['pv_company_name'])->where('user_id',Auth::user()->id)->whereNotNull('name')->groupBy('poc_name')->select('poc_name','id')->get();
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
        $requs = PvCompany::orderBy('id', 'DESC')->where('name',$request['pv_company_name'])->where('poc_name',$request['poc_name'])->first();
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

    public function getCommaSeperatedValues($data) {
        return ','.implode(',',$data).',';
    }

    public function repostRequirement($id){
        $data['menu'] = "Requirements";
        $requirement = Requirement::where('id',$id)->first();
        $data['requirement'] = $requirement;
        $user = $this->getUser();
        if($user['role'] == 'admin'){
            $data['pv_company'] = PVCompany::where('status','active')->pluck('name','id')->prepend('Please Select','');
        }else{
            $data['pv_company'] = PVCompany::where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
        }
        $data['category'] = Category::where('status','active')->pluck('name','id')->prepend('Please Select','');
        $data['moi'] = Moi::where('status','active')->pluck('name','id');
        $data['visa'] = Visa::where('status','active')->pluck('name','id');
        $data['requirementDocuments'] = RequirementDocuments::where('requirement_id',$id)->pluck('document','id');
        $data['recruiter'] = Admin::where('role','recruiter')->pluck('name','id');
        $data['selectedRecruiter'] = !empty($data['requirement']) && !empty($data['requirement']['recruiter']) ? explode(',',$data['requirement']['recruiter']) : [];
        $data['selectedVisa'] = !empty($data['requirement']) && !empty($data['requirement']['visa']) ? explode(',',$data['requirement']['visa']) : [];
        $data['selectedMoi'] = !empty($data['requirement']) && !empty($data['requirement']['moi']) ? explode(',',$data['requirement']['moi']) : [];
        $data['pvCompanyName'] = $this->getPvCompanyName();

        $data = $this->getLinkingPocDetail($data, $requirement->poc_email);

        return view('admin.requirement.repost',$data);
    }

    public function saveRepostRequirement(Request $request, $id){
        if(empty($request) || !$id){
            \Session::flash('danger', 'Invalid Request.');
            return redirect()->route('requirement.index');
        }

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
            //'priority' => 'required',
            'term' => 'required',
            'category' => 'required',
            'moi' => 'required',
            'job_keyword' => 'required',
            'description' => 'required',
            //'document' => 'required',
            'pv_company_name' => 'required',
            'poc_name' => 'required',
            'poc_email' => 'required',
            'poc_phone_number' => 'required',
            //'client_name' => 'required',
        ]);

        $requirementRow = Requirement::findOrFail($id);

        if(empty($requirementRow)){
            \Session::flash('danger', 'Invalid Request.');
            return redirect()->route('requirement.index');
        }

        $input = $request->all();
        if($requirementRow->parent_requirement_id == 0) {
            $requirementRow->parent_requirement_id = $id;
            $input['parent_requirement_id'] = $id;
            $requirementRow->save();
        }else{
            $input['parent_requirement_id'] = $requirementRow->parent_requirement_id;
        }

        $input['user_id'] = Auth::user()->id;
        $input['job_id'] = 0;
        unset($input['recruiter']);
        unset($input['visa']);
        unset($input['moi']);
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
                $recruiter['recruiter'] = $this->getCommaSeperatedValues($request['recruiter']);
                $requirements->update($recruiter);

                if($requirements->id){
                    $this->assignRecruiterToRequirement($requirements->id, $request['recruiter']);
                }
            }

            if(!empty($request['visa']) && $requirements){
                $visa['visa'] = $this->getCommaSeperatedValues($request['visa']);
                $requirements->update($visa);
            }

            if(!empty($request['moi']) && $requirements){
                $moi['moi'] = $this->getCommaSeperatedValues($request['moi']);
                $requirements->update($moi);
            }
        }

        \Session::flash('success', 'Requirement has been reposted successfully!');
        return redirect()->route('requirement.index');
    }

    public function addPvCompanyDetails($requirement){
        if(!$requirement){
            return $this;
        }

        $pvCompanyName = $requirement->pv_company_name;
        $pocName       = $requirement->poc_name;
        $email         = $requirement->poc_email;
        $phone         = $requirement->poc_phone_number;
        $pocLocation   = $requirement->poc_location;
        $pvCompanyLocation = $requirement->pv_company_location;
        $clientName    = $requirement->client_name;
        $userId        = Auth::user()->id;

        $oldPvCompanyData = PVCompany::where('email',$requirement->poc_email)
            ->where('name', $pvCompanyName)
            ->where('poc_name', $pocName)
            ->first();

        if($oldPvCompanyData){
            return $this;
        }

        PVCompany::create(
            [
                'user_id'             => $userId,
                'assigned_user_id'    => $userId,
                'name'                => $pvCompanyName,
                'poc_name'            => $pocName,
                'email'               => $email,
                'phone'               => $phone,
                'poc_location'        => $pocLocation,
                'pv_company_location' => $pvCompanyLocation,
                'client_name'         => $clientName,
                'status'              => 'active',
            ]
        );
        return $this;
    }

    public function checkPocEmailData(Request $request){
        if(empty($request->poc_email)){
            $data['status'] = 0;
            return $data;
        }

        $logggedInUserId = Auth::user()->id;
        $pocEmail = $request->poc_email;
        $currentUserPocEmail = PVCompany::where(function ($query) use ($pocEmail) {
            $query->where('email', '=', $pocEmail)
                  ->orWhere('linked_data', 'like', '%'.$pocEmail.'%');
            })->where('assigned_user_id', $logggedInUserId)->first();

        if(!empty($currentUserPocEmail)){
            $data['status'] = 1;
            $data['is_current_user_email'] = 1;
            $data['pvcompany'] = $currentUserPocEmail;
            $data['linking_data'] = $this->getLinkingPocDetail([], $currentUserPocEmail->email);
            return $data;
        }

        $otherUserPocEmail = PVCompany::where(function ($query) use ($pocEmail) {
            $query->where('email', '=', $pocEmail)
                  ->orWhere('linked_data', 'like', '%'.$pocEmail.'%');
            })->where('assigned_user_id','!=', $logggedInUserId)->first();

        if(!empty($otherUserPocEmail)){
            $defaultDays = 14;
            $settingRow =  \App\Models\Setting::where('name', 'transfer_poc_if_req_not_post_days')->first();

            if(!empty($settingRow) && $settingRow->value){
                $defaultDays = $settingRow->value;
            }
            $previousDate = \Carbon\Carbon::now()->subDays($defaultDays);
            $requirement = Requirement::where('poc_email', $otherUserPocEmail->email)->where('created_at', '>=', $previousDate)->first();
            $data['status'] = 1;
                $data['pvcompany'] = $otherUserPocEmail;
                $data['linking_data'] = $this->getLinkingPocDetail([], $otherUserPocEmail->email);
            if($requirement){
                $data['poc_registered'] = 1;
                $data['message'] = "<ul class='list-group list-group-flush text-left'>
                                        <li class='list-group-item'><i class='fa fa-hand-point-right mr-2'></i>1 or more Original Requirement are posted from this POC in the past ".$defaultDays." days.</li>
                                        <li class='list-group-item'><i class='fa fa-hand-point-right mr-2'></i>This Account is currently Registered to BDM: <span class='font-weight-bold'>".Admin::getUserNameBasedOnId($otherUserPocEmail->assigned_user_id)."</span>.</li>
                                        <li class='list-group-item'><i class='fa fa-hand-point-right mr-2'></i>Use the POC Email Filter to manually search for the same requirement from this POC on All Requirement Page. If same job is not posted,</li>
                                        <li class='list-group-item'><i class='fa fa-hand-point-right mr-2'></i>Request manager for a Key Transfer or request BDM: <span class='font-weight-bold'>".Admin::getUserNameBasedOnId($otherUserPocEmail->assigned_user_id)."</span> to use Self Transfer under PV Data & Transfer Page to transfer this POC to you. Once Transfer is complete, you will be able to post</li>
                                </ul>";
            } else {
                $data['poc_can_transfer'] = 1;
                $data['message'] = 'There are 0 Original Requirements posted from this POC in the past '. $defaultDays .' days.
                                    Click Yes to Post requirement and Automatically Transfer this POC to you';
            }
            return $data;
        }

        $matches = [];
        $domain = (preg_match('/@(.+?)\./', $pocEmail, $matches) && isset($matches[1])) ? $matches[1] : null;

        if($domain){
            $isMatchWithDomain = PVCompany::where(function ($query) use ($domain) {
                $query->where('email', 'like', '%@'. $domain .'.%')
                    ->orWhere('linked_data', 'like', '%@'. $domain .'.%');
            })->orderBy('id', 'desc')->first();

            if(!empty($isMatchWithDomain)){
                $data['status'] = 1;
                $data['same_pv_company'] = 1;
                $data['pv_company_name'] = $isMatchWithDomain->name;
                return $data;
            }
        }

        $data['status'] = 1;
        $data['new_poc_email'] = 1;

        return $data;
    }

    public function savePocLinkingData(Request $request){
        if(empty($request->email)){
            $data['status'] = 0;
            return $data;
        }

        $email = $request->email;
        $type = $request->type;

        $existingRow = PvCompany::where('email', $request->poc_email)->first();

        if(!empty($existingRow)){
            $data['is_found'] = 1;
            $data['message'] = "Contact admin to merge vendor contact,  Email already exist for another contact!";
            return $data;
        }

        $oldData = PVCompany::where('email', $email)->first();

        if(empty($oldData)){
            $data['status'] = 0;
            return $data;
        }

        $linkedData = ($oldData->linked_data) ? json_decode($oldData->linked_data, 1) : [];

        $value = '';
        $linkType = '';
        $parentDiv = '';

        if($type == 'linking_email'){
            $value = $request->poc_email;
            $linkType = 'POC Email';
            $parentDiv = 'linkPocEmail';
        } elseif($type == 'linking_poc_phone'){
            $value = $request->poc_phone;
            $linkType = 'POC Phone Number';
            $parentDiv = 'linkPocPhoneNumber';
        }elseif($type == 'linking_location'){
            $value = $request->poc_location;
            $linkType = 'POC Location';
            $parentDiv = 'linkPocLocation';
        }elseif($type == 'linking_pv_location'){
            $value = $request->pc_company_location;
            $linkType = 'PV Company Location';
            $parentDiv = 'linkPvCompanyLocation';
        }

        $found = 0;

        if($oldData && $oldData->linked_data){
            $linkedData = json_decode($oldData->linked_data, 1);
            foreach ($linkedData as $key => $linkValue) {
                if($key != $type){
                    continue;
                }
                foreach($linkValue as $values){
                    $existingValue = $values['value'];
                    if(strtolower($existingValue) == strtolower($value)){
                        $found = 1;
                    }
                }
            }
        }

        if($found){
            $data['is_found'] = 1;
            $data['message'] = "Contact admin to merge vendor contact,  $linkType already exist for another contact!";
            return $data;
        }

        $linkedValuesdata['value']    = $value;
        $linkedValuesdata['user_id']  = Auth::user()->id;;
        $linkedValuesdata['dateTime'] = \Carbon\Carbon::now();

        $linkedData[$type][] = $linkedValuesdata;

        PVCompany::where('email', $email)->update(['linked_data' => json_encode($linkedData)]);

        $data['status'] = 1;
        $data['user_name'] = Admin::getUserNameBasedOnId(Auth::user()->id);
        $data['date']  = date('m-d-y', strtotime(\Carbon\Carbon::now()));
        $data['value'] = $value;
        $data['parent_div'] = $parentDiv;
        return $data;
    }

    public function getLinkingPocDetail($data, $pocEmail){
        if(!$pocEmail){
            return [];
        }
        $linkPocEmail = '';
        $linkPocPhoneNumber = '';
        $linkPocLocation = '';
        $linkPvCompanyLocation = '';

        $ulStartData = '<ul class="list-group mt-3">';
        $ulEndData = '</ul>';

        $pvCompanyData = PVCompany::where('email',$pocEmail)->first();

        if($pvCompanyData && $pvCompanyData->linked_data){
            $linkedData = json_decode($pvCompanyData->linked_data, 1);
            foreach ($linkedData as $key => $linkValue) {
                foreach($linkValue as $values){
                    if($key == 'linking_email'){
                        $linkPocEmail .= ' <li class="list-group-item p-1"><span class="text-primary">'.$values['value'].'</span> ( '.Admin::getUserNameBasedOnId($values['user_id']).' : '.date('m-d-y', strtotime($values['dateTime'])).' ) </li>';
                    }
                    if($key == 'linking_poc_phone'){
                        $linkPocPhoneNumber .= ' <li class="list-group-item p-1"><span class="text-primary">'.$values['value'].'</span> ( '.Admin::getUserNameBasedOnId($values['user_id']).' : '.date('m-d-y', strtotime($values['dateTime'])).' ) </li>';
                    }
                    if($key == 'linking_location'){
                        $linkPocLocation .= ' <li class="list-group-item p-1"><span class="text-primary">'.$values['value'].'</span> ( '.Admin::getUserNameBasedOnId($values['user_id']).' : '.date('m-d-y', strtotime($values['dateTime'])).' ) </li>';
                    }
                    if($key == 'linking_pv_location'){
                        $linkPvCompanyLocation .= ' <li class="list-group-item p-1"><span class="text-primary">'.$values['value'].'</span> ( '.Admin::getUserNameBasedOnId($values['user_id']).' : '.date('m-d-y', strtotime($values['dateTime'])).' ) </li>';
                    }
                }
            }
        }

        $data['linkPocEmail'] = "<div id='linkPocEmail'> $ulStartData  $linkPocEmail  $ulEndData </div>";
        $data['linkPocPhoneNumber'] = "<div id='linkPocPhoneNumber'> $ulStartData  $linkPocPhoneNumber  $ulEndData </div>";
        $data['linkPocLocation'] = "<div id='linkPocLocation'> $ulStartData  $linkPocLocation  $ulEndData </div>";
        $data['linkPvCompanyLocation'] = "<div id='linkPvCompanyLocation'> $ulStartData  $linkPvCompanyLocation  $ulEndData </div>";

        return $data;
    }
    public function checkPoc(Request $request)
    {
        $status = 0;
        if(!empty($request->poc_name) && !empty($request->pv_company_name)){
            $exists = PVCompany::where('poc_name','like', '%'.$request->poc_name.'%')->where('name', $request->pv_company_name)->exists();
            if($exists){
                $status = 1;
            }
        }
        $data['status'] = $status;
        return  $data;
    }

    public function transferPoc(Request $request)
    {
        $status = 0;
        if(!empty($request->poc_email) && Auth::user()->role == 'bdm'){
            $pvCompanyRow = PVCompany::where('email', $request->poc_email)->first();
            if(!empty($pvCompanyRow)){

                $this->transferPocData($pvCompanyRow, $this->getCurrentUserId(), POCTransfer::TRANSFER_TYPE_AUTOMATIC);

                $status = 1;
                $data['pvcompany'] = $pvCompanyRow;
                $data['linking_data'] = $this->getLinkingPocDetail([], $pvCompanyRow->email);
            }
        }
        $data['status'] = $status;
        return $data;
    }

    public function checkTransferKey(Request $request)
    {
        $status = 0;
        if(!empty($request->poc_email) && !empty($request->transfer_key)){
            $userDataRow = Admin::where('transfer_key', $request->transfer_key)->first();
            if(!empty($userDataRow)){
                $pvCompanyRow = PVCompany::where('email', $request->poc_email)->first();
                if(!empty($pvCompanyRow)){
                    $this->transferPocData($pvCompanyRow, $this->getCurrentUserId(), POCTransfer::TRANSFER_TYPE_KEY);

                    $pvCompanyRow->used_key = $userDataRow->transfer_key;
                    $pvCompanyRow->used_key_owner = $userDataRow->id;
                    $pvCompanyRow->save();
                    $status = 1;
                }
            }
        }
        $data['status'] = $status;
        return $data;
    }
}
