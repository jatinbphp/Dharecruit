<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use App\Models\Interview;
use App\Models\Submission;
use App\Models\Requirement;
use App\Models\InterviewDocuments;
use App\Models\EntityHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class InterviewController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('accessright:manage_interview');
    }

    public function index(Request $request)
    {
        $data['menu'] = "Manage Interview";

        if ($request->ajax()) {
            $loggedinUser = Auth::user()->id;
            $interviews = Interview::join('submissions', 'submissions.id', '=', 'interviews.submission_id')
                ->join('requirements', 'requirements.id', '=', 'submissions.requirement_id')
                ->join('admins', 'admins.id', '=', 'requirements.user_id')
                ->join('admins as recruiter', 'recruiter.id', '=', 'submissions.user_id')
                ->select(
                    'interviews.*',
                    'admins.name as bdm_name',
                    'recruiter.name as recruiter_name',
                );
            $submissionIds = [];

            if(!empty($request->fromDate)){
                $fromDate = date('Y-m-d', strtotime($request->fromDate));
                $interviews->where('interviews.created_at', '>=' ,$fromDate." 00:00:00");
            }

            if(!empty($request->toDate)){
                $toDate = date('Y-m-d', strtotime($request->toDate));
                $interviews->where('interviews.created_at', '<=' ,$toDate." 23:59:59");
            }

            if(!empty($request->client_feedback)){
                $interviews->whereIn('interviews.status', $request->client_feedback);
            }

            if(!empty($request->filter_employer_name)){
                $employerSubIds = $this->getSubmissionIdBasedOnData('employer_name', $request->filter_employer_name, 'like','submission');
                $submissionIds[] = $employerSubIds;
            }

            if(!empty($request->filter_employee_name)){
                $employeeSubIds = $this->getSubmissionIdBasedOnData('employee_name', $request->filter_employee_name, 'like', 'submission');
                $submissionIds[] = $employeeSubIds;
            }

            if(!empty($request->filter_employee_phone_number)){
                $employeePhoneSubIds = $this->getSubmissionIdBasedOnData('employee_phone', $request->filter_employee_phone_number, 'equal','submission');
                $submissionIds[] = $employeePhoneSubIds;
            }

            if(!empty($request->filter_employee_email)){
                $employeeEmailSubIds = $this->getSubmissionIdBasedOnData('employee_email', $request->filter_employee_email, 'equal', 'submission');
                $submissionIds[] = $employeeEmailSubIds;
            }

            if(!empty($request->candidate_name)){
                $candidateNameSubIds = $this->getSubmissionIdBasedOnData('name', $request->candidate_name, 'like', 'submission');
                $submissionIds[] = $candidateNameSubIds;
            }

            if(!empty($request->candidate_id)){
                $candidateIdSubIds = $this->getSubmissionIdBasedOnData('candidate_id', $request->candidate_id, 'equal', 'submission');
                $submissionIds[] = $candidateIdSubIds;
            }

            if(!empty($request->job_title)){
                $jobTitleSubIds = $this->getSubmissionIdBasedOnData('job_title', $request->job_title, 'like');
                $submissionIds[] = $jobTitleSubIds;
            }

            if(!empty($request->bdm)){
                $bdmSubIds = $this->getSubmissionIdBasedOnData('user_id', $request->bdm, 'equal');
                $submissionIds[] = $bdmSubIds;
            }

            if(!empty($request->recruiter)){
                $bdmSubIds = $this->getSubmissionIdBasedOnData('user_id', $request->recruiter, 'equal', 'submission');
                $submissionIds[] = $bdmSubIds;
            }

            if(!empty($request->job_id)){
                $jobIdSubIds = $this->getSubmissionIdBasedOnData('job_id', $request->job_id, 'equal');
                $submissionIds[] = $jobIdSubIds;
            }

            if(!empty($request->client)){
                $interviews->where('interviews.client', 'like', '%'.$request->client.'%');
            }

            if(!empty($request->job_location)){
                $jobLocationSubIds = $this->getSubmissionIdBasedOnData('location', $request->job_location, 'like');
                $submissionIds[] = $jobLocationSubIds;
            }

            if(!empty($request->pv_email)){
                $pvEmailReqIds = $this->getSubmissionIdBasedOnData('poc_email', $request->pv_email, 'equal');
                $submissionIds[] = $pvEmailReqIds;
            }

            if(!empty($request->pv_company)){
                $pvCompanyReqIds = $this->getSubmissionIdBasedOnData('pv_company_name', $request->pv_company, 'like');
                $submissionIds[] = $pvCompanyReqIds;
            }

            if(!empty($request->pv_name)){
                $pvNameReqIds = $this->getSubmissionIdBasedOnData('poc_name', $request->pv_name, 'like');
                $submissionIds[] = $pvNameReqIds;
            }

            if(!empty($request->pv_phone)){
                $pvPhoneReqIds = $this->getSubmissionIdBasedOnData('poc_phone_number', $request->pv_phone, 'equal');
                $submissionIds[] = $pvPhoneReqIds;
            }

            if($submissionIds && count($submissionIds)){
                $commonSubmissiontIds = call_user_func_array('array_intersect', $submissionIds);

                if($commonSubmissiontIds && count($commonSubmissiontIds)){
                    $interviews->whereIn('interviews.submission_id', $commonSubmissiontIds);
                } else {
                    $interviews->whereIn('interviews.submission_id', []);
                }
            }

            if(Auth::user()->role == 'recruiter'){
                $interviews->where('submissions.user_id', $loggedinUser);
            }elseif(Auth::user()->role == 'bdm'){
                $interviews->where('requirements.user_id', $loggedinUser);
            }

            return Datatables::of($interviews)
                ->addIndexColumn()
                ->editColumn('status', function($row){
                    $status = '<select name="interviewStatus" class="form-control select2 interviewStatus" data-id="'.$row->id.'">';
                    $interviewStatus = Interview::$interviewStatusOptions;
                    foreach ($interviewStatus as $key => $val){
                        $selected = $row->status == $key ? 'selected' : '';
                        $status .= '<option value="'.$key.'" '.$selected.'>'.$val.'</option>';
                    }
                    $status .= '</select>';
                    $status .= getEntityLastUpdatedAtHtml(EntityHistory::ENTITY_TYPE_INTERVIEW_STATUS,$row->submission_id);
                    $status .= "<span class='feedback' style='display:none'>".$this->getTooltipHtml($row->feedback)."</span>";
                    return $status;
                })
                ->addColumn('action', function($row){
                    return '<div class="btn-group btn-group-sm mr-2"><a href="'.url('admin/interview/'.$row->id.'/edit').'"><button class="btn btn-sm btn-default tip" data-toggle="tooltip" title="Edit Interview" data-trigger="hover" type="submit" ><i class="fa fa-edit"></i></button></a></div>';
                })
                ->editColumn('candidate_phone_number', function($row){
                    return '<i class="fa fa-eye candidate_phone-icon candidate-phone-icon-'.$row->id.'" onclick="showData('.$row->id.',\'candidate-phone-\')" aria-hidden="true"></i><span class="candidate_phone candidate-phone-'.$row->id.'" style="display:none">'.$row->candidate_phone_number.'</span>';
                })
                ->editColumn('candidate_email', function($row){
                    return '<i class="fa fa-eye candidate_email-icon candidate-email-icon-'.$row->id.'" onclick="showData('.$row->id.',\'candidate-email-\')" aria-hidden="true"></i><span class="candidate_email candidate-email-'.$row->id.'" style="display:none">'.$row->candidate_email.'</span>';
                })
                ->addColumn('candidate_name', function($row){
                    return $this->getCandidateStatusWiseHtml($row);
                })
                ->addColumn('created_at', function($row){
                    return date('m/d/Y', strtotime($row->created_at));
                })
                ->addColumn('client_location', function($row){
                    return $row->Submission->Requirement->location;
                })
                ->addColumn('candidate_location', function($row){
                    return $row->Submission->location;
                })
                ->addColumn('recruiter', function($row){
                    return $row->Submission->Recruiters->name;
                })
                ->addColumn('bdm', function($row){
                    return $row->Submission->Requirement->BDM->name;
                })
                ->addColumn('br', function($row){
                    return $row->Submission->Requirement->my_rate;
                })
                ->addColumn('rr', function($row){
                    return $row->Submission->recruiter_rate;
                })
                ->editColumn('employer_name', function($row){
                    if(Auth::user()->role != 'admin'){
                        return '<i class="fa fa-eye show_employer_name-icon employer-name-icon-'.$row->id.'" onclick="showData('.$row->id.',\'employer-name-\')" aria-hidden="true"></i><span class="show_employer_name employer-name-'.$row->id.'" style="display:none">'.$row->Submission->employer_name.'</span>';
                    }
                    $employerNameCount = $this->getAllEmpDataCount('employer_name', $row->Submission->employer_name);
                    return '<i class="fa fa-eye show_employer_name-icon employer-name-icon-'.$row->id.'" onclick="showData('.$row->id.',\'employer-name-\')" aria-hidden="true"></i><div class="container"><span class="show_employer_name employer-name-'.$row->id.'" style="display:none">'.$row->Submission->employer_name.(($employerNameCount) ? "<span class='badge bg-indigo position-absolute top-0 end-0' style='margin-top: -6px'>$employerNameCount</span>" : "").'</span></div>';
                    // return '<i class="fa fa-eye show_employer_name-icon show-employer-name-icon-'.$row->id.'" onclick="showData('.$row->id.',\'show-employer-name-\')" aria-hidden="true"></i><span class="show_employer_name show-employer-name-'.$row->id.'" style="display:none">'.$row->Submission->employer_name.'</span>';
                })
                ->editColumn('employee_name', function($row){
                    $empPocNameArray = explode(' ', $row->Submission->employee_name);
                    $empPocFirstName = isset($empPocNameArray[0]) ? $empPocNameArray[0] : '';

                    if(Auth::user()->role != 'admin'){
                        return '<i class="fa fa-eye emp_poc-icon emp_poc-icon-'.$row->id.'" onclick="showData('.$row->id.',\'emp_poc-\')" aria-hidden="true"></i><span class="emp_poc emp_poc-'.$row->id.'" style="display:none">'.$empPocFirstName.'</span>';

                    }
                    $empPocCount = $this->getAllEmpDataCount('employee_name', $row->Submission->employee_name);
                    return '<i class="fa fa-eye emp_poc-icon emp_poc-icon-'.$row->id.'" onclick="showData('.$row->id.',\'emp_poc-\')" aria-hidden="true"></i><div><span class="emp_poc emp_poc-'.$row->id.'" style="display:none">'.$empPocFirstName.(($empPocCount) ? "<span class='badge bg-indigo position-absolute top-0 end-0' style='margin-top: -6px'>$empPocCount</span>" : "").'</span></div>';
                    // return '<i class="fa fa-eye emp_poc-icon emp-poc-icon-'.$row->id.'" onclick="showData('.$row->id.',\'emp-poc-\')" aria-hidden="true"></i><span class="emp_poc emp-poc-'.$row->id.'" style="display:none">'.$empPocFirstName.'</span>';
                })
                ->addColumn('poc_name', function($row){
                    $pocNameArray = explode(' ', $row->Submission->Requirement->poc_name);
                    $pocFirstName = isset($pocNameArray[0]) ? $pocNameArray[0] : '';

                    if(Auth::user()->role != 'admin'){
                        return $pocFirstName;
                    }
                    $isNewPoc           = $this->isNewAsPerConfiguration('poc_name', $row->Submission->Requirement->poc_name);
                    $totalOrigReqInDays = $this->getTotalOrigReqBasedOnPocData($row->Submission->Requirement->poc_name, $row->Submission->Requirement->poc_email);

                    return '<div class="container"><p class="'.(($isNewPoc) ? "text-primary" : "").'">'.$row->Submission->Requirement->poc_name. (($totalOrigReqInDays) ? "<span class='badge bg-indigo position-absolute top-0 end-0' style='margin-top: -6px'>$totalOrigReqInDays</span>" : "").'</p></div>';

                    // return '<i class="fa fa-eye poc_name-icon poc-name-icon-'.$row->id.'" onclick="showData('.$row->id.',\'poc-name-\')" aria-hidden="true"></i><span class="poc_name poc-name-'.$row->id.'" style="display:none">'.$pocFirstName.'</span>';
                })
                ->addColumn('pv_name', function($row){
                    if(Auth::user()->role != 'admin'){
                        return $row->Submission->Requirement->pv_company_name;
                    }
                    $totalPvCount  = $this->getAllPvCompanyCount($row->Submission->Requirement->pv_company_name);
                    $isNewPoc      = $this->isNewAsPerConfiguration('pv_company_name', $row->Submission->Requirement->pv_company_name);

                    $pocHtml = '<span class="font-weight-bold '.(($isNewPoc) ? "text-primary" : "").'">'.$row->Submission->Requirement->pv_company_name;
                    $pocHtml .= '<br><br><span class="border pt-1 pl-1 pr-1 pb-1 '.(($isNewPoc) ? "border-primary" : "border-secondary").'">'.$totalPvCount.'</span></span>';
                    return $pocHtml;

                    //return '<i class="fa fa-eye pv_name-icon pv-name-icon-'.$row->id.'" onclick="showData('.$row->id.',\'pv-name-\')" aria-hidden="true"></i><span class="pv_name pv-name-'.$row->id.'" style="display:none">'.$row->Submission->Requirement->pv_company_name.'</span>';
                })
                ->editColumn('hiring_manager', function($row){
                    return '<i class="fa fa-eye hiring_manager-icon hiring-manager-icon-'.$row->id.'" onclick="showData('.$row->id.',\'hiring-manager-\')" aria-hidden="true"></i><span class="hiring_manager hiring-manager-'.$row->id.'" style="display:none">'.$row->hiring_manager.'</span>';
                })
                ->editColumn('client', function($row){
                    return '<i class="fa fa-eye client_data-icon client_data-icon-'.$row->id.'" onclick="showData('.$row->id.',\'client_data-\')" aria-hidden="true"></i><span class="client_data client_data-'.$row->id.'" style="display:none">'.$row->client.'</span>';
                })
                ->addColumn('interview_time', function($row){
                    return '<br><span style="font-weight:bold">'.date('m/d l', strtotime($row->interview_date)).'</span><br>
                    <span>'.date('H:i:s', strtotime($row->interview_time)) .' '. $row->time_zone.'</span>';
                })
                ->addColumn('job_id', function($row){
                    return '<span class=" job-title" data-id="'.$row->Submission->requirement_id.'">'.$row->job_id.'</span>';;
                })
                ->rawColumns(['status','candidate_name','action','candidate_phone_number','emp_poc','candidate_email','employer_name','employee_name','poc_name','pv_name','hiring_manager','client','interview_time','job_id'])
                ->make(true);
        }

        $data['filterFile'] = 'common_filter';

        return view('admin.interview.index', $data);
    }

    public function getSubmissionIdBasedOnData($column, $data, $operator = '', $tableName = 'requirement')
    {
        if(!$column || !$data){
            return [];
        }

        $requirementId = [];

        if($operator == 'equal'){
            if($tableName == 'submission'){
                return Submission::where($column, $data)->pluck('id')->toArray();
            } else {
                $requirementId = Requirement::where($column, $data)->pluck('id')->toArray();
            }
        } else if($operator == 'like'){
            if($tableName == 'submission'){
                return Submission::where($column, 'Like', '%'.$data.'%')->pluck('id')->toArray();
            } else {
                $requirementId = Requirement::where($column, 'Like', '%'.$data.'%')->pluck('id')->toArray();
            }
        }

        return Submission::whereIn('requirement_id', $requirementId)->pluck('id')->toArray();
    }

    public function create()
    {
        $data['menu'] = "Manage Interview";
        $data['interviewStatus'] = Interview::$interviewStatusOptions;
        return view("admin.interview.create",$data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'interview_date' => 'required|date',
            'interview_time' => 'required',
            'candidate_phone_number' => 'required|numeric|digits:10',
            'candidate_email' => 'required|email',
            'time_zone' => 'required',
            'status' => 'required',
            //'document' => 'required',
        ]);

        $existInterview = Interview::where('submission_id',$request->submission_id)->where('job_id',$request->job_id)->first();
        if(!empty($existInterview)){
            $msg = 'Interview has been already Added For '.$existInterview->Submission->name;
            \Session::flash('danger', $msg);
            return redirect()->back();
        }

        $input = $request->all();
        $input['user_id'] = Auth::user()->id;
        $interview = Interview::create($input);

        if(!empty($interview)){
            if(!empty($request['document'])){
                if($files = $request->file('document')){
                    foreach ($files as $file) {
                        $documentData['interview_id'] = $interview->id;
                        $documentData['document'] = $this->fileMove($file,'user_documents');
                        InterviewDocuments::create($documentData);
                    }
                }
            }
        }

        $inputData['submission_id']  = $interview->submission_id;
        $inputData['requirement_id'] = $interview->Submission->Requirement->id;
        $inputData['entity_type']    = EntityHistory::ENTITY_TYPE_INTERVIEW_STATUS;
        $inputData['entity_value']   = $interview->status;

        $submission = Submission::where('id', $request->submission_id)->first();
        $submission->update(['interview_status_updated_at' => \Carbon\Carbon::now()]);

        EntityHistory::create($inputData);

        \Session::flash('success', 'Interview has been inserted successfully!');
        return redirect()->route('interview.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $data['menu'] = "My Interview";
        $data['interview'] = Interview::where('id',$id)->first();
        $data['interviewStatus'] = Interview::$interviewStatusOptions;
        $data['interviewDocuments'] = InterviewDocuments::where('interview_id',$id)->pluck('document','id');
        return view('admin.interview.edit',$data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'interview_date' => 'required|date',
            'interview_time' => 'required',
            'candidate_phone_number' => 'required|numeric|digits:10',
            'candidate_email' => 'required|email',
            'time_zone' => 'required',
            'status' => 'required',
        ]);

        $input = $request->all();
        $interview = Interview::where('id',$id)->first();
        $interview->update($request->all());

        if($interview){
            if(!empty($request['document'])){
                if($files = $request->file('document')){
                    foreach ($files as $file) {
                        $documentData['interview_id'] = $interview->id;
                        $documentData['document'] = $this->fileMove($file,'user_documents');
                        InterviewDocuments::create($documentData);
                    }
                }
            }
        }

        \Session::flash('success','Interview has been updated successfully!');
        return redirect()->route('interview.index');
    }

    public function destroy($id)
    {
        //
    }

    public function changeInterviewStatus(Request $request, $id){
        $interview = Interview::where('id',$id)->first();
        if(empty($interview)){
            $data['status'] = 0;
            return $data;
        }
        $input['status'] = $request['status'];
        $interview->update($input);

        $entityTypeInterviewStatus = EntityHistory::ENTITY_TYPE_INTERVIEW_STATUS;

        $inputData['submission_id']  = $interview->submission_id;
        $inputData['requirement_id'] = $interview->Submission->Requirement->id;
        $inputData['entity_type']    = $entityTypeInterviewStatus;
        $inputData['entity_value']   = $interview->status;

        $submission = Submission::where('id', $interview->submission_id)->first();
        $submission->update(['interview_status_updated_at' => \Carbon\Carbon::now()]);

        EntityHistory::create($inputData);

        $data['status']                 = 1;
        $data['updated_date_html']      = getEntityLastUpdatedAtHtml($entityTypeInterviewStatus,$interview->submission_id);
        $data['updated_candidate_html'] = $this->getCandidateStatusWiseHtml($interview);
        $data['submission_id']          = $interview->submission_id;
        $data['entity_type']            = $entityTypeInterviewStatus;
        return $data;
    }

    function getCandidatesName(Request $request) {
        if(empty($request->job_id)){
            return 0;
        }
        $requirementId = Requirement::where('job_id',$request->job_id)->pluck('id')->first();
        $candidateData = Submission::where('requirement_id',$requirementId)->where('status',Submission::STATUS_ACCEPT)->where('pv_status',Submission::STATUS_SUBMITTED_TO_END_CLIENT)->whereNotNull('name')->select('name','id')->orderBy('id', 'DESC')->get()->unique('name');

        $data['status']        = 0;
        $data['cnadidateName'] = '';

        if(!count($candidateData)){
            return $data;
        }

        $data['status'] = 1;
        $option = '<option value="">Please Select Candidate Name</option>';
        foreach ($candidateData as $candidate){
            $option .= '<option value="'.$candidate['id'].'" data-id="'.$candidate['id'].'">'.$candidate['name'].'</option>';
        }
        $data['cnadidateName'] .= ' <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label col-md-12" for="candidateSelection">Employee Name</label>
                                        <select class="form-control select2 col-md-12" id="candidateSelection" style="width: 100%" onChange="loadCandidateData(event)">
                                            '.$option.'
                                        </select>
                                    </div>
                                </div>';

        return $data;
    }

    public function getCandidateData(Request $request){
        $data['status'] = 0;

        if(!$request->candidate_id){
            return $data;
        }

        $submissionData = Submission::where('id',$request->candidate_id)->first();

        if(empty($submissionData)){
            return $data;
        }

        $candidateData['client']                 = $submissionData->Requirement->client_name;
        $candidateData['candidate_phone_number'] = $submissionData->phone;
        $candidateData['candidate_email']        = $submissionData->email;
        $candidateData['recruiter_name']         = $submissionData->Recruiters->name;

        $data['status']        = 1;
        $data['candidateData'] = $candidateData;

        return $data;
    }

    public function getCandidateStatusWiseHtml($interview){
        $statusData = Interview::where('id',$interview->id)->first(['status']);
        if(!$statusData || !$statusData->status){
            return '';
        }

        $textColor = '';
        $divClass  = '';

        $interviewStatus = $statusData->status;
        $interviewModel  = new Interview();
        $divCss          = "width: fit-content;";

        if($interviewStatus == $interviewModel::STATUS_SCHEDULED){
            $divClass .= 'border border-warning rounded-pill';
            $textColor = 'text-dark';
        } else if($interviewStatus == $interviewModel::STATUS_RE_SCHEDULED){
            $divClass .= 'border-warning-10 rounded-pill';
            $textColor = 'text-dark';
        } else if(in_array($interviewStatus, [$interviewModel::STATUS_SELECTED_FOR_NEXT_ROUND, $interviewModel::STATUS_WAITING_FEEDBACK])){
            $divClass .= 'bg-warning rounded-pill';
            $textColor = 'text-dark';
        } else if($interviewStatus == $interviewModel::STATUS_CONFIRMED_POSITION){
            $divClass .= 'bg-success';
            $textColor = 'text-dark';
        } else if($interviewStatus == $interviewModel::STATUS_REJECTED){
            $divClass .= 'bg-danger';
            $textColor = 'text-white';
        } else if($interviewStatus == $interviewModel::STATUS_BACKOUT){
            $divClass .= 'bg-dark';
            $textColor = 'text-white';
        }

        $candidateCount = $this->getCandidateCountByEmail($interview->Submission->email);
        $submission = Submission::where('id',$interview->submission_id)->first();
        $candidateNames = explode(' ',$interview->Submission->name);
        $candidateName = isset($candidateNames[0]) ? $candidateNames[0] : '';
        $isCandidateHasLog  = $this->isCandidateHasLog($submission);
        $isEmployerNameChanged = $this->isEmployerNameChanged($submission->candidate_id);
        $timeSpan = $this->getSubmissionTimeSpan($interview->Submission->Requirement->created_at, $interview->Submission->created_at);
        $isSamePvCandidate = $this->isSamePvCandidate($interview->Submission->email, $interview->Submission->requirement_id, $interview->Submission->id);

        return ($candidateCount ? "<span class='badge bg-indigo position-absolute top-0 start-100 translate-middle'>$candidateCount</span>" : "")
                .(($isCandidateHasLog) ? "<span class='badge badge-pill badge-primary ml-4 position-absolute top-0 start-100 translate-middle'>L</span>" : "")
                .(($isEmployerNameChanged) ? "<span class='badge bg-red ml-5'>2 Emp</span>" : "")
                .'<div class="candidate-'. $interview->id .'">
                    <div class="'.$divClass.'  pt-2 pl-2 pb-2 pr-2" style="'.$divCss.'">
                        <span class="candidate '.$textColor.'" data-cid='.$interview->submission_id.'>'.($isSamePvCandidate ? "<i class='fa fa-info'></i>  ": "").$candidateName.'-'.$interview->Submission->candidate_id.'</span>
                    </div>
                    <div class="p-1 mt-1 border border-dark" style="width: fit-content;">
                        <span class="text-secondary font-weight-bold">'.$timeSpan.'</span>
                    </div>
                </div>';
    }

    public function removeDocument($id) {
        $data = [];
        if(!$id){
            $data['status'] = 0;
            return $data;
        }
        InterviewDocuments::where('id', $id)->delete();
        $data['status'] = 1;

        return $data;
    }
}
