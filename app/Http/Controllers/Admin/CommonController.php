<?php

namespace App\Http\Controllers\Admin;

use App\Models\Submission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommonController extends Controller
{
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
                        </div>
                        <div class="row mt-2">
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
}
