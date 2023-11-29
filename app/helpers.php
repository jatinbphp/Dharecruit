<?php

use App\Models\Admin;
use App\Models\Submission;
use App\Models\Requirement;
use App\Models\EntityHistory;
use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Support\Facades\Auth;

if(!function_exists('getListHtml')){
    function getListHtml($data, $page='requirement') {
        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('job_title', function($row){
                return getJobTitleHtml($row);
            })
            ->addColumn('user_id', function($row){
                return $row->BDM->name;
            })
            ->addColumn('category', function($row){
                return $row->Category->name;
            })
            ->addColumn('recruiter', function($row){
                return getRecruiterHtml($row);
            })
            ->addColumn('status', function($row){
                return getStatusHtml($row);
            })
            ->addColumn('candidate', function ($row) use (&$page){
                return getCandidateHtml($row, $page);
            })
            ->addColumn('action', function ($row) use (&$page){
                return getActionHtml($row, $page);
            })
            ->addColumn('client', function($row) {
                return getClientHtml($row);
            })
            ->addColumn('job_keyword', function($row) {
                return getJobKeywordHtml($row);
            })
            ->addColumn('job_id', function($row) {
                return getJobIdHtml($row);
            })
            ->setRowClass(function ($row) {
                return (($row->parent_requirement_id != 0 && $row->parent_requirement_id == $row->id) ? 'parent-row' : (($row->parent_requirement_id != 0) ? 'child-row' : ''));
                ;
            })
            ->rawColumns(['user_id','category','recruiter','status','candidate','action','client','job_title','job_keyword','job_id'])
            ->make(true);
    }
}

if(!function_exists('getJonbTitleHtml')){
    function getJobTitleHtml($row){
        $loggedinUser = Auth::user()->id;
        $isShowRecruiters = explode(',', $row->is_show_recruiter);
        $isShowRecruitersAfterUpdate = explode(',', $row->is_show_recruiter_after_update);
        $textStyle = '';
        if(Auth::user()->role == 'recruiter'){
            if(!in_array($loggedinUser, $isShowRecruiters)){
                $textStyle = 'pt-1 pl-2 pb-1 pr-2 border border-primary text-primary';
            } else if($row->is_update_requirement == 1){
                if(!in_array($loggedinUser, $isShowRecruitersAfterUpdate) && in_array($loggedinUser, $isShowRecruiters)){
                    $textStyle = 'pt-1 pl-2 pb-1 pr-2 border border-warning text-warning';
                }
            }
        }
        return '<div class="'.$textStyle.' job-title job-title-'.$row->id.'" data-id="'.$row->id.'"><span class="font-weight-bold">'.$row->job_title.'</span></div>';
    }
}

if(!function_exists('getRecruiterHtml')){
    function getRecruiterHtml($row){
        $recruiterIds = !empty($row->recruiter) ? explode(',',$row->recruiter) : [];
        if(!count($recruiterIds)){
            return '';
        }
        $recName = '';

        $user = Auth::user();

        if($user->role == 'recruiter'){
            if(in_array($user->id, $recruiterIds)){
                $recruiterIds = array_flip($recruiterIds);
                unset($recruiterIds[ $user->id ]);
                $recruiterIds = array_filter(array_flip($recruiterIds));
                sort($recruiterIds);
                array_unshift($recruiterIds,$user->id);    
            }
        } else {
            $recruiterIds = array_filter($recruiterIds);
            sort($recruiterIds);
        }


        foreach ($recruiterIds as $recruiterId){
            $recruterUser = Admin::where('id',$recruiterId)->first();
            if(empty($recruterUser)){
                continue;
            }
            $bgColor = '';
            if($user->id == $recruterUser->id){
                $bgColor = '#BED8E2';
            }
            $submission = Submission::where('user_id',$recruiterId)->where('requirement_id',$row->id)->count();
            $recName .= '<div class="border border-dark floar-left p-1 mt-2" style="
                border-radius: 5px; width: auto; background-color:'.$bgColor.'"><span>'. $submission.' '.$recruterUser['name']. '</span></div>';
        }
        return $recName;
    }
}

if(!function_exists('getStatusHtml')){
    function getStatusHtml($row){
        $statusBtn = '';
        $user = Auth::user();
        if($user['role'] == 'admin' || $user['id'] == $row->user_id){
            if ($row->status == "hold") {
                $statusBtn .= '<div class="btn-group-horizontal" id="assign_remove_"'.$row->id.'">
                                    <button class="btn btn-danger unassign ladda-button" data-style="slide-left" id="remove" url="'.route('requirement.unassign').'" ruid="'.$row->id.'" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Hold</span> </button>
                                </div>';
                $statusBtn .= '<div class="btn-group-horizontal" id="assign_add_"'.$row->id.'"  style="display: none"  >
                                    <button class="btn btn-success assign ladda-button" data-style="slide-left" id="assign" uid="'.$row->id.'" url="'.route('requirement.assign').'" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Need</span></button>
                                </div>';
            }
            if ($row->status == "unhold") {
                $statusBtn .= '<div class="btn-group-horizontal" id="assign_add_"'.$row->id.'">
                                    <button class="btn btn-success assign ladda-button" id="assign" data-style="slide-left" uid="'.$row->id.'" url="'.route('requirement.assign').'" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Need</span></button>
                                </div>';
                $statusBtn .= '<div class="btn-group-horizontal" id="assign_remove_"'.$row->id.'" style="display: none" >
                                    <button class="btn  btn-danger unassign ladda-button" id="remove" ruid="'.$row->id.'" data-style="slide-left" url="'.route('requirement.unassign').'" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Hold</span></button>
                                </div>';
            }
        }else{
            if ($row->status == "hold") {
                $statusBtn .= '<div class="btn-group-horizontal">
                                    <button class="btn btn-danger noChange ladda-button" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Hold</span></button>
                                </div>';
            }
            if ($row->status == "unhold") {
                $statusBtn .= '<div class="btn-group-horizontal">
                                    <button class="btn btn-success noChange ladda-button" data-style="slide-left" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Need</span></button>
                                </div>';
            }
        }
        if(in_array($row->status,[Requirement::STATUS_EXP_HOLD, Requirement::STATUS_EXP_NEED])){
            $statusBtn .= '<div class="btn-group-horizontal">
                                    <button class="btn btn btn-secondary noChange ladda-button" data-style="slide-left" type="button" style="height:28px; padding:0 12px"><span class="ladda-label"><b>'.(isset(Requirement::$exprieStatus[$row->status]) ? (Requirement::$exprieStatus[$row->status]) : '').'</b></span></button>
                                </div>';
        }
        return $statusBtn;
    }
}

if(!function_exists('getcandidateHtml')){

    function getcandidateHtml($row, $page='requirement'){

        if(Auth::user()->role == 'recruiter'){
            $loggedInRecruterSubmission = Submission::where('user_id', Auth::user()->id)->where('requirement_id',$row->id)->where('status','!=','reject')->orderby('user_id','DESC')->get();
            $notLoggedInRecruterSubmission = Submission::where('user_id', '!=',Auth::user()->id)->where('requirement_id',$row->id)->where('status','!=','reject')->orderby('user_id','ASC')->get();
            $allSubmission = $loggedInRecruterSubmission->merge($notLoggedInRecruterSubmission);
        } else {
            $allSubmission = Submission::where('requirement_id',$row->id)->where('status','!=','reject')->orderby('user_id','ASC')->get();
        }

        $candidate = '';
        $controllerObj = new Controller();

        if(count($allSubmission) > 0){
            $candidate .= $controllerObj->getCandidateHtml($allSubmission, $row, $page);
        } else {
            if(!empty($row->recruiter)){
                $candidate .= '<div style="width:50px; background-color: yellow;">&nbsp;</div>';
            }
        }
        return $candidate;
    }
}

if(!function_exists('getActionHtml')){
    function getActionHtml($row, $page='requirement'){
        $exprieStatus = Requirement::$exprieStatus;
        $user = Auth::user();
        $btn = '';
        if($page == 'submission'){
            if($row->submissionCounter < 3){
                $rId = !empty($row->recruiter) ? explode(',',$row->recruiter) : [];
                if(!empty($rId) && in_array(Auth::user()->id,$rId) && !in_array($row->status,$exprieStatus)){
                    //$btn = '<div class="btn-group btn-group-sm mr-2"><a href="'.url('admin/submission/'.$row->id).'"><button class="btn btn-sm btn-default tip" data-toggle="tooltip" title="View Submission" data-trigger="hover" type="submit" ><i class="fa fa-eye"></i></button></a></div>';
                    $btn = '<div class="btn-group btn-group-sm mr-2"><button class="btn btn-sm btn-default tip view-submission" data-toggle="tooltip" title="View Submission" data-trigger="hover" type="submit" data-id="'.$row->id.'" ><i class="fa fa-eye"></i></button></div>';
                    $btn .= '<div class="btn-group btn-group-sm"><a href="'.url('admin/submission/new/'.$row->id).'"><button class="btn btn-sm btn-default tip" data-toggle="tooltip" title="Add New Submission" data-trigger="hover" type="submit" ><i class="fa fa-upload"></i></button></a></div>';
                }else{
                    $btn = '';
                    if($row->status != "hold" && !in_array($row->status,$exprieStatus)){
                        $btn = '<span data-toggle="tooltip" title="Assign Requirement" data-trigger="hover">
                                    <button class="btn btn-sm btn-default assignRequirement mr-2" data-id="'.$row->id.'" type="button"><i class="fa fa-plus-square"></i></button>
                                </span>';
                    }
                }
            }else{
                $btn = '';
            }
            $btn .= '<div class="border border-dark floar-left p-1 mt-2" style="
                border-radius: 5px; width: auto"><span>'.getTimeInReadableFormate($row->created_at).'</span></div>';
        } else {
            if(($user['role'] == 'admin' && !array_key_exists($row->status,$exprieStatus)) || ($user['role'] == 'bdm' && $user['id'] == $row->user_id && !array_key_exists($row->status,$exprieStatus))){
                $btn .= '<div class="btn-group btn-group-sm mr-2"><a href="'.url('admin/requirement/'.$row->id.'/edit').'"><button class="btn btn-sm btn-default tip" data-toggle="tooltip" title="Edit Requirement" data-trigger="hover" type="submit" ><i class="fa fa-edit"></i></button></a></div>';
            }
            if($user['role'] == 'admin' && !array_key_exists($row->status,$exprieStatus)){
                $btn .= '<span data-toggle="tooltip" title="Delete Requirement" data-trigger="hover">
                            <button class="btn btn-sm btn-default deleteRequirement mr-2" data-id="'.$row->id.'" type="button"><i class="fa fa-trash"></i></button>
                        </span>';
            }
            //$btn .= '<div class="btn-group btn-group-sm"><a href="'.url('admin/requirement/'.$row->id).'"><button class="btn btn-sm btn-default tip" data-toggle="tooltip" title="View Submission" data-trigger="hover" type="submit" ><i class="fa fa-eye"></i></button></a></div>';
            if(($user['role'] == 'admin') || ($user->id == $row->user_id)){
               $btn .= '<div class="btn-group btn-group-sm"><button class="btn btn-sm btn-default tip view-submission" data-toggle="tooltip" title="View Submission" data-trigger="hover" type="submit" data-id="'.$row->id.'"><i class="fa fa-eye"></i></button></div>';
            }   
            //$btn .= '<div class="btn-group btn-group-sm ml-2"><a href="'.Route('requirement.repost',[$row->id]).'"><button class="btn btn-sm btn-default tip" data-toggle="tooltip" title="Repost Requirement" data-trigger="hover" type="submit"><i class="fa fa-retweet"></i></button></a></div>';
            if(($user['role'] == 'admin') || ($user['role'] == 'bdm' && $user['id'] == $row->user_id && $page != 'all_requirement')){ 
                $btn .= '<div class="btn-group btn-group-sm ml-2"><a href="'.url('admin/requirement/repostReqirement').'/'.$row->id.'"><button class="btn btn-sm btn-default tip" data-toggle="tooltip" title="Repost Requirement" data-trigger="hover" type="submit"><i class="fa fa-retweet"></i></button></a></div>';
            }
            $btn .= '<div class="border border-dark floar-left p-1 mt-2" style="
                border-radius: 5px; width: auto"><span>'.getTimeInReadableFormate($row->created_at).'</span></div>';
        }
        return $btn;
    }
}

if(!function_exists('getClientHtml')){
    function getClientHtml($row){
        $clientName = '';
        if($row->display_client == '1'){
            $clientName = $row->client_name;
        }
        return $clientName;
    }
}

if(!function_exists('getJobKeywordHtml')){
    function getJobKeywordHtml($row){
        $jobKeyword = strip_tags($row->job_keyword);
        if(strlen($jobKeyword) > 60){
            $shortString = substr($jobKeyword, 0, 60);
            return '<p>' . $shortString . '<span class=" job-title" data-id="'.$row->id.'"><span class="font-weight-bold"> More +</span></p>';
        }
        return '<p>'.strip_tags($row->job_keyword).'</p>';
    }
}

if(!function_exists('getJobIdHtml')){
    function getJobIdHtml($row){
        if(Auth::user()->role == 'admin' || (Auth::user()->role=='bdm' && Auth::user()->id == $row->user_id)){
            if($row->parent_requirement_id != $row->id && $row->parent_requirement_id != 0){
                return '<span class="border-width-5 border-color-info job-title pt-1 pl-1 pl-1 pr-1" data-id="'.$row->id.'">'.$row->job_id.'</span>';
            } elseif($row->parent_requirement_id == $row->id){
                return '<span class="border-width-5 border-color-warning job-title pt-1 pl-1 pl-1 pr-1" data-id="'.$row->id.'">'.$row->job_id.'</span>';
            } else {
                return '<span class=" job-title" data-id="'.$row->id.'">'.$row->job_id.'</span>';
            }
        } else {
            return '<span class=" job-title" data-id="'.$row->id.'">'.$row->job_id.'</span>';
        }
    }
}

if(!function_exists('getEntityLastUpdatedAtHtml')){
    function getEntityLastUpdatedAtHtml($entityType,$submissioId){
        $lastUpdatedAt =  EntityHistory::where('entity_type',$entityType)->where('submission_id',$submissioId)->orderBy('id','DESC')->first(['created_at']); 
        if(empty($lastUpdatedAt) || !$lastUpdatedAt->created_at){
            return '<div style="display:none" class="status-time statusUpdatedAt-'.$entityType.'-'.$submissioId.'"></div>';
        }
        return '<div style="display:none" class="status-time statusUpdatedAt-'.$entityType.'-'.$submissioId.'"><div class="border border-dark floar-left p-1 mt-2" style="border-radius: 5px; width: auto"><span style="color:#AC5BAD; font-weight:bold;">'.date('m/d h:i A', strtotime($lastUpdatedAt->created_at)).'</span></div></div>';
    }
}

if(!function_exists('getTimeInReadableFormate')){
    function getTimeInReadableFormate($date){
        $currentDateAndTime  = Carbon\Carbon::now();
        $requirementCreatedDate = Carbon\Carbon::parse($date);
        $timeSpan = '';

        // Calculate the difference in hours and minutes
        $diffInHours   = $currentDateAndTime->diffInHours($requirementCreatedDate);
        $diffInMinutes = $requirementCreatedDate->diffInMinutes($currentDateAndTime) % 60;

        if ($diffInHours >= 24) {
            // If the difference is more than 24 hours
            $diffInDays = floor($diffInHours / 24);
            $diffInHours = $diffInHours % 24;

            $timeSpan = "$diffInDays days, $diffInHours hr : $diffInMinutes m";
        } else {
            if($diffInHours > 1){
                // If the difference is less than 24 hours
                $timeSpan = "$diffInHours hr:$diffInMinutes m";
            }else{
                // If the difference is less than 1 hours
                $timeSpan = "$diffInMinutes m";
            }
        }
        return $timeSpan;
    }
}
