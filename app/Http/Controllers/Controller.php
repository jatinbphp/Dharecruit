<?php

namespace App\Http\Controllers;

use App\Models\Requirement;
use App\Models\Submission;
use App\Models\Interview;
use App\Models\EntityHistory;
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

        if(!empty($request->date)){
            $date = explode('-',$request->date);
            $dateS = date('Y-m-d', strtotime($date[0]));
            $dateE = date('Y-m-d', strtotime($date[1]));
            $query->whereBetween('created_at', [$dateS." 00:00:00", $dateE." 23:59:59"]);
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

            if($user->id == $userId || $user->role == 'admin'){
                if($submission->is_show == 0){
                    $textColor = 'text-primary';
                    $divClass .= 'border border-primary';
                } else{
                    $interviewStatus = $this->getInterviewStatus($submission, $row);
                    if($interviewStatus){
                        $divCss = "width: fit-content;";
                        if($interviewStatus == $interviewModel::STATUS_SCHEDULED){
                            $divClass .= 'border border-warning rounded-pill';
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
                            $textColor = 'text-dange';
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
            $nameArray = explode(" ",$submission->name);
            $candidateFirstName = isset($nameArray[0]) ? $nameArray[0] : '';
            $candidateLastDate = ($this->getCandidateLastStatusUpdatedAt($submission)) ? date('d/m h:i A', strtotime($this->getCandidateLastStatusUpdatedAt($submission))) : ''; 
            $candidate .= '<div class="'.$divClass.'" style="'.$divCss.'"><span class="candidate '.$textColor.' candidate-'.$submission->id.'" id="candidate-'.$submission->id.'" style="'.$css.'" data-cid="'.$submission->id.'">'.$candidateFirstName.' - '.$submission->id.'</span></div><span style="color:#AC5BAD; font-weight:bold; display:none" class="submission-date">'.$candidateLastDate.'</span>';
        }
        return $candidate;
    }

    public function getCandidateCss($submission,$checkUser = false) {
        $userId = $submission->requirement->user_id;
        $user = Auth::user();
        $submissionModel = new Submission();

        if($user->id == $userId || $user->role == 'admin' || $checkUser){
            if($submission->pv_status == $submissionModel::STATUS_NO_RESPONSE_FROM_PV){
                return "solid;";
            } else if($submission->pv_status == $submissionModel::STATUS_SUBMITTED_TO_END_CLIENT){
                return "solid;";
            }else if($submission->pv_status == $submissionModel::STATUS_REJECTED_BY_END_CLIENT){
                return "6px double;";
            }else if($submission->pv_status == $submissionModel::STATUS_REJECTED_BY_PV){
                return "solid;";
            }
        }
        return '';
    }

    public function getCandidateClass($submission,$checkUser = false) {
        $userId = $submission->requirement->user_id;
        $user = Auth::user();
        $submissionModel = new Submission();

        if($user->id == $userId || $user->role == 'admin' || $checkUser){
            if($submission->is_show == 0){
                return 'text-primary';
            } else{
                if(!empty($submission->pv_status) && $submission->pv_status){
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
                    }
                }
            }
        }
        return '';
    }

    function getInterviewStatus($submission, $row) {
        $jobId = $row->job_id;
        $submissionId = $submission->id;

        
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
}
