<?php

namespace App\Http\Controllers\Admin;

use \Carbon\Carbon;
use App\Models\Submission;
use App\Models\Requirement;
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
                                <strong>Last 4 SSN:</strong> '.$submission['last_4_ssn'].'
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <strong>Education Detail:</strong> '.$submission['education_details'].'
                            </div>
                            <div class="col-md-6">
                                <strong>Resume Experience:</strong> '.$submission['resume_experience'].'
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <strong>Linkedin Id:</strong> '.$submission['linkedin_id'].'
                            </div>
                            <div class="col-md-6">
                                <strong>Vendor Rate:</strong> '.$submission['vendor_rate'].'
                            </div>
                        </div>';
                        if(Auth::user()->role == 'bdm'){
                            $cData .= '<div class="row mt-2">
                                            <div class="col-md-6">
                                                <strong>Employer Name:</strong> '.$submission['employer_name'].'
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Resume:</strong><a href="'.asset('storage/'.$submission['documents']).'" target="_blank"><img src="'.url('assets/dist/img/resume.png').'" height="50"></a>
                                            </div>
                                        </div>';
                        } else {
                            $cData .= '<div class="row mt-2">
                                            <div class="col-md-6">
                                                <strong>Employer Name:</strong> '.$submission['employer_name'].'
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
                            <th scope="col">BDM Status</th>
                            <th scope="col">PV Status</th>
                            <th scope="col">Employer</th>
                            <th scope="col">Download Resume</th>
                        </tr>
                    </thead>
                    <tbody>';
                    if(!count($candidateHistory)){
                        $historyData .= '<tr>
                                            <td align="center" colspan="9">No History Found.</td>
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
                                                <td>'.date('m-d-Y',strtotime($candidateData->created_at)).'</td>
                                                <td>'.$candidateData->requirement->job_id.'</td>
                                                <td>'.$candidateData->requirement->job_title.'</td>
                                                <td>'.$candidateData->requirement->BDM->name.'</td>
                                                <td>'.$candidateData->recruiters->name.'</td>
                                                <td>'.ucfirst($candidateData->status).'</td>
                                                <td>'.$pvStatus.'</td>
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
        }
        $data['requirementData'] = $rData;
        $data['candidateData'] = $cData;
        $data['submission'] = $submission;
        $data['candidateStatus'] = $candidateStatus;
        $data['status'] = $status;
        $data['historyData'] = $historyData;
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
        if(!empty($requirement)){
            $status = 1;
            $user = Auth::user();
            if($user->role == 'recruiter'){
                $isShowRecruiters = array_filter(explode(',', $requirement->is_show_recruiter));
                if(!in_array($user->id, $isShowRecruiters)){
                    array_push($isShowRecruiters, $user->id);
                    $requirement->is_show_recruiter = implode(',', $isShowRecruiters);
                    $requirement->save();
                    $data['is_show_requirement'] = 1;    
                }
            }
            $requirementTitle = $requirement->job_title;
            $requirementContent .= '
            <div class="row">
                <div class="col-md-6">
                    <strong>Job Title:</strong> '.$requirement->job_title.'
                </div>
                <div class="col-md-6">
                    <strong>No # Position:</strong> '.$requirement->no_of_position.'
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <strong>Experience:</strong> '.$requirement->experience.'
                </div>
                <div class="col-md-6">
                    <strong>Location:</strong> '.$requirement->location.'
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <strong>Onsite/Hybrid/Remote:</strong> '.$requirement->work_type.'
                </div>
                <div class="col-md-6">
                    <strong>Duration:</strong> '.$requirement->duration.'
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <strong>Visa:</strong> '.Requirement::getVisaNames($requirement->visa) .'
                </div>
                <div class="col-md-6">
                    <strong>Client:</strong> '.$requirement->client.'
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <strong>Priority:</strong> '.$requirement->priority.'
                </div>
                <div class="col-md-6">
                    <strong>Term:</strong> '.$requirement->term.'
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <strong>Category:</strong> '.$requirement->Category->name.'
                </div>
                <div class="col-md-6">
                    <strong>MOI:</strong> '.Requirement::getMoiNames($requirement->moi).'
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <strong>Job Keyword:</strong> '.$requirement->job_keyword.'
                </div>
                <div class="col-md-6">
                    <strong>Special Notes:</strong> '.$requirement->notes.'
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <strong>Job Description:</strong>
                </div>
                <div class="col-md-12">
                    '.$requirement->description.'
                </div>
            </div>';
        }
        $data['status']             = $status;
        $data['requirementContent'] = $requirementContent;
        $data['requirementTitle']   = $requirementTitle;
        $data['requirememt']        = $requirement;

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
                    <th scope="col">BDM Status</th>
                    <th scope="col">Vendor Status</th>
                </tr>
            </thead>
        ';
        
        $requirementCreatedDate = Carbon::parse($requirement->created_at);
        
        if(!empty($submissions) && count($submissions)){
            $submissionModel = new Submission();
            $entityModel     = new EntityHistory();
            $bdmStatus       = $submissionModel::$status;
            $pvStatus        = $submissionModel::$pvStatus;
            $entityTypeBdm   = $entityModel::ENTITY_TYPE_BDM_STATUS;
            $entityTypePv    = $entityModel::ENTITY_TYPE_PV_STATUS;
            foreach($submissions as $submission){
                $candidateClass = $this->getCandidateClass($submission,true);
                $candidateCss   = $this->getCandidateCss($submission,true);
                $candidateBorderCss = $this->getCandidateBorderCss($submission);
                $candidateNames = explode(' ',$submission->name);
                $candidateName = isset($candidateNames[0]) ? $candidateNames[0] : '';
                $timeSpan = '';
                
                $submissionCreatedDate  = Carbon::parse($submission->created_at);
                $timeSpan = '';

                // Calculate the difference in hours and minutes
                $diffInHours   = $requirementCreatedDate->diffInHours($submissionCreatedDate);
                $diffInMinutes = $requirementCreatedDate->diffInMinutes($submissionCreatedDate) % 60;

                if ($diffInHours >= 24) {
                    // If the difference is more than 24 hours
                    $diffInDays = floor($diffInHours / 24);
                    $diffInHours = $diffInHours % 24;

                    $timeSpan = "$diffInDays days, $diffInHours hr : $diffInMinutes mins";
                } else {
                    if($diffInHours > 1){
                        // If the difference is less than 24 hours
                        $timeSpan = "$diffInHours hr:$diffInMinutes mins";
                    }else{
                        // If the difference is less than 1 hours
                        $timeSpan = "$diffInMinutes mins";
                    }
                }

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
                        <td class="pt-4">
                            <span>' .(isset($bdmStatus[$submission->status]) ? $bdmStatus[$submission->status]  :''). '</span><br>
                            '. getEntityLastUpdatedAtHtml($entityTypeBdm, $submission->id) .'
                        </td>
                        <td class="pt-4">
                            <span>' .(isset($pvStatus[$submission->pv_status]) ? $pvStatus[$submission->pv_status] :''). '</span><br>
                            '.getEntityLastUpdatedAtHtml($entityTypePv, $submission->id).'
                        </td>
                    </tr>';
            }
        }else{
            $submissionData .= 
                '<tbody>
                    <tr>
                        <td colspan="8" class="text-center">No Records Found</td>
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
