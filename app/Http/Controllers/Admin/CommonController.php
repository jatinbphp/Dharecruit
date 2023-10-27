<?php

namespace App\Http\Controllers\Admin;

use App\Models\Submission;
use App\Models\Requirement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CommonController extends Controller
{
    public function getCandidate(Request $request){
        $submission = Submission::where('id',$request->cId)->first();
        if(!empty($submission)){
            if($submission->requirement->user_id == Auth::user()->id){
                $submission->update(['is_show' => 1]);
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
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <strong>Pv Company:</strong> '.$submission['Requirement']['pv_company_name'].'
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
                                            <td align="center" colspan="8">No History Found.</td>
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
        if(!empty($requirement)){
            $status = 1;
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
                    <strong>Visa:</strong> '.$requirement->visa.'
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
                    <strong>MOI:</strong> '.$requirement->MOI->name.'
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
}
