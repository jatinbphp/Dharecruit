<?php

namespace App\Http\Controllers\Admin;

use \Carbon\Carbon;
use App\Models\Submission;
use App\Models\Requirement;
use App\Models\RequirementDocuments;
use App\Models\Interview;
use App\Models\DataLog;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Models\EntityHistory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CommonController extends Controller
{
    public function getCandidate(Request $request){
        $submission = Submission::where('id',$request->cId)->first();
        $data['is_show'] = 0;
        if(!empty($submission)){
            if($submission->requirement->user_id == Auth::user()->id){
                if($submission->is_show == 0){
                    $submission->update(['is_show' => 1]);
                    $data['is_show'] = 1;
                }
            }
        }

        $submission = Submission::with('Recruiters','Requirement','Requirement.BDM','Requirement.Category','Requirement.PvCompany')->where('id',$request['cId'])->first();
        $rData = '';
        $cData = '';
        $jobTitle = '';
        $candidateStatus = '';
        $commSkills = '';
        $skiilsMatch = '';
        $reason = '';
        $historyData = '';
        $status = 0;
        $showLogButton = 1;
        $manageLogFileds = Submission::$manageLogFileds;
        $isInterviewCreated = 0;
        if(!empty($submission)){
            $interviewRow = Interview::where('submission_id', $submission->id)->first();
            if($interviewRow && $interviewRow->id){
                $isInterviewCreated = 1;
            }
            $status = 1;
            $candidateStatus = $submission['status'];
            $commSkills = $submission['common_skills'];
            $skiilsMatch = $submission['skills_match'];
            $reason = $submission['skills_match'];
            $jobTitle = $submission['Requirement']['job_title'];
            $oldLocationHtml = $this->getLogDataByName($submission, 'location');
            $oldPhoneHtml = $this->getLogDataByName($submission, 'phone');
            $oldWorkAuthorizationHtml = $this->getLogDataByName($submission, 'work_authorization');
            $oldLast4ssnHtml = $this->getLogDataByName($submission, 'last_4_ssn');
            $oldEducationDetailsHtml = $this->getLogDataByName($submission, 'education_details');
            $oldResumeExperienceHtml = $this->getLogDataByName($submission, 'resume_experience');
            $oldLinkedinIdHtml = $this->getLogDataByName($submission, 'linkedin_id');
            $oldEmployerNameHtml = '';//$this->getLogDataByName($submission, 'employer_name');
            $oldEmployerDetailHtml = $this->getLogDataByName($submission, 'employer_detail');
            $oldRelocationHtml = $this->getLogDataByName($submission, 'relocation');
            if(!$oldLocationHtml && !$oldPhoneHtml && !$oldWorkAuthorizationHtml && !$oldLast4ssnHtml && !$oldEducationDetailsHtml && !$oldResumeExperienceHtml && !$oldLinkedinIdHtml && !$oldEmployerNameHtml && !$oldEmployerDetailHtml){
                $showLogButton = 0;
            }
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
                                <strong>Location:</strong> <span class="actual-data">'.$submission['location'].'</span>'.((in_array('location',$manageLogFileds) && $oldLocationHtml) ? "<span class='badge badge-primary ml-2'>L</span>" : "").((in_array('location',$manageLogFileds) && $oldLocationHtml) ? $oldLocationHtml : "").'
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <strong>Phone:</strong> <span class="actual-data">'.$submission['phone'].'</span>'.((in_array('phone',$manageLogFileds) && $oldPhoneHtml) ? "<span class='badge badge-primary ml-2'>L</span>" : "").((in_array('phone',$manageLogFileds) && $oldPhoneHtml) ? $oldPhoneHtml : "").'
                            </div>
                            <div class="col-md-6">
                                <strong>Employer Detail:</strong> <span class="actual-data">'.$submission['employer_detail'].'</span>'.((in_array('employer_detail',$manageLogFileds) && $oldEmployerDetailHtml) ? "<span class='badge badge-primary ml-2'>L</span>" : "").((in_array('employer_detail',$manageLogFileds) && $oldEmployerDetailHtml) ? $oldEmployerDetailHtml : "").'
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <strong>Work Authorization:</strong> <span class="actual-data">'.$submission['work_authorization'].'</span>'.((in_array('work_authorization',$manageLogFileds) && $oldWorkAuthorizationHtml) ? "<span class='badge badge-primary ml-2'>L</span>" : "").((in_array('work_authorization',$manageLogFileds) && $oldWorkAuthorizationHtml) ? $oldWorkAuthorizationHtml : "").'
                            </div>
                            <div class="col-md-6">
                                <strong>Last 4 SSN:</strong> <span class="actual-data">'.$submission['last_4_ssn'].'</span>'.((in_array('last_4_ssn',$manageLogFileds) && $oldLast4ssnHtml) ? "<span class='badge badge-primary ml-2'>L</span>" : "").((in_array('last_4_ssn',$manageLogFileds) && $oldLast4ssnHtml) ? $oldLast4ssnHtml : "").'
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <strong>Education Detail:</strong> <span class="actual-data">'.$submission['education_details'].'</span>'.((in_array('education_details',$manageLogFileds) && $oldEducationDetailsHtml) ? "<span class='badge badge-primary ml-2'>L</span>" : "").((in_array('education_details',$manageLogFileds) && $oldEducationDetailsHtml) ? $oldEducationDetailsHtml : "").'
                            </div>
                            <div class="col-md-6">
                                <strong>Resume Experience:</strong> <span class="actual-data">'.$submission['resume_experience'].'</span>'.((in_array('resume_experience',$manageLogFileds) && $oldResumeExperienceHtml) ? "<span class='badge badge-primary ml-2'>L</span>" : "").((in_array('resume_experience',$manageLogFileds) && $oldResumeExperienceHtml) ? $oldResumeExperienceHtml : "").'
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <strong>Linkedin Id:</strong> <span class="actual-data">'.$submission['linkedin_id'].'</span>'.((in_array('linkedin_id',$manageLogFileds) && $oldLinkedinIdHtml) ? "<span class='badge badge-primary ml-2'>L</span>" : "").((in_array('linkedin_id',$manageLogFileds) && $oldLinkedinIdHtml) ? $oldLinkedinIdHtml : "").'
                            </div>
                            <div class="col-md-6">
                                <strong>R Rate:</strong> '.$submission['recruiter_rate'].'
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <strong>Relocation:</strong> <span class="actual-data">'.$submission['relocation'].'</span>'.((in_array('relocation',$manageLogFileds) && $oldRelocationHtml) ? "<span class='badge badge-primary ml-2'>L</span>" : "").((in_array('relocation',$manageLogFileds) && $oldRelocationHtml) ? $oldRelocationHtml : "").'
                            </div>
                        </div>';
                        if(Auth::user()->role == 'bdm'){
                            $cData .= '<div class="row mt-2">
                                            <div class="col-md-6">
                                                <strong>Employer Name:</strong> <span class="actual-data">'.$submission['employer_name'].'</span>'.((in_array('employer_name',$manageLogFileds) && $oldEmployerNameHtml) ? "<span class='badge badge-primary ml-2'>L</span>" : "").((in_array('employer_name',$manageLogFileds) && $oldEmployerNameHtml) ? $oldEmployerNameHtml : "").'
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Resume:</strong><a href="'.asset('storage/'.$submission['documents']).'" target="_blank"><img src="'.url('assets/dist/img/resume.png').'" height="50"></a>
                                            </div>
                                        </div>';
                        } else {
                            $cData .= '<div class="row mt-2">
                                            <div class="col-md-6">
                                                <strong>Employer Name:</strong> <span class="actual-data">'.$submission['employer_name'].'</span>'.((in_array('employer_name',$manageLogFileds) && $oldEmployerNameHtml) ? "<span class='badge badge-primary ml-2'>L</span>" : "").((in_array('employer_name',$manageLogFileds) && $oldEmployerNameHtml) ? $oldEmployerNameHtml : "").'
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Employee Name:</strong> '.$submission['employee_name'].'
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-6">
                                                <strong>Employee Email:</strong> '.$submission['employee_email'].'
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Employee Phone:</strong> '.$submission['employee_phone'].'
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-6">
                                                <strong>Resume:</strong><a href="'.asset('storage/'.$submission['documents']).'" target="_blank"><img src="'.url('assets/dist/img/resume.png').'" height="50"></a>
                                            </div>
                                        </div>';
                        }
            if(in_array(Auth::user()->role, ['bdm','admin'])){
                $candidateHistory = Submission::where('email',$submission->email)->where('id','!=',$submission->id)->orderBy('id', 'DESC')->get();
                $historyData .= '<h3>Candidate History</h3>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Date Of Submission</th>
                            <th scope="col">Job Id</th>
                            <th scope="col">Job TItle</th>
                            <th scope="col">BDM</th>
                            <th scope="col">Recruiter</th>
                            <th scope="col">Job Location</th>
                            <th scope="col">Work Type</th>
                            <th scope="col">BDM Status</th>
                            <th scope="col">PV Status</th>
                            <th scope="col">Employer</th>
                            <th scope="col">Download Resume</th>
                        </tr>
                    </thead>
                    <tbody>';
                    if(!count($candidateHistory)){
                        $historyData .= '<tr>
                                            <td align="center" colspan="11">No History Found.</td>
                                        </tr>';
                    } else {
                        $submissionObj = new Submission();
                        $allPvStatus   = $submissionObj::$pvStatus;

                        foreach($candidateHistory as $candidateData){
                            $pvStatus = '';

                            if(!empty($candidateData->pv_status)){
                                $pvStatus = isset($allPvStatus[$candidateData->pv_status]) ? $allPvStatus[$candidateData->pv_status] : '';
                            }

                            $historyData .= '<tr data-id='.$candidateData->id.'>
                                                <td class="'.(($submission->Requirement->pv_company_name == $candidateData->Requirement->pv_company_name) ? "bg-primary" : "").'">'.date('m-d-Y',strtotime($candidateData->created_at)).'</td>
                                                <td>'.$candidateData->requirement->job_id.'</td>
                                                <td>'.$candidateData->requirement->job_title.'</td>
                                                <td>'.$candidateData->requirement->BDM->name.'</td>
                                                <td>'.$candidateData->recruiters->name.'</td>
                                                <td>'.$candidateData->requirement->location.'</td>
                                                <td>'.$candidateData->requirement->work_type.'</td>
                                                <td>
                                                    <span>'.ucfirst($candidateData->status).'</span><br>
                                                    <span>'.$this->getTooltipHtml($candidateData->reason).'</span>
                                                </td>
                                                <td>
                                                    <span>'.$pvStatus.'</span><br>
                                                    <span>'.$this->getTooltipHtml($candidateData->pv_reason).'</span>
                                                </td>
                                                <td>'.$candidateData->employer_name.'</td>
                                                <td>
                                                    <div class="col-md-2 mt-2">
                                                        <div class="text-center">
                                                            <a href="'.asset('storage/'.$candidateData->documents).'" target="_blank"><img src="'.url('assets/dist/img/resume.png').'" height="50"></a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>';
                        }
                    }
                    $historyData .= '</tbody>
                </table>';
            }

            $editData = view('admin/updateSubmissionForm')->toHtml();
        }
        $data['requirementData'] = $rData;
        $data['candidateData'] = $cData;
        $data['submission'] = $submission;
        $data['candidateStatus'] = $candidateStatus;
        $data['status'] = $status;
        $data['historyData'] = $historyData;
        $data['showLogButton'] = $showLogButton;
        $data['editData'] = $editData;
        $data['linking_data'] = $this->getEmployeeLinkData([],$submission->employee_email);
        $data['isInterviewCreated'] = $isInterviewCreated;
        return $data;
    }

    function getRequirement(Request $request) {
        $data = [];
        $status = 0;
        if(empty($request->id)){
            $data['status'] = $status;
            return $data;
        }
        $requirement = Requirement::where('id',$request->id)->first();
        $requirementTitle = '';
        $requirementContent = '';
        $data['is_show_requirement'] = 0;
        $isRecruiter = 0;
        $isShowRecruiterAfterUpdate = 0;
        if(!empty($requirement)){
            $status = 1;
            $user = Auth::user();
            if($user->role == 'recruiter'){
                $isShowRecruiterAfterUpdate = array_filter(explode(',', $requirement->is_show_recruiter_after_update));
                if(!in_array($user->id, $isShowRecruiterAfterUpdate)){
                    array_push($isShowRecruiterAfterUpdate, $user->id);
                    $requirement->is_show_recruiter_after_update = ','.implode(',', $isShowRecruiterAfterUpdate).',';
                    $requirement->save();
                    $data['is_show_requirement'] = 1;
                    $isShowRecruiterAfterUpdate = 1;
                }
                $isRecruiter = 1;
                $isShowRecruiters = array_filter(explode(',', $requirement->is_show_recruiter));
                if(!in_array($user->id, $isShowRecruiters)){
                    array_push($isShowRecruiters, $user->id);
                    $requirement->is_show_recruiter = ','.implode(',', $isShowRecruiters).',';
                    $requirement->save();
                    $data['is_show_requirement'] = 1;
                    $isShowRecruiterAfterUpdate = 0;
                }
            }
            $updatedFileds = (!empty($requirement->updated_fileds)) ? explode(',', $requirement->updated_fileds) : [];

            $requirementTitle = $requirement->job_title;
            $requirementDocuments = RequirementDocuments::where('requirement_id',$request->id)->pluck('document','id');

            $requirementContent .= '
            <div class="row">
                <div class="col-md-6">
                    <strong>Job Title:</strong> <span class="'.($isRecruiter == 1 && $isShowRecruiterAfterUpdate == 1 && in_array("job_title",$updatedFileds) ? "text-primary font-weight-bold" : "").'">'.$requirement->job_title.'</span>
                </div>
                <div class="col-md-6">
                    <strong>No # Position:</strong> <span class="'.($isRecruiter == 1 && $isShowRecruiterAfterUpdate == 1 && in_array("no_of_position",$updatedFileds) ? "text-primary font-weight-bold" : "").'">'.$requirement->no_of_position.'</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <strong>Experience:</strong> <span<span class="'.($isRecruiter == 1 && $isShowRecruiterAfterUpdate == 1 && in_array("experience",$updatedFileds) ? "text-primary font-weight-bold" : "").'">'.$requirement->experience.'</span>
                </div>
                <div class="col-md-6">
                    <strong>Location:</strong> <span<span class="'.($isRecruiter == 1 && $isShowRecruiterAfterUpdate == 1 && in_array("location",$updatedFileds) ? "text-primary font-weight-bold" : "").'">'.$requirement->location.'</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <strong>Onsite/Hybrid/Remote:</strong> <span<span class="'.($isRecruiter == 1 && $isShowRecruiterAfterUpdate == 1 && in_array("work_type",$updatedFileds) ? "text-primary font-weight-bold" : "").'">'.$requirement->work_type.'</span>
                </div>
                <div class="col-md-6">
                    <strong>Duration:</strong> <span<span class="'.($isRecruiter == 1 && $isShowRecruiterAfterUpdate == 1 && in_array("duration",$updatedFileds) ? "text-primary font-weight-bold" : "").'">'.$requirement->duration.'</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <strong>Visa:</strong> <span<span class="'.($isRecruiter == 1 && $isShowRecruiterAfterUpdate == 1 && in_array("visa",$updatedFileds) ? "text-primary font-weight-bold" : "").'">'.Requirement::getVisaNames($requirement->visa) .'</span>
                </div>
                <div class="col-md-6">
                    <strong>Client:</strong> <span<span class="'.($isRecruiter == 1 && $isShowRecruiterAfterUpdate == 1 && in_array("client",$updatedFileds) ? "text-primary font-weight-bold" : "").'">'.$requirement->client.'</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <strong>Priority:</strong> <span<span class="'.($isRecruiter == 1 && $isShowRecruiterAfterUpdate == 1 && in_array("priority",$updatedFileds) ? "text-primary font-weight-bold" : "").'">'.$requirement->priority.'</span>
                </div>
                <div class="col-md-6">
                    <strong>Term:</strong> <span<span class="'.($isRecruiter == 1 && $isShowRecruiterAfterUpdate == 1 && in_array("term",$updatedFileds) ? "text-primary font-weight-bold" : "").'">'.$requirement->term.'</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <strong>Category:</strong> <span<span class="'.($isRecruiter == 1 && $isShowRecruiterAfterUpdate == 1 && in_array("category",$updatedFileds) ? "text-primary font-weight-bold" : "").'">'.$requirement->Category->name.'</span>
                </div>
                <div class="col-md-6">
                    <strong>MOI:</strong> <span<span class="'.($isRecruiter == 1 && $isShowRecruiterAfterUpdate == 1 && in_array("moi",$updatedFileds) ? "text-primary font-weight-bold" : "").'">'.Requirement::getMoiNames($requirement->moi).'</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <strong>Job Keyword:</strong> <span<span class="'.($isRecruiter == 1 && $isShowRecruiterAfterUpdate == 1 && in_array("job_keyword",$updatedFileds) ? "text-primary font-weight-bold" : "").'">'.$requirement->job_keyword.'</span>
                </div>
                <div class="col-md-6">
                    <strong>Special Notes:</strong> <span<span class="'.($isRecruiter == 1 && $isShowRecruiterAfterUpdate == 1 && in_array("notes",$updatedFileds) ? "text-primary font-weight-bold" : "").'">'.$requirement->notes.'</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <strong>Download Attachments:</strong><br>';
                    if(!empty($requirementDocuments) && count($requirementDocuments)){
                        $requirementContent .= '
                        <div class="border border-primary">';
                            foreach($requirementDocuments as $id => $document){
                                $requirementContent .=
                                '<div class="col-md-12 mt-2">
                                    <div class="text-center">';
                                        $documentNameArray = explode('/',$document);
                                        $documentName = isset($documentNameArray[2]) ? $documentNameArray[2] : '';
                                        $requirementContent .=
                                        '<a href="'. asset('storage/'.$document).'" target="_blank"><p class="text-left">'.$documentName.'</p></a>
                                    </div>
                                </div>';
                            }
                        $requirementContent .= '
                        </div>';
                    }
                    $requirementContent .= '
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <strong>Job Description:</strong>
                </div>
                <div class="col-md-12">
                    <span<span class="'.($isRecruiter == 1 && $isShowRecruiterAfterUpdate == 1 && in_array("description",$updatedFileds) ? "text-primary font-weight-bold" : "").'">
                        '.$requirement->description.'
                    </span>
                </div>
            </div>';
        }
        $data['status']             = $status;
        $data['requirementContent'] = $requirementContent;
        $data['requirementTitle']   = $requirementTitle;
        $data['requirememt']        = $requirement;
        $data['isShowRequirement']  = 1;

        return $data;
    }

    public function getSubmissionData(Request $request){
        if(!$request->requirement_id){
            $data['status'] = 0;
            return $data;
        }
        $requirement = Requirement::where('id',$request->requirement_id)->first();

        $user = Auth::user();
        if($user->role == 'recruiter'){
            $submissions = Submission::where('user_id', $user->id)->where('requirement_id',$request->requirement_id)->get();
        }elseif($user->role == 'bdm'){
            $requirementIds = Requirement::where('user_id', $user->id)->pluck('id')->toArray();
            $submissions = Submission::whereIn('requirement_id', $requirementIds)->where('requirement_id',$request->requirement_id)->get();
        }else{
            $submissions = Submission::where('requirement_id',$request->requirement_id)->get();
        }

        $submissionData = '';
        $submissionHeadingData = '';
        $submissionHeaderData = '';

        if(!empty($requirement)){
            $submissionHeadingData .=
                '<div class="col-md-12">
                    <span class="h5" style="font-weight:bold">('.$requirement->job_title.')</span>
                </div>
                <div class="col-md-12">
                    <sapm class="h5" style="font-weight:bold">'.date('m/d  h:i A', strtotime($requirement->created_at)).'</span>
                </div>
                ';

            $submissionHeaderData .=
                '<div class="row">
                    <div class="col">
                        <span class="h5" style="font-weight:bold">Req.:</span><br><span>'.$requirement->id.'</span>
                    </div>
                    <div class="col">
                        <span class="h5" style="font-weight:bold">Client Loc:</span><br><span>'.$requirement->location.'</span>
                    </div>
                    <div class="col">
                        <span class="h5" style="font-weight:bold">BDM:</span><br><span>'.$requirement->BDM->name.'</span>
                    </div>';

                    if(Auth::user()->role != 'recruiter'){
                        $submissionHeaderData .= '<div class="col">
                            <span class="h5" style="font-weight:bold">PV:</span><br>
                            <i class="fa fa-eye  pv-companny-popup-icon" onclick="showPVData()" aria-hidden="true"></i><span class="pv-company" style="display:none">'.$requirement->pv_company_name.'</span>
                        </div>';
                    }
                    $submissionHeaderData .='<div class="col">
                        <span class="h5" style="font-weight:bold">Client:</span><br><span>'.(($requirement->display_client && $requirement->display_client == 1) ? $requirement->client_name : '').'</span>
                    </div>
                    <div class="col">
                        <span class="h5" style="font-weight:bold">BDM Rate:</span><br><span>'.$requirement->my_rate.'</span>
                    </div>
                    <div class="col">
                        <span class="h5" style="font-weight:bold">Term:</span><br><span>'.$requirement->term.'</span>
                    </div>
                    <div class="col">
                        <span class="h5" style="font-weight:bold">Type:</span><br><span>'.$requirement->work_type.'</span>
                    </div>
                </div>';
        }

        $submissionData .= '
        <table class="table table-striped" id="submissionDataTable">
            <thead>
                <tr>
                    <th scope="col">Time Span</th>
                    <th scope="col">Sub ID</th>
                    <th scope="col">Consultant</th>
                    <th scope="col">Location</th>
                    <th scope="col">Recruiter</th>
                    <th scope="col">RRate</th>
                    <th scope="col">Emp Name</th>
                    <th scope="col">BDM Status</th>
                    <th scope="col">Vendor Status</th>
                    <th scope="col">Client Status</th>
                </tr>
            </thead>
        ';

        $requirementCreatedDate = Carbon::parse($requirement->created_at);
        $interviewModel         = new Interview();

        if(!empty($submissions) && count($submissions)){
            $submissionModel     = new Submission();
            $entityModel         = new EntityHistory();
            $bdmStatus           = $submissionModel::$status;
            $pvStatus            = $submissionModel::$pvStatus;
            $entityTypeBdm       = $entityModel::ENTITY_TYPE_BDM_STATUS;
            $entityTypePv        = $entityModel::ENTITY_TYPE_PV_STATUS;
            $entityTypeInterview = $entityModel::ENTITY_TYPE_INTERVIEW_STATUS;
            foreach($submissions as $submission){
                $candidateClass = $this->getCandidateClass($submission,true);
                $candidateCss   = $this->getCandidateCss($submission,true);
                $candidateBorderCss = $this->getCandidateBorderCss($submission);
                $candidateNames = explode(' ',$submission->name);
                $candidateName = isset($candidateNames[0]) ? $candidateNames[0] : '';
                $timeSpan = $this->getSubmissionTimeSpan($requirementCreatedDate, $submission->created_at);

                $submissionData .=
                    '<tr>
                        <td class="pt-4">' .$timeSpan . '</td>
                        <td class="pt-4">
                            <span>' .$submission->id. '</span><br>
                            <div style="display:none" class="status-time"><div class="border border-dark floar-left p-1 mt-2" style="border-radius: 5px; width: auto"><span style="color:#AC5BAD; font-weight:bold;">'.date('m/d h:i A', strtotime($submission->updated_at)).'</span></div></div>
                        </td>
                        <td>
                            <div class="a-center pt-2 pl-2 pb-2 pr-2 '. $candidateCss.'" style="width: fit-content;"><span class="'.$candidateClass.' candidate" style="'.$candidateBorderCss.'">'. $candidateName. '</span></div>
                        </td>
                        <td class="pt-4">'. $submission->location .'</td>
                        <td class="pt-4">'. $submission->Recruiters->name .'</td>
                        <td class="pt-4">'. $submission->recruiter_rate .'</td>
                        <td class="pt-4">'. $submission->employer_name .'</td>
                        <td class="pt-4">
                            <span>' .(isset($bdmStatus[$submission->status]) ? $bdmStatus[$submission->status]  :''). '</span><br>
                            <span>'.$submission->reason.'</span>
                            '. getEntityLastUpdatedAtHtml($entityTypeBdm, $submission->id) .'
                        </td>
                        <td class="pt-4">
                            <span>' .(isset($pvStatus[$submission->pv_status]) ? $pvStatus[$submission->pv_status] :''). '</span><br>
                            <span>'.$submission->pv_reason.'</span>
                            '.getEntityLastUpdatedAtHtml($entityTypePv, $submission->id).'
                        </td>
                        <td class="pt-4">
                            <span>' .$interviewModel->getInterviewStatusBasedOnSubmissionIdAndJobId($submission->id, $submission->Requirement->job_id).'</span><br>
                            '.getEntityLastUpdatedAtHtml($entityTypeInterview, $submission->id).'
                        </td>
                    </tr>';
            }
        }else{
            $submissionData .=
                '<tbody>
                    <tr>
                        <td colspan="10" class="text-center">No Records Found</td>
                    </tr>
                </tbody>
            </table>';
        }
        $data['status'] = 1;
        $data['submissionHeadingData'] = $submissionHeadingData;
        $data['submissionHeaderData'] = $submissionHeaderData;
        $data['submissionData'] = $submissionData;

        return $data;
    }
}
