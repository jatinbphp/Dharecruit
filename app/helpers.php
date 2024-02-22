<?php

use App\Models\Admin;
use App\Models\Submission;
use App\Models\Requirement;
use App\Models\EntityHistory;
use App\Models\Interview;
use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamMember;
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

    if(!function_exists('isLeadUser')){
        function isLeadUser()
        {
           return (Team::where('team_lead_id', getLoggedInUserId())->exists()) ? true : false;
        }
    }

    if(!function_exists('getTeamMembers')){
        function getTeamMembers()
        {
            return Team::with('teamMembers')
                ->where('team_lead_id', getLoggedInUserId())
                ->get()
                ->pluck('teamMembers.*.member_id')
                ->flatten()
                ->unique()
                ->values()
                ->toArray();
        }
    }

    if(!function_exists('isManager')){
        function isManager()
        {
            return (Team::where('manager_id', getLoggedInUserId())->exists()) ? true : false;
        }
    }

    if(!function_exists('getManagerAllUsers')){
        function getManagerAllUsers()
        {
            if(!isManager()){
                return [];
            }
            return Team::with('teamMembers')
                ->where('manager_id', getLoggedInUserId())
                ->get()
                ->pluck('teamMembers.*.member_id')
                ->flatten()
                ->unique()
                ->values()
                ->toArray();
        }
    }

    if(!function_exists('getUserIdWiseTeamName')){
        function getUserIdWiseTeamName()
        {
            $userData = [];

            $admins = Admin::leftJoin('team_members', 'admins.id', '=', 'team_members.member_id')
                ->leftJoin('teams', 'team_members.team_id', '=', 'teams.id')
                ->select('admins.id', 'admins.name as admin_name', 'teams.team_name as team_name')
                ->where('status','active')
                ->whereNotNull('teams.team_name')
                ->orderBy('name')
                ->get();

            foreach ($admins as $admin) {
                if ($admin->team_name) {
                    $userData[$admin->id] =  $admin->team_name;
                }
            }

            return $userData;
        }
    }

    if(!function_exists('getTeamIdWiseTeamName')){
        function getTeamIdWiseTeamName($type = '')
        {
            $collection = Team::select();
                if($type) {
                    $collection->where('team_type', $type);
                }
            $data = $collection->pluck('team_name', 'id')
                ->toArray();

            return $data;
        }
    }
}
