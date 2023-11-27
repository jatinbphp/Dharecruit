<?php

namespace App\Http\Controllers;

use App\Models\Requirement;
use App\Models\Submission;
use App\Models\Interview;
use App\Models\EntityHistory;
use App\Models\Setting;
use App\Models\DataLog;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Orders;
use App\Models\OrderItems;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function fileMove($photo, $path){
        $root = storage_path('app/public/uploads/'.$path);
        $filename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
        $name = $filename."_".date('His',time()).".".$photo->getClientOriginalExtension();
        if (!file_exists($root)) {
            mkdir($root, 0777, true);
        }
        $photo->move($root,$name);
        return 'uploads/'.$path."/".$name;
    }

    public function getUser(){
        return Auth::user();
    }

    public function Filter($request){
        $whereInfo = [];

        $user = Auth::user();

        if($user['role'] == 'bdm' && isset($request->authId) && $request->authId > 0){
            $query = Requirement::where('user_id',$request->authId)->select();
        }elseif($user['role'] == 'recruiter' && isset($request->authId) && $request->authId > 0){
            $query = Requirement::whereRaw("find_in_set($request->authId,recruiter)")->select();
        }else{
            $query = Requirement::select();
        }

        if(!empty($request->fromDate)){
            $fromDate = date('Y-m-d', strtotime($request->fromDate));
            $query->where('created_at', '>=' ,$fromDate." 00:00:00");
        }

        if(!empty($request->toDate)){
            $toDate = date('Y-m-d', strtotime($request->toDate));
            $query->where('created_at', '<=' ,$toDate." 23:59:59");
        }

        if(!empty($request->requirement)){
            $whereInfo[] = ['job_title', 'like', '%'.$request->requirement.'%'];
        }

        if(!empty($request->bdm)){
            $whereInfo[] = ['bdm', $request->bdm];
        }

        if(!empty($request->recruiter)){
            $whereInfo[] = ['recruiter', 'like', '%,'.$request->recruiter.',%'];
        }

        if(!empty($request->poc_email)){
            $whereInfo[] = ['poc_email', 'like', '%,'.$request->poc_email.',%'];
        }

        if(!empty($request->pv_company)){
            $whereInfo[] = ['pv_company_name', $request->pv_company];
        }

        if(!empty($request->moi)){
            $whereInfo[] = ['moi', $request->moi];
        }

        if(!empty($request->work_type)){
            $whereInfo[] = ['work_type', $request->work_type];
        }

        if(!empty($request->show_merge) && $request->show_merge == 1){
            return $query->where($whereInfo)->orderBy('parent_requirement_id', 'DESC')->orderBy('id', 'desc');
        }

        return $query->where($whereInfo)->orderBy('id', 'desc');
    }

    public function submissionFilter($request,$id){
        $whereInfo = [];

        $user = $this->getUser();

        if($user['role'] == 'admin'){
            $query = Submission::where('requirement_id',$id)->select();
        }else{
            $query = Submission::where('user_id',$user['id'])->where('requirement_id',$id)->select();
        }

        if(!empty($request->candidateId)){
            $whereInfo[] = ['id', $request->candidateId];
        }

        if(!empty($request->candidateEmail)){
            $whereInfo[] = ['email', 'like', '%'.$request->candidateEmail.'%'];
        }

        return $query->where($whereInfo);
    }

    public function getCandidateHtml($submissions, $row, $page = 'requirement') {
        $user = Auth::user();
        $candidate = '';
        $submissionModel = new Submission();
        $interviewModel  = new Interview();

        foreach ($submissions as $submission){
            $textColor = '';
            $css = '';
            $divClass = 'a-center pt-2 pl-2 pb-2 pr-2 mt-2 ';
            $divCss = '';
            $userId = $row->user_id;
            if($page == 'submission') {
                $userId = $submission->user_id;
            }

            if($page == 'my_submission'){
                $userId = $submission->requirement->user_id;
            }
            
            $isSamePvCandidate = $this->isSamePvCandidate($submission->email, $submission->requirement_id, $submission->id);

            if($user->id == $userId || $user->role == 'admin'){
                if($submission->is_show == 0){
                    $textColor = 'text-primary';
                    $divClass .= 'border border-primary';
                } else{
                    $interviewStatus = $this->getInterviewStatus($submission->id, $row->job_id);
                    if($interviewStatus){
                        $divCss = "width: fit-content;";
                        if($interviewStatus == $interviewModel::STATUS_SCHEDULED){
                            $divClass .= 'border border-warning rounded-pill';
                            $textColor = 'text-dark';
                        } else if($interviewStatus == $interviewModel::STATUS_RE_SCHEDULED){
                            $divClass .= 'border-warning-10 rounded-pill';
                            $textColor = 'text-dark';
                        } else if($interviewStatus == $interviewModel::STATUS_SELECTED_FOR_NEXT_ROUND){
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
                    } else if(!empty($submission->pv_status) && $submission->pv_status){
                        if($submission->pv_status == $submissionModel::STATUS_NO_RESPONSE_FROM_PV){
                            $css = "border-bottom: solid;";
                            $textColor = 'text-secondary';
                        } else if($submission->pv_status == $submissionModel::STATUS_SUBMITTED_TO_END_CLIENT){
                            $css = "border-bottom: solid;";
                            $textColor = 'text-success test';
                        }else if($submission->pv_status == $submissionModel::STATUS_REJECTED_BY_END_CLIENT){
                            $css = "border-bottom: 6px double;";
                            $textColor = 'text-danger';
                        }else if($submission->pv_status == $submissionModel::STATUS_REJECTED_BY_PV){
                            $textColor = 'text-danger';
                            $css = "border-bottom: solid;";
                        }
                    } else {
                        if($submission->status == $submissionModel::STATUS_REJECTED){
                            $textColor = 'text-danger';
                        } elseif($submission->status == $submissionModel::STATUS_ACCEPT){
                            $textColor = 'text-success';
                        } elseif($submission->is_show == 1) {
                            $textColor = 'text-primary';
                        }
                    }
                }
            }
            $nameArray          = explode(" ",$submission->name);
            $candidateFirstName = isset($nameArray[0]) ? $nameArray[0] : '';
            $candidateLastDate  = ($this->getCandidateLastStatusUpdatedAt($submission)) ? date('m/d h:i A', strtotime($this->getCandidateLastStatusUpdatedAt($submission))) : ''; 
            $candidateCount     = $this->getCandidateCountByEmail($submission->email);
            $latestJobIdOfMatchPvCompany = $this->getLatestJobIdOfMatchPvCompany($submission->email);
            $isCandidateHasLog  = $this->isCandidateHasLog($submission);

            if($user->id == $userId && $user->role == 'recruiter'){
                $candidate .= (($candidateCount) ? "<span class='badge bg-indigo position-absolute top-0 start-100 translate-middle'>$candidateCount</span>" : "").(($isCandidateHasLog) ? "<span class='badge badge-pill badge-primary ml-4 position-absolute top-0 start-100 translate-middle'>L</span>" : "").'<div onClick="showUpdateSubmissionModel('.$submission->id.')" class="'.$divClass.'" style="'.$divCss.'"><span class="candidate '.$textColor.' candidate-'.$submission->id.'" id="candidate-'.$submission->id.'" style="'.$css.'" data-cid="'.$submission->id.'">'.($isSamePvCandidate ? "<i class='fa fa-info'></i>  ": "").$candidateFirstName.'-'.$submission->candidate_id.'</span></div><span style="color:#AC5BAD; font-weight:bold; display:none" class="submission-date">'.$candidateLastDate.'</span><br>';
            } else {
                if(($user->id == $userId && $user->role == 'bdm') || $user->role == 'admin'){
                    $class = 'candidate';
                } else {
                    $class = '';
                }
                $candidate .= (($candidateCount) ? "<span class='badge bg-indigo position-absolute top-0 start-100 translate-middle'>$candidateCount</span>" : "").(($isCandidateHasLog) ? "<span class='badge badge-pill badge-primary ml-4 position-absolute top-0 start-100 translate-middle'>L</span>" : "").'<div class="'.$divClass.'" style="'.$divCss.'"><span class="'.$class.' '.$textColor.' candidate-'.$submission->id.'" id="candidate-'.$submission->id.'" style="'.$css.'" data-cid="'.$submission->id.'">'.($isSamePvCandidate ? "<i class='fa fa-info'></i> " :"").$candidateFirstName.'-'.$submission->candidate_id.'</span></div><span style="color:#AC5BAD; font-weight:bold; display:none" class="submission-date">'.$candidateLastDate.'</span><br>';
            }
        }
        return $candidate;
    }

    public function getCandidateCss($submission,$checkUser = false) {
        $userId = $submission->requirement->user_id;
        $user = Auth::user();
        $submissionModel = new Submission();
        $interviewModel  = new Interview();

        if($user->id == $userId || $user->role == 'admin' || $checkUser){
            if($submission->is_show == 0){
                return 'border border-primary';
            } else {
                $interviewStatus = $this->getInterviewStatus($submission->id, $submission->Requirement->job_id);
                if($interviewStatus){
                    //$divCss .= "width: fit-content;";
                    if($interviewStatus == $interviewModel::STATUS_SCHEDULED){
                        return 'border border-warning rounded-pill';
                    } else if($interviewStatus == $interviewModel::STATUS_RE_SCHEDULED){
                        return 'border-warning-10 rounded-pill';
                    } else if($interviewStatus == $interviewModel::STATUS_SELECTED_FOR_NEXT_ROUND){
                        return 'bg-warning rounded-pill';
                    } else if($interviewStatus == $interviewModel::STATUS_CONFIRMED_POSITION){
                        return 'bg-success';
                    } else if($interviewStatus == $interviewModel::STATUS_REJECTED){
                        return 'bg-danger';
                    } else if($interviewStatus == $interviewModel::STATUS_BACKOUT){
                        return 'bg-dark';
                    }
                } else if($submission->pv_status == $submissionModel::STATUS_NO_RESPONSE_FROM_PV){
                    return "solid;";
                } else if($submission->pv_status == $submissionModel::STATUS_SUBMITTED_TO_END_CLIENT){
                    return "solid;";
                }else if($submission->pv_status == $submissionModel::STATUS_REJECTED_BY_END_CLIENT){
                    return "6px double;";
                }else if($submission->pv_status == $submissionModel::STATUS_REJECTED_BY_PV){
                    return "solid;";
                }
            }
        }
        return '';
    }

    public function getCandidateBorderCss($submission){
        $interviewStatus = $this->getInterviewStatus($submission->id, $submission->Requirement->job_id);
        if(!$interviewStatus && $submission->is_show == 1 && !empty($submission->pv_status) && $submission->pv_status){
            $submissionModel = new Submission();
            if($submission->pv_status == $submissionModel::STATUS_NO_RESPONSE_FROM_PV){
                return "border-bottom: solid;";
            } else if($submission->pv_status == $submissionModel::STATUS_SUBMITTED_TO_END_CLIENT){
                return "border-bottom: solid;";
            }else if($submission->pv_status == $submissionModel::STATUS_REJECTED_BY_END_CLIENT){
                return "border-bottom: 6px double;";
            }else if($submission->pv_status == $submissionModel::STATUS_REJECTED_BY_PV){
                return "border-bottom: solid;";
            }
        }
        return '';
    }

    public function getCandidateClass($submission,$checkUser = false) {
        $userId = $submission->requirement->user_id;
        $user = Auth::user();
        $submissionModel = new Submission();
        $interviewModel  = new Interview();

        if($user->id == $userId || $user->role == 'admin' || $checkUser){
            if($submission->is_show == 0){
                return 'text-primary';
            } else {
                $interviewStatus = $this->getInterviewStatus($submission->id, $submission->Requirement->job_id);
                if($interviewStatus){
                    if(in_array($interviewStatus, [$interviewModel::STATUS_SCHEDULED, $interviewModel::STATUS_RE_SCHEDULED, $interviewModel::STATUS_SELECTED_FOR_NEXT_ROUND, $interviewModel::STATUS_CONFIRMED_POSITION])){
                        return 'text-dark';
                    } else if(in_array($interviewStatus, [$interviewModel::STATUS_REJECTED, $interviewModel::STATUS_BACKOUT])){
                        return 'text-white';
                    }
                } else if(!empty($submission->pv_status) && $submission->pv_status){
                    if($submission->pv_status == $submissionModel::STATUS_NO_RESPONSE_FROM_PV){
                        return 'text-secondary';
                    } else if($submission->pv_status == $submissionModel::STATUS_SUBMITTED_TO_END_CLIENT){
                        return 'text-success test';
                    }else if($submission->pv_status == $submissionModel::STATUS_REJECTED_BY_END_CLIENT){
                        return 'text-danger';
                    }else if($submission->pv_status == $submissionModel::STATUS_REJECTED_BY_PV){
                        return 'text-danger seconda';
                    }
                } else {
                    if($submission->status == $submissionModel::STATUS_REJECTED){
                        return 'text-danger';
                    } elseif($submission->status == $submissionModel::STATUS_ACCEPT){
                        return 'text-success';
                    } elseif($submission->is_show == 1) {
                        return 'text-primary';
                    }
                }
            }
        }
        return '';
    }

    public function getInterviewStatus($submissionId, $jobId) {
        if(!$jobId || !$submissionId){
            return '';
        }

        $statusData = Interview::where('submission_id', $submissionId)->where('job_id', $jobId)->first(['status']);
        if(!$statusData || !$statusData->status){
            return '';
        }

        return $statusData->status;
    }

    public function getCandidateLastStatusUpdatedAt($submission){
        $submissionId = $submission->id;

        if(!$submissionId){
            return '';
        }

        $statuslastUpdatedAt =  EntityHistory::whereIn('entity_type',[EntityHistory::ENTITY_TYPE_PV_STATUS,EntityHistory::ENTITY_TYPE_BDM_STATUS])->where('submission_id',$submissionId)->orderBy('id','DESC')->first(['created_at']); 
        if(empty($statuslastUpdatedAt) || !$statuslastUpdatedAt->created_at){
            return '';
        }
        return $statuslastUpdatedAt->created_at;
    }

    public function getSettingData() {
        return Setting::pluck('value','name')->toArray();
    }

    public function isSamePvCandidate($submissionEmail, $requirementId, $submissionId = 0){
        if(!$submissionEmail || !$requirementId){
            return 0;
        }

        $requirementIdsWithCurrentEmail = Submission::where('email', $submissionEmail)->pluck('requirement_id')->toArray();

        if($submissionId){
            $requirementIdsWithCurrentEmail = Submission::where('email', $submissionEmail)->where('id','!=',$submissionId)->pluck('requirement_id')->toArray();
        }

        if(!$requirementIdsWithCurrentEmail || !count($requirementIdsWithCurrentEmail)){
            return 0;
        }

        $currentRequirement = Requirement::where('id', $requirementId)->first();
        if(!$currentRequirement){
            return 0;
        }
        $currentRequirementPvCompany = $currentRequirement->pv_company_name;

        $samePvCompanyCandidate = Requirement::whereIn('id',$requirementIdsWithCurrentEmail)->where('pv_company_name',$currentRequirementPvCompany)->first();
        
        if(empty($samePvCompanyCandidate)){
            return 0;
        }
    
        return 1;
    }
    
    public function getCandidateCountByEmail($email){
        if(!$email){
            return 0;
        }
        $candidateCount = Submission::where('email', $email)->groupBy('email')->count();

        return $candidateCount > 1 ? $candidateCount : 0;
    }

    public function manageSubmissionLogs($newData, $oldData) {
        if(!$newData || !$oldData){
            return $this;
        }

        $differentValues = [];
        $isAllSameData = 0;

        foreach ($newData as $key => $value) {
            if(in_array($key,['created_at','updated_at'])){
                continue;
            }
            if(is_array($value)){
                $value = ','. implode(',',$value).',';
            }
            if($value == 'on'){
                $value = '1';
            }elseif($value == 'off'){
                $value = '0';
            }
            if (isset($oldData[$key]) && $oldData[$key] != $value) {
                $isAllSameData = 1;
                $differentValues[$key] = $oldData[$key];
            } else {
                $differentValues[$key] = '';
            }
        }
        if($isAllSameData){
            $this->saveDataLog($oldData->id, DataLog::SECTION_SUBMISSION, $differentValues,$oldData);
        }

        return $this;
    }

    public function saveDataLog($sectionId,$section,$data,$submission){
        $cloneSubmission = $submission->replicate();
        if(!$sectionId || !$section || !$data){
            return $this;
        }

        $inputData['section_id']   = $sectionId;
        $inputData['section']      = $section;
        $inputData['candidate_id'] = $cloneSubmission->candidate_id;
        $inputData['job_id']       = $cloneSubmission->Requirement->job_id;
        $inputData['user_id']      = Auth::user()->id;
        $inputData['data']         = json_encode($data);

        DataLog::create($inputData);
        return $this;
    }

    public function getLatestJobIdOfMatchPvCompany($email){
        if(!$email){
            return '';
        }

        $submission = Submission::where('email', $email)->orderBy('created_at','DESC')->first();
        
        if(!empty($submission) && $submission->Requirement->job_id){
            return $submission->Requirement->job_id;
        }
        return '';
    }

    public function getLogDataByName($submission, $key){
        if(!$submission || !$key || !in_array($key, Submission::$manageLogFileds)){
            return '';
        }

        $allLogData = DataLog::where('candidate_id', $submission->candidate_id)->where('section', DataLog::SECTION_SUBMISSION)->orderBy('created_at', 'DESC')->get();

        if(empty($allLogData) || !count($allLogData)){
            return '';
        }
        
        $logData = 
            '<br><table class="table table-striped log-data" style="display:none">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Value</th>
                        <th scope="col">JID</th>
                        <th scope="col">Modified By</th>
                        <th scope="col">Date</th>
                    </tr>
                </thead>
                <tbody>';
        $count = 1;
        $isNoLogData = 1;
        $logData .= 
            '<tr>
                <th scope="row" class="text-success">'.$count.'</th>
                <td class="text-success">'.(isset($submission[$key]) ? $submission[$key]:'').'</td>
                <td class="text-success">'.$submission->Requirement->job_id.'</td>
                <td class="text-success">'.$submission->recruiters->role.' : '.$submission->recruiters->name.'</td>
                <td class="text-success">'.date('m-d-y',strtotime($submission->created_at)).'</td>
            </tr>';
        foreach($allLogData as $data){
            $allData = json_decode($data->data, 1);
            if(isset($allData[$key]) && $allData[$key]){
                $isNoLogData = 0;
                    $count++;
                    $logData .= 
                    '<tr>
                        <th scope="row" class="text-danger">'.$count.'</th>
                        <td class="text-danger">'.$allData[$key].'</td>
                        <td class="text-danger">'.$data->job_id.'</td>
                        <td class="text-danger">'.$data->userDetail->role.' : '.$data->userDetail->name.'</td>
                        <td class="text-danger">'.date('m-d-y',strtotime($data->created_at)).'</td>
                    </tr>';
            }
        }

        if($isNoLogData){
            return '';
        }

        $logData .= 
                '</tbody>
            </table>';

        return $logData;
    }

    public function isCandidateHasLog($submission) {
        if(empty($submission)){
            return 0;
        }

        $manageLogFileds = Submission::$manageLogFileds;

        if(empty($manageLogFileds) || !count($manageLogFileds)){
            return 0;
        }

        $isHasLog = 0;
        foreach ($manageLogFileds as $value) {
            $oldLogData = $this->getLogDataByName($submission, $value);
            if($oldLogData){
                $isHasLog = 1;
                break;
            }
        }

        if(!$oldLogData){
            return 0;
        }

        return 1;
    }

    public function updateCandidateWithSameCandidateId($submission){
        if(empty($submission)){
            return $this;
        }
        $submissionData = $submission->toArray();
        unset($submissionData['id']);
        unset($submissionData['requirement_id']);
        unset($submissionData['user_id']);
        unset($submissionData['email']);
        unset($submissionData['common_skills']);
        unset($submissionData['skills_match']);
        unset($submissionData['reason']);
        unset($submissionData['status']);
        unset($submissionData['pv_status']);
        unset($submissionData['pv_reason']);
        unset($submissionData['is_show']);
        unset($submissionData['deleted_at']);
        unset($submissionData['created_at']);
        unset($submissionData['updated_at']);
        $candidateId = $submission->candidate_id;
        if($candidateId){
            $oldSubmissionRow = Submission::where('candidate_id',$candidateId)->first();
            Submission::where('candidate_id', $candidateId)->update($submissionData);
            $this->manageSubmissionLogs($submission->toArray(),$oldSubmissionRow);
        }
        return $this;
    }
}
