<?php

namespace App\Http\Controllers;

use App\Models\Requirement;
use App\Models\Submission;
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
        foreach ($submissions as $submission){
            $textColor = '';
            $css = '';
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
                } else{
                    if(!empty($submission->pv_status) && $submission->pv_status){
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
                            $textColor = 'text-danger seconda';
                            $css = "border-bottom: solid;";
                        }
                    } else {
                        if($submission->status == $submissionModel::STATUS_REJECTED){
                            $textColor = 'text-danger';
                        } elseif($submission->status == $submissionModel::STATUS_ACCEPT){
                            $textColor = 'text-success';
                        }
                    }
                }
            }
            $nameArray = explode(" ",$submission->name);
            $candidateFirstName = isset($nameArray[0]) ? $nameArray[0] : '';
            $candidate .= '<span class="candidate '.$textColor.'" style="'.$css.'" data-cid="'.$submission->id.'">'.$candidateFirstName.' - '.$submission->id.'</span><br>';
        }
        return $candidate;
    }
}
