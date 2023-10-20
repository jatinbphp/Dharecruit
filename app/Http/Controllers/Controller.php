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
        return $query->where($whereInfo);
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
}
