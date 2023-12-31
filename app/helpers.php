<?php

use App\Models\Admin;
use App\Models\Submission;
use App\Models\Requirement;
use App\Models\EntityHistory;
use App\Models\Interview;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

if(!function_exists('getLoggedInUserId')){
    function getLoggedInUserId(){
        return Auth::user()->id;
    }
}

if(!function_exists('getLoggedInUserRole')){
    function getLoggedInUserRole(){
        return Auth::user()->role;
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
