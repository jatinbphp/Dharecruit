<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use App\Models\Submission;
use App\Models\Requirement;
use App\Models\EntityHistory;
use App\Models\Interview;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BDMSubmissionController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('accessright:manage_bdm_submission');
    }

    public function index(Request $request)
    {
        $data['menu'] = "Manage Submission";
        if ($request->ajax()) {
            $user = Auth::user();
            $requirementIds = [];

//            $submissions = Submission::with(
//                [
//                    'Requirement' => function ($query) {
//                        $query->with(['BDM' => function ($queryBDM) {
//                            $queryBDM->select('id', 'name');
//                        }])->select('id', 'created_at','user_id','job_id', 'job_title', 'location', 'work_type', 'duration', 'visa', 'client', 'my_rate', 'category', 'moi', 'job_keyword', 'pv_company_name', 'poc_name', 'client_name', 'display_client', 'status', 'recruiter', 'is_show_recruiter', 'is_show_recruiter_after_update','is_update_requirement','parent_requirement_id','poc_email');
//                    },
//                    'recruiters' => function ($query) {
//                        $query->select('id', 'name');
//                    }
//                ]
//            )->select('id', 'user_id', 'requirement_id', 'candidate_id', 'name', 'email', 'location', 'recruiter_rate', 'employer_name', 'employee_name', 'employee_email', 'status', 'pv_status', 'created_at', 'is_show');


            $submissions = Submission::select()
                ->join('requirements', 'submissions.requirement_id', '=', 'requirements.id')
                ->leftJoin('admins', 'requirements.user_id', '=', 'admins.id')
                ->leftJoin('admins as recruiter', 'submissions.user_id', '=', 'recruiter.id')
                ->select(
                    'submissions.id',
                    'submissions.user_id',
                    'submissions.requirement_id',
                    'submissions.candidate_id',
                    'submissions.name',
                    'submissions.email',
                    'submissions.location',
                    'submissions.recruiter_rate',
                    'submissions.employer_name',
                    'submissions.employee_name',
                    'submissions.employee_email',
                    'submissions.status',
                    'submissions.pv_status',
                    'submissions.created_at',
                    'submissions.is_show',
                    'submissions.bdm_status_updated_at',
                    'submissions.pv_status_updated_at',
                    'submissions.interview_status_updated_at',
                    'requirements.id as requirement_id',
                    'requirements.created_at as requirement_created_at',
                    'requirements.user_id as requirement_user_id',
                    'requirements.job_id',
                    'requirements.job_title',
                    'requirements.location as requirement_location',
                    'requirements.work_type',
                    'requirements.duration',
                    'requirements.visa',
                    'requirements.client',
                    'requirements.my_rate',
                    'requirements.category',
                    'requirements.moi',
                    'requirements.job_keyword',
                    'requirements.pv_company_name',
                    'requirements.poc_name',
                    'requirements.client_name',
                    'requirements.display_client',
                    'requirements.status as requirement_status',
                    'requirements.recruiter',
                    'requirements.is_show_recruiter',
                    'requirements.is_show_recruiter_after_update',
                    'requirements.is_update_requirement',
                    'requirements.parent_requirement_id',
                    'requirements.poc_email',
                    'admins.name as bdm_name',
                    'recruiter.name as recruiter_name',
                );

            if(!empty($request->fromDate)){
                $fromDate = date('Y-m-d', strtotime($request->fromDate));
                $submissions->where('submissions.created_at', '>=' ,$fromDate." 00:00:00");
            }

            if(!empty($request->toDate)){
                $toDate = date('Y-m-d', strtotime($request->toDate));
                $submissions->where('submissions.created_at', '<=' ,$toDate." 23:59:59");
            }

            if(!empty($request->filter_employer_name)){
                $submissions->where('submissions.employer_name', 'like', '%'.$request->filter_employer_name.'%');
            }

            if(!empty($request->filter_employee_name)){
                $submissions->where('submissions.employee_name', 'like', '%'.$request->filter_employee_name.'%');
            }

            if(!empty($request->filter_employee_phone_number)){
                $submissions->where('submissions.employee_phone', $request->filter_employee_phone_number);
            }

            if(!empty($request->filter_employee_email)){
                $submissions->where('submissions.employee_email', $request->filter_employee_email);
            }

            if(!empty($request->candidate_name)){
                $submissions->where('submissions.name', 'like' , '%'.$request->candidate_name.'%');
            }

            if(!empty($request->candidate_id)){
                $submissions->where('submissions.candidate_id', $request->candidate_id);
            }

            if(!empty($request->bdm_feedback)){
                $bdmFeedBack = $request->bdm_feedback;
                $isOrWhere = 0;
                $isWhere = 0;
                $isStatus = 0;
                if(in_array('no_updates', $bdmFeedBack)){
                    $bdmFeedBack = array_flip($bdmFeedBack);
                    unset($bdmFeedBack['no_updates']);
                    $bdmFeedBack = array_flip($bdmFeedBack);
                    $isWhere = 1;
                }

                if(in_array('no_viewed', $bdmFeedBack)){
                    $bdmFeedBack = array_flip($bdmFeedBack);
                    unset($bdmFeedBack['no_viewed']);
                    $bdmFeedBack = array_flip($bdmFeedBack);

                    if($bdmFeedBack && count($bdmFeedBack)){
                        $isStatus = 1;
                    } else {
                        $isOrWhere = 1;
                    }
                }

                $submissions->where(function ($submissions) use ($isWhere, $isStatus, $isOrWhere, $bdmFeedBack) {
                    if($isWhere == 1){
                        $submissions->whereNull('submissions.pv_status');
                        $submissions->Where('submissions.is_show', 1);
                        $submissions->Where('submissions.status', 'pending');
                        if($isStatus == 0 && $bdmFeedBack && count($bdmFeedBack)){
                            $submissions->orWhereIn('submissions.status', $bdmFeedBack);
                        }
                    }
                    if($isOrWhere == 1){
                        $submissions->orWhere('is_show', 0);
                    }
                    if($isStatus == 1){
                        $submissions->orwhere(function ($submissions) use ($isWhere, $isStatus, $isOrWhere, $bdmFeedBack) {
                            $submissions->whereIn('submissions.status', $bdmFeedBack);
                            $submissions->orWhere('submissions.is_show', 0);
                        });
                    }
                });

                if(!in_array('no_updates', $request->bdm_feedback) && !in_array('no_viewed', $request->bdm_feedback)){
                    $submissions->whereIn('submissions.status', $request->bdm_feedback);
                }
            }

            if(!empty($request->pv_feedback)){
                $submissions->whereIn('submissions.pv_status', $request->pv_feedback);
            }

            if(!empty($request->recruiter)){
                $submissions->where('submissions.user_id',$request->recruiter);
            }

            if(!empty($request->client_feedback)){
                $submissionId = Interview::whereIn('status', $request->client_feedback)->pluck('submission_id')->toArray();
                $requiremrntIdsHavingSubmission = [];
                if($submissionId && count($submissionId)) {
                    $submissionsData = Submission::whereIn('id', $submissionId);
                    if(Auth::user()->role == 'recruiter'){
                        $requiremrntIdsHavingSubmission = $submissions->where('user_id', Auth::user()->id)->pluck('requirement_id')->toArray();
                    } else if(Auth::user()->role == 'bdm') {
                        $bdmRequirementIds = Requirement::where('user_id', Auth::user()->id)->pluck('id')->toArray();

                        if($bdmRequirementIds && count($bdmRequirementIds)){
                            $submissions->whereIn('requirement_id', $bdmRequirementIds);
                        }else{
                            $submissions->where('requirement_id', 0);
                        }

                        $requiremrntIdsHavingSubmission = $submissionsData->pluck('requirement_id')->toArray();
                    } else {
                        if(!empty($request->recruiter)){
                            $requiremrntIdsHavingSubmission = $submissionsData->where('user_id', $request->recruiter)->pluck('requirement_id')->toArray();
                        } else {
                            $requiremrntIdsHavingSubmission = $submissionsData->pluck('requirement_id')->toArray();
                        }
                    }
                }
                $requirementIds[] = $requiremrntIdsHavingSubmission;
            }

            if(!empty($request->pv_email)){
                $pvEmailReqIds = $this->getRequirementIdBasedOnData('poc_email', $request->pv_email);
                $requirementIds[] = $pvEmailReqIds;
            }

            if(!empty($request->pv_company)){
                $pvCompanyReqIds = $this->getRequirementIdBasedOnData('pv_company_name', $request->pv_company, 'like');
                $requirementIds[] = $pvCompanyReqIds;
            }

            if(!empty($request->pv_name)){
                $pvNameReqIds = $this->getRequirementIdBasedOnData('poc_name', $request->pv_name, 'like');
                $requirementIds[] = $pvNameReqIds;
            }

            if(!empty($request->pv_phone)){
                $pvPhoneReqIds = $this->getRequirementIdBasedOnData('poc_phone_number', $request->pv_phone);
                $requirementIds[] = $pvPhoneReqIds;
            }

            if(!empty($request->job_title)){
                $jobTitleReqIds = $this->getRequirementIdBasedOnData('job_title', $request->job_title, 'like');
                $requirementIds[] = $jobTitleReqIds;
            }

            if(!empty($request->bdm)){
                $bdmReqIds = $this->getRequirementIdBasedOnData('user_id', $request->bdm);
                $requirementIds[] = $bdmReqIds;
            }

            if(!empty($request->job_id)){
                $jobIdReqIds = $this->getRequirementIdBasedOnData('job_id', $request->job_id);
                $requirementIds[] = $jobIdReqIds;
            }

            if(!empty($request->client)){
                $clientReqIds = $this->getRequirementIdBasedOnData('client_name', $request->client, 'like');
                $requirementIds[] = $clientReqIds;
            }

            if(!empty($request->job_location)){
                $jobLocationReqIds = $this->getRequirementIdBasedOnData('location', $request->job_location, 'like');
                $requirementIds[] = $jobLocationReqIds;
            }

            if($requirementIds && count($requirementIds)){
                $commonRequirementIds = call_user_func_array('array_intersect', $requirementIds);

                if(Auth::user()->role == 'recruiter'){
                    if($commonRequirementIds && count($commonRequirementIds)){
                        $submissions->whereIn('requirement_id', $commonRequirementIds);
                    } else {
                        $submissions->where('requirement_id', 0);
                    }
                }
            }

            if($user->role == 'recruiter'){
                $submissions->where('submissions.user_id', $user->id);
            }else if($user->role == 'bdm'){
                $loggedinBdmrequirementIds = Requirement::where('user_id', $user->id)->pluck('id')->toArray();
                if($requirementIds && count($requirementIds)){
                    if(isset($commonRequirementIds) && $commonRequirementIds && count($commonRequirementIds)){
                        $allRequirementIds = array_intersect($loggedinBdmrequirementIds, $commonRequirementIds);
                        $submissions->whereIn('requirement_id', $allRequirementIds);
                    } else {
                        $submissions->where('requirement_id', 0);
                    }
                } else {
                    $submissions->whereIn('requirement_id', $loggedinBdmrequirementIds);
                }
            }else{
                if($requirementIds && count($requirementIds)){
                    if(isset($commonRequirementIds) && $commonRequirementIds && count($commonRequirementIds)){
                        $submissions->whereIn('requirement_id', $commonRequirementIds);
                    } else {
                        $submissions->where('requirement_id', 0);
                    }
                }
            }

            return Datatables::of($submissions)
                ->addColumn('candidate_name', function($row){
                    $candidateClass = $this->getCandidateClass($row,true);
                    $candidateCss   = $this->getCandidateCss($row,true);
                    $candidateBorderCss = $this->getCandidateBorderCss($row);
                    $candidateNames = explode(' ',$row->name);
                    $candidateName = isset($candidateNames[0]) ? $candidateNames[0] : '';
                    $candidateCount = $this->getCandidateCountByEmail($row->email);
                    $isCandidateHasLog  = $this->isCandidateHasLog($row);
                    $isEmployerNameChanged = $this->isEmployerNameChanged($row->candidate_id);
                    $timeSpan = $this->getSubmissionTimeSpan($row->Requirement->created_at, $row->created_at);
                    return ($candidateCount ? "<span class='badge bg-indigo position-absolute top-0 start-100 translate-middle'>$candidateCount</span>" : "")
                        . (($isCandidateHasLog) ? "<span class='badge badge-pill badge-primary ml-4 position-absolute top-0 start-100 translate-middle'>L</span>" : "")
                        .(($isEmployerNameChanged) ? "<span class='badge bg-red ml-5'>2 Emp</span>" : "").
                        '<div  class="a-center pt-2 pl-2 pb-2 pr-2 '. $candidateCss.'" style="width: fit-content;">
                            <span class="'.$candidateClass.' candidate candidate-'.$row->id.'" style="'.$candidateBorderCss.'" data-cid="'.$row->id.'">'. $candidateName. '-' .$row->candidate_id. '</span>
                        </div>
                        <div class="p-1 mt-1 border border-dark" style="width: fit-content;">
                            <span class="text-secondary font-weight-bold">'.$timeSpan.'</span>
                        </div>';
                })
                ->addColumn('bdm_status', function($row){
                    $statusLastUpdatedAt = ($row->bdm_status_updated_at) ? strtotime($row->bdm_status_updated_at) : 0;
                    $status = isset(Submission::$status[$row->status]) ? "<p data-order='$statusLastUpdatedAt'>".Submission::$status[$row->status]."</p>" : '';
                    if($row->status){
                        if($row->status == Submission::STATUS_REJECTED){
                            $status .= "<span class='feedback' style='display:none'>".$this->getTooltipHtml($row->reason)."</span>";
                        }
                        $status .= getEntityLastUpdatedAtHtml(EntityHistory::ENTITY_TYPE_BDM_STATUS,$row->id);
                    }
                    return $status;
                })
                ->addColumn('pv_status', function($row){
                    $status = '';
                    $statusLastUpdatedAt = ($row->pv_status_updated_at) ? strtotime($row->pv_status_updated_at) : 0;
                    if($row->status == Submission::STATUS_ACCEPT){
                        $status .= isset(Submission::$pvStatus[$row->pv_status]) ? "<p data-order='$statusLastUpdatedAt'>".Submission::$pvStatus[$row->pv_status]."</p>" : '';
                    }else{
                        $status .= "<p data-order='$statusLastUpdatedAt'></p>";
                    }
                    if($row->pv_status && $row->status == Submission::STATUS_ACCEPT){
                        $status .= "<span class='feedback' style='display:none'>".$this->getTooltipHtml($row->pv_reason)."</span>";
                        $status .= getEntityLastUpdatedAtHtml(EntityHistory::ENTITY_TYPE_PV_STATUS,$row->id);
                    }
                    return $status;
                })
                ->editColumn('created_at', function($row){
                    return date('m/d/y', strtotime($row->created_at));
                })
                ->addColumn('pv', function($row){
                    if(Auth::user()->role != 'admin'){
                        return $row->Requirement->pv_company_name;
                    }
                    $totalPvCount  = $this->getAllPvCompanyCount($row->Requirement->pv_company_name);
                    $isNewPoc      = $this->isNewAsPerConfiguration('pv_company_name', $row->Requirement->pv_company_name);

                    $pocHtml = '<span class="font-weight-bold '.(($isNewPoc) ? "text-primary" : "").'">'.$row->Requirement->pv_company_name;
                    $pocHtml .= '<br><br><span class="border pt-1 pl-1 pr-1 pb-1 '.(($isNewPoc) ? "border-primary" : "border-secondary").'">'.$totalPvCount.'</span></span>';
                    return $pocHtml;

                    // return '<i class="fa fa-eye pv_name-icon pv-name-icon-'.$row->id.'" onclick="showData('.$row->id.',\'pv-name-\')" aria-hidden="true"></i><span class="pv_name pv-name-'.$row->id.'" style="display:none">'.$row->Requirement->pv_company_name.'</span>';
                })
                ->addColumn('poc', function($row){
                    $pocNameArray = explode(' ', $row->Requirement->poc_name);
                    $pocFirstName = isset($pocNameArray[0]) ? $pocNameArray[0] : '';
                    if(Auth::user()->role != 'admin'){
                        return $pocFirstName;
                    }
                    $isNewPoc      = $this->isNewAsPerConfiguration('poc_name', $row->Requirement->poc_name);
                    $totalOrigReqInDays = $this->getTotalOrigReqBasedOnPocData($row->Requirement->poc_name, $row->Requirement->poc_email);

                    return '<div class="container"><p class="'.(($isNewPoc) ? "text-primary" : "").'">'.$row->Requirement->poc_name. (($totalOrigReqInDays) ? "<span class='badge bg-indigo position-absolute top-0 end-0' style='margin-top: -6px'>$totalOrigReqInDays</span>" : "").'</p></div>';
                })
                ->addColumn('r_rate', function($row){
                    return $row->recruiter_rate;
                })
                ->editColumn('employer_name', function($row){
                    if(Auth::user()->role != 'admin'){
                        return '<i class="fa fa-eye show_employer_name-icon employer-name-icon-'.$row->id.'" onclick="showData('.$row->id.',\'employer-name-\')" aria-hidden="true"></i><span class="show_employer_name employer-name-'.$row->id.'" style="display:none">'.$row->employer_name.'</span>';
                    }
                    $employerNameCount = $this->getAllEmpDataCount('employer_name', $row->employer_name);
                    return '<i class="fa fa-eye show_employer_name-icon employer-name-icon-'.$row->id.'" onclick="showData('.$row->id.',\'employer-name-\')" aria-hidden="true"></i><div class="container"><span class="show_employer_name employer-name-'.$row->id.'" style="display:none">'.$row->employer_name.(($employerNameCount) ? "<span class='badge bg-indigo position-absolute top-0 end-0' style='margin-top: -6px'>$employerNameCount</span>" : "").'</span></div>';
                })
                ->editColumn('employee_name', function($row){
                    $empPocNameArray = explode(' ', $row->employee_name);
                    $empPocFirstName = isset($empPocNameArray[0]) ? $empPocNameArray[0] : '';
                    if(Auth::user()->role != 'admin'){
                        return '<i class="fa fa-eye emp_poc-icon emp_poc-icon-'.$row->id.'" onclick="showData('.$row->id.',\'emp_poc-\')" aria-hidden="true"></i><span class="emp_poc emp_poc-'.$row->id.'" style="display:none">'.$empPocFirstName.'</span>';

                    }
                    $empPocCount = $this->getAllEmpDataCount('employee_email', $row->employee_email);
                    return '<i class="fa fa-eye emp_poc-icon emp_poc-icon-'.$row->id.'" onclick="showData('.$row->id.',\'emp_poc-\')" aria-hidden="true"></i><div><span class="emp_poc emp_poc-'.$row->id.'" style="display:none">'.$empPocFirstName.(($empPocCount) ? "<span class='badge bg-indigo position-absolute top-0 end-0' style='margin-top: -6px'>$empPocCount</span>" : "").'</span></div>';
                })
                ->addColumn('client_status', function($row){
                    $interviewModel = new Interview();
                    $statusLastUpdatedAt = ($row->interview_status_updated_at) ? strtotime($row->interview_status_updated_at) : 0;
                    $interviewFeedback = $interviewModel->getInterviewFeedbackBasedOnSubmissionIdAndJobId($row->id, $row->Requirement->job_id);
                    $status = "<p data-order='$statusLastUpdatedAt'>".$interviewModel->getInterviewStatusBasedOnSubmissionIdAndJobId($row->id, $row->Requirement->job_id)."</p>";
                    $status .= "<span class='feedback' style='display:none'>".$this->getTooltipHtml($interviewFeedback)."</span>";
                    return $status;
                })
                ->rawColumns(['poc','pv','employer_name','employee_name','candidate_name','action','bdm_status','pv_status','emp_poc','created_at','client_status'])
                ->make(true);
        }

        $submissionModel = new Submission();
        $submissionStatusOptions[$submissionModel::STATUS_ACCEPT] = 'Show Accepted only';
        $submissionStatusOptions[$submissionModel::STATUS_REJECTED] = 'Show Rejected only';
        $submissionStatusOptions[$submissionModel::STATUS_PENDING] = 'Show Pending only';
        $submissionStatusOptions['un_viewed'] = 'Show Unviewed Only';
        $submissionStatusOptions['both'] = 'Show Both';

        $data['filterOptions'] = $submissionStatusOptions;
        $data['filterFile'] = 'common_filter';
        $data['pvCompanyName'] = $this->getPvCompanyName();

        return view('admin.bdm_submission.index',$data);
    }

    public function getRequirementIdBasedOnData($column, $data, $operator = 'equal')
    {
        if(!$column || !$data){
            return [];
        }

        if($operator == 'equal'){
            return Requirement::where($column, $data)->pluck('id')->toArray();
        } else if($operator == 'like'){
            return Requirement::where($column, 'Like', '%'.$data.'%')->pluck('id')->toArray();
        }

        return [];
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $data['menu'] = "Manage Submission";
        $data['submission'] = Submission::where('id',$id)->first();
        $data['sub_menu'] = "Submission";
        $data['isFromBDM'] = 1;
        $data['settings'] = $this->getSettingData();

        return view('admin.submission.edit',$data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            // 'name' => 'required',
            // 'email' => 'required',
            'location' => 'required',
            'phone' => 'required|numeric',
            'employer_detail' => 'required',
            'work_authorization' => 'required',
            'last_4_ssn' => 'required',
            'recruiter_rate' => 'required',
            'education_details' => 'required',
            'resume_experience' => 'required',
            'linkedin_id' => 'required',
            'relocation' => 'required',
            //'vendor_rate' => 'required',
            'employer_name' => 'required',
            'employee_name' => 'required',
            'employee_email' => 'required|email',
            'employee_phone' => 'required|numeric|digits:10',
        ]);

        $input = $request->all();
        unset($input['submission_id']);
        unset($input['name']);
        unset($input['email']);
        $Submission = Submission::where('id',$id)->first();
        $cloneSubmission = $Submission->replicate();
        $this->manageSubmissionLogs($input, $Submission, $cloneSubmission->Requirement->job_id);
        $Submission->update($input);
        $this->updateCandidateWithSameCandidateId($Submission,$cloneSubmission->Requirement->job_id);
        \Session::flash('success','Submission  has been updated successfully!');
        return redirect(route('bdm_submission.index'));
    }

    public function destroy($id)
    {
        //
    }

    public function changePvStatus(Request $request, $id)
    {
       $submission = Submission::where('id',$id)->first();

        if(!empty($submission)){
            $input['pv_status'] = $request['pv_status'];
            $input['pv_reason'] = '';
            $input['pv_status_updated_at'] = \Carbon\Carbon::now();
            $submission->update($input);

            $inputData['submission_id']  = $submission->id;
            $inputData['requirement_id'] = $submission->requirement_id;
            $inputData['entity_type']    = EntityHistory::ENTITY_TYPE_PV_STATUS;
            $inputData['entity_value']   = $submission->status;

            EntityHistory::create($inputData);

            $data['status'] = 1;
            $data['css']    = $this->getCandidateCss($submission);
            $data['class']  = $this->getCandidateClass($submission);
            $data['entity_type'] = EntityHistory::ENTITY_TYPE_PV_STATUS;
            $data['updated_date_html'] = getEntityLastUpdatedAtHtml(EntityHistory::ENTITY_TYPE_PV_STATUS,$submission->id);
        }else{
           $data['status'] = 0;
        }

        return $data;
    }

    public function pvRejectReasonUpdate(Request $request)
    {
        $submission = Submission::where('id',$request->submissionId)->first();

        if(!empty($submission)){
            $input['pv_reason'] = $request['pv_reason'];
            $input['pv_status'] = $request['pv_status'];
            $submission->update($input);
        }

        return redirect()->route('bdm_submission.index')->with('filter', $request['filter']);
    }

    public function getUpdateSubmissionData(Request $request){
        $data['status'] = 0;
        $submissionData = Submission::where('id', $request->id)->first();

        if(empty($submissionData)){
            return $data;
        }

        $data['status'] = 1;
        $data['submissionData'] = $submissionData;

        return $data;
    }

    public function updateSubmissionData(Request $request){
        $data['status'] = 0;
        if(empty($request->submission_id)){
            return $data;
        }
        $submission = Submission::where('id',$request->submission_id)->first();
        if(empty($submission)){
            return $data;
        }
        $inputData = $request->all();
        unset($inputData['submission_id']);
        unset($inputData['name']);
        unset($inputData['email']);
        $cloneSubmission = $submission->replicate();
        $this->manageSubmissionLogs($inputData, $submission, $cloneSubmission->Requirement->job_id);
        $submission->update($inputData);

        $this->updateCandidateWithSameCandidateId($submission,$cloneSubmission->Requirement->job_id);
        $data['status'] = 1;
        $data['url'] = route('bdm_submission.index');
        \Session::flash('success','Submission has been updated successfully!');
        return $data;
    }
}
