<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AssignToRecruiter;
use App\Models\Interview;
use App\Models\Requirement;
use App\Models\Submission;
use App\Models\Team;
use App\Models\TeamMember;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function getTeamData($type)
    {
        $loggedInUserId   = Auth::user()->id;
        $loggedInUserName = Auth::user()->name;

        if(getLoggedInUserRole() == 'admin'){
            $teams = Team::with('teamMembers')->where('team_type', $type)->get();
        } elseif((isManager() || isLeadUser())){
            if(isManager() && isLeadUser()){
                $teams = Team::with('teamMembers')
                    ->where('team_type', $type)
                    ->where('team_lead_id', getLoggedInUserId())
                    ->orWhere('manager_id', getLoggedInUserId())
                    ->get();
            } elseif (isManager()){
                $teams = Team::with('teamMembers')
                    ->where('team_type', $type)
                    ->where('manager_id', getLoggedInUserId())
                    ->get();
            } elseif(isLeadUser()){
                $teams = Team::with('teamMembers')
                    ->where('team_type', $type)
                    ->where('team_lead_id', getLoggedInUserId())
                    ->get();
            }
        } else {
            $teams = Team::Join('team_members', 'teams.id', '=', 'team_members.team_id')
                ->join('admins', 'admins.id', '=', 'team_members.member_id')
                ->where('admins.status', 'active')
                ->select('teams.*')
                ->where('team_type', $type)
                ->Where('team_members.member_id', getLoggedInUserId())
                ->get();
        }

        $formattedData = [];

        foreach ($teams as $team) {
            $member = TeamMember::where('team_id', $team->id)->get();
            if(isLeadUser() || isManager() || getLoggedInUserRole() == 'admin'){
                $subs = $team->teamMembers->map(function ($member) {
                    return [
                        'id' => $member->member_id,
                        'title' => $member->membersData->name,
                    ];
                })->toArray();
            } else {
                $subs[] = [
                    'id' => $loggedInUserId,
                    'title' => $loggedInUserName,
                ];
            }

            if ($team->team_lead_id == $loggedInUserId) {
                array_unshift($subs,[
                    'id' => $loggedInUserId,
                    'title' => $loggedInUserName . '(Team Lead)',
                ]);
            }

            $formattedTeam = [
                'id' => 'team-' . $team->id,
                'title' => $team->team_name,
                'subs' => $subs
            ];

            $formattedData[] = $formattedTeam;
        }

        $userCollection = Admin::where('role', ($type == Team::TEAM_TYPE_BDM) ? 'bdm' : 'recruiter')->where('status', 'active');
        $users = [];

        if(getLoggedInUserRole() == 'admin'){
            $users = $userCollection->get();
        } else {
            if(!isLeadUser()){
                $users = $userCollection->where('id', getLoggedInUserId())->get();
            }
        }

        foreach ($users as $user){
            $id = $user->id;
            $teamMembership = TeamMember::where('member_id', $user->id)->first();
            if (!$teamMembership) {
                $formattedData[] = [
                    'id' => $id,
                    'title' => $user->name,
                ];
            }
        }
        return $formattedData;
    }

    public function getTypeWiseDateLabels($type, $fromDate = '', $toDate = '')
    {
        if(!$fromDate || !$toDate){
            $toDate = Carbon::today()->format('m/d/Y');
            $currentDate = Carbon::today();
            if($type == 'monthly'){
                $fromDate = $currentDate->startOfYear()->format('m/d/Y');
            } elseif ($type == 'weekly'){
                $fromDate =  $currentDate->startOfMonth()->format('m/d/Y');
            }else{
                $fromDate = $currentDate->startOfMonth()->format('m/d/Y');
            }
        }

        $fromDate = Carbon::createFromFormat('m/d/Y', $fromDate);
        $toDate = Carbon::createFromFormat('m/d/Y', $toDate);

        switch ($type) {
            case 'weekly':
                $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $fromDate)->startOfWeek();
                $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $toDate)->endOfWeek();
                $labels = [];

                while ($startDate <= $endDate) {
                    $labels[] = $startDate->format('m/d/y') . ' to ' . $startDate->copy()->endOfWeek()->format('m/d/y');
                    $startDate->addWeek();
                }
                break;

            case 'monthly':
                $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $fromDate)->startOfMonth();
                $endDate = Carbon::createFromFormat('Y-m-d  H:i:s', $toDate)->endOfMonth();

                $labels = [];

                while ($startDate <= $endDate) {
                    $labels[] = $startDate->format('M') .'-'. $startDate->format('Y');
                    $startDate->addMonthNoOverflow();
                }
                break;
            case 'time_frame':
                $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $fromDate);
                $endDate   = Carbon::createFromFormat('Y-m-d H:i:s', $toDate);
                $labels[]  = $startDate->format('m/d/y') . ' to ' . $endDate->format('m/d/y');
                break;
            case 'daily':
            default:
                $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $fromDate)->startOfDay();
                $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $toDate)->endOfDay();
                $labels = [];

                while ($startDate <= $endDate) {
                    $labels[] = $startDate->format('d-M');
                    $startDate->addDay();
                }
                break;
        }
        return $labels;
    }

    public function index(){
        $data['menu'] = 'Dashboard';
        $data['loggedUser']              = $this->getUser();
        $data['adminUsers']              = Admin::where('id', '!=', Auth::user()->id)->where('role','admin')->count();
        $data['bdmUsers']                = Admin::where('id', '!=', Auth::user()->id)->where('role','bdm')->where('status', 'active')->count();
        $data['recUsers']                = Admin::where('id', '!=', Auth::user()->id)->where('role','recruiter')->where('status', 'active')->count();
        $data['totalRequirements']       = Requirement::count();
        $data['monthlyLabels']           = $this->getTypeWiseDateLabels('monthly');
        $data['monthlyRequiremtCounts']  = $this->getCountsForModel('App\Models\Requirement', 'monthly', 0);
        $data['monthlySubmissionCounts'] = $this->getCountsForModel('App\Models\Submission', 'monthly', 0);
        $data['monthlyServedCounts']     = $this->getservedRequirementCount('monthly', 0);
        $data['rec_team_data']           = json_encode($this->getTeamData(Team::TEAM_TYPE_RECRUITER));
        $data['bdm_team_data']           = json_encode($this->getTeamData(Team::TEAM_TYPE_BDM));
        $data['user_color']              = json_encode(Admin::getUserNameWiseColor());

        return view('admin.dashboard',$data);
    }

    public function getTypeWiseChartData(Request $request)
    {
        $data['status'] = 0;
        if(empty($request->type)){
            return $data;
        }

        $data['label']            = $this->getTypeWiseDateLabels($request->type);
        $data['requiremtCounts']  = $this->getCountsForModel('App\Models\Requirement', $request->type, $request->is_uni_req);
        $data['submissionCounts'] = $this->getCountsForModel('App\Models\Submission', $request->type, $request->is_uni_sub);
        $data['servedCounts']     = $this->getservedRequirementCount($request->type, $request->is_uni_req);
        $data['status'] = 1;
        return $data;
    }

    private function getCountsForModel($modelName, $type, $isUni)
    {
        $currentDate = now();
        switch ($type) {
            case 'monthly':
                $startDate = $currentDate->copy()->startOfYear();
                $endDate = $currentDate->copy()->endOfMonth();
                break;
            case 'weekly':
                $startDate = $currentDate->copy()->startOfMonth()->startOfWeek();
                $endDate = $currentDate->copy()->endOfMonth()->endOfWeek();
                break;
            case 'time_frame':
                $startDate = $currentDate->copy()->startOfYear();
                $endDate = $currentDate;
                break;
            case 'daily':
            default:
                $startDate = $currentDate->copy()->startOfMonth();
                $endDate = $currentDate->copy()->endOfMonth();
                break;
        }

        $collection = $modelName::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d") as date, COUNT(*) as count');
        if($modelName == 'App\Models\Requirement' && $isUni){
            $collection->where(function ($query) {
                $query->where('id' ,'=', \DB::raw('parent_requirement_id'));
                $query->orwhere('parent_requirement_id', '=', '0');
            });
        }

        if($modelName == 'App\Models\Submission' && $isUni){
            $collection->where('id' ,'=', \DB::raw('candidate_id'));
        }

        $countsQuery = $collection->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        if($type == 'time_frame'){
            $counts[] = array_sum($countsQuery);
            return $counts;
        }

        $counts = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $date = $currentDate->format('Y-m-d');
            switch ($type) {
                case 'monthly':
                    $month = $currentDate->format('Y-m');
                    $counts[] = array_sum(array_filter($countsQuery, function ($key) use ($month) {
                        return strpos($key, $month) === 0;
                    }, ARRAY_FILTER_USE_KEY));
                    $currentDate->addMonthNoOverflow(); // Move to the next month
                    break;
                case 'weekly':
                    $counts = [];
                    while ($currentDate->lte($endDate)) {
                        $weekStart = $currentDate->copy()->startOfWeek();
                        $weekEnd = $currentDate->copy()->endOfWeek();
                        $countForWeek = [];

                        foreach ($countsQuery as $key => $count) {
                            $date = \Carbon\Carbon::parse($key);
                            if ($date->between($weekStart, $weekEnd)) {
                                $countForWeek[]= $count;
                            }
                        }
                        $counts[] =array_sum($countForWeek);
                        $currentDate->addWeek();
                    }
                    break;
                case 'daily':
                default:
                    $counts[] = $countsQuery[$date] ?? 0;
                    $currentDate->addDay(); // Move to the next day
                    break;
            }
        }
        return $counts;
    }

    public function getservedRequirementCount($type, $isUni)
    {
        $currentDate = now();
        switch ($type) {
            case 'monthly':
                $startDate = $currentDate->copy()->startOfYear();
                $endDate = $currentDate->copy()->endOfMonth();
                break;
            case 'weekly':
                $startDate = $currentDate->copy()->startOfMonth()->startOfWeek();
                $endDate = $currentDate->copy()->endOfMonth()->endOfWeek();
                break;
            case 'time_frame':
                $startDate = $currentDate->copy()->startOfYear();
                $endDate = $currentDate;
                break;
            case 'daily':
            default:
                $startDate = $currentDate->copy()->startOfMonth();
                $endDate = $currentDate->copy()->endOfMonth();
                break;
        }

        $collection = Requirement::leftJoin('submissions', 'requirements.id', '=', 'submissions.requirement_id')
            ->whereBetween('requirements.created_at', [$startDate, $endDate])
            ->select(\DB::raw('DATE(requirements.created_at) AS date'), \DB::raw('COUNT(DISTINCT(submissions.requirement_id)) AS count'));
        if($isUni){
            $collection->where(function ($query) {
                $query->where('requirements.id' ,'=', \DB::raw('requirements.parent_requirement_id'));
                $query->orwhere('requirements.parent_requirement_id', '=', '0');
            });
        }

        $countsQuery = $collection->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        if($type == 'time_frame'){
            $counts[] = array_sum($countsQuery);
            return $counts;
        }

        $counts = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $date = $currentDate->format('Y-m-d');
            switch ($type) {
                case 'monthly':
                    $month = $currentDate->format('Y-m');
                    $counts[] = array_sum(array_filter($countsQuery, function ($key) use ($month) {
                        return strpos($key, $month) === 0;
                    }, ARRAY_FILTER_USE_KEY));
                    $currentDate->addMonthNoOverflow(); // Move to the next month
                    break;
                case 'weekly':
                    $counts = [];
                    while ($currentDate->lte($endDate)) {
                        $weekStart = $currentDate->copy()->startOfWeek();
                        $weekEnd = $currentDate->copy()->endOfWeek();
                        $countForWeek = [];

                        foreach ($countsQuery as $key => $count) {
                            $date = \Carbon\Carbon::parse($key);
                            if ($date->between($weekStart, $weekEnd)) {
                                $countForWeek[]= $count;
                            }
                        }
                        $counts[] =array_sum($countForWeek);
                        $currentDate->addWeek();
                    }
                    break;
                case 'daily':
                default:
                    $counts[] = $countsQuery[$date] ?? 0;
                    $currentDate->addDay(); // Move to the next day
                    break;
            }
        }
        return $counts;
    }

    public function getBdmStatusData(Request $request)
    {
        $data['status'] = 0;
        if(empty($request->fromDate) || empty($request->toDate) || empty($request->type)){
            return $data;
        }
        $bdmUser = $request->bdmUser;
        $recUser = $request->recUser;
        if(empty($bdmUser)){
            $bdmUser = [0];
        }

        if(empty($recUser)){
            $recUser = [0];
        }
        $data['labels'] = array_values(Submission::$status);
        $data['counts'] = $this->getBdmStatusCounts($request->fromDate, $request->toDate, $request->type, $bdmUser, $recUser, $request->frame_type);
        $data['status'] = 1;
        return $data;
    }

    public function getBdmStatusCounts($fromDate, $toDate, $type, $bdmUser, $recruiterUser, $frameType)
    {
        $fromDate = \Carbon\Carbon::createFromFormat('m/d/Y', $fromDate)->format('Y-m-d');
        $toDate = \Carbon\Carbon::createFromFormat('m/d/Y', $toDate)->addDay()->format('Y-m-d');
        $bdmStatus = Submission::$status;
        $bdmStatus = array_fill_keys(array_keys($bdmStatus), 0);
        $submissionCounts = [];

        if($type == 'recruiter' && $recruiterUser){
            $collection = Submission::select('status', \DB::raw('count(*) as count'))
                ->whereIn('status', array_keys($recruiterUser))
                ->whereIn('user_id', $recruiterUser);
            if($frameType == 'submission_frame'){
                $collection->whereBetween('created_at', [$fromDate, $toDate]);
            } else {
                $collection->whereBetween('bdm_status_updated_at', [$fromDate, $toDate]);
            }
            $submissionCounts = $collection->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
        } elseif ($type == 'bdm' && $bdmUser){
            $collection = Requirement::leftjoin('submissions', 'submissions.requirement_id', '=', 'requirements.id')
                ->select('submissions.status', \DB::raw('count(*) as count'))
                ->whereIn('requirements.user_id', $bdmUser)
                ->whereIn('submissions.status', array_keys($bdmStatus));
            if($frameType == 'submission_frame'){
                $collection->whereBetween('submissions.created_at', [$fromDate, $toDate]);
            } else {
                $collection->whereBetween('submissions.bdm_status_updated_at', [$fromDate, $toDate]);
            }
            $submissionCounts = $collection->groupBy('submissions.status')
                ->pluck('count', 'submissions.status')
                ->toArray();
        }

        foreach ($bdmStatus as $status => $count) {
            if (isset($submissionCounts[$status])) {
                $bdmStatus[$status] = $submissionCounts[$status];
            }
        }

        return array_values($bdmStatus);
    }

    public function getPvStatusData(Request $request)
    {
        $data['status'] = 0;
        if(empty($request->fromDate) || empty($request->toDate) || empty($request->type)){
            return $data;
        }
        $bdmUser = $request->bdmUser;
        $recUser = $request->recUser;
        if(empty($bdmUser)){
            $bdmUser = [0];
        }

        if(empty($recUser)){
            $recUser = [0];
        }

        $data['labels'] = array_values(Submission::$pvStatus);
        $data['counts'] = $this->getPvStatusCount($request->fromDate, $request->toDate, $request->type, $bdmUser, $recUser, $request->frame_type);
        $data['status'] = 1;
        return $data;
    }
    public function getPvStatusCount($fromDate, $toDate, $type, $bdmUser, $recruiterUser, $frameType)
    {
        $fromDate = \Carbon\Carbon::createFromFormat('m/d/Y', $fromDate)->format('Y-m-d');
        $toDate = \Carbon\Carbon::createFromFormat('m/d/Y', $toDate)->addDay()->format('Y-m-d');
        $pvStatus = Submission::$pvStatus;
        $pvStatus = array_fill_keys(array_keys($pvStatus), 0);

        $submissionCounts = [];

        if($type == 'recruiter' && $recruiterUser) {
            $collection = Submission::select('pv_status', \DB::raw('count(*) as count'))
                ->whereIn('pv_status', array_keys($pvStatus))
                ->whereIn('user_id', $recruiterUser)
                ->groupBy('pv_status');
            if($frameType == 'submission_frame'){
                $collection->whereBetween('created_at', [$fromDate, $toDate]);
            } else {
                $collection->whereBetween('pv_status_updated_at', [$fromDate, $toDate]);
            }
            $submissionCounts = $collection->pluck('count', 'pv_status')
                ->toArray();
        } elseif ($type == 'bdm' && $bdmUser){
            $collection = Requirement::leftjoin('submissions', 'submissions.requirement_id', '=', 'requirements.id')
                ->select('submissions.pv_status', \DB::raw('count(*) as count'))
                ->whereIn('requirements.user_id', $bdmUser)
                ->whereIn('submissions.pv_status', array_keys($pvStatus))
                ->groupBy('submissions.pv_status');
            if($frameType == 'submission_frame'){
                $collection->whereBetween('submissions.created_at', [$fromDate, $toDate]);
            } else {
                $collection->whereBetween('submissions.pv_status_updated_at', [$fromDate, $toDate]);
            }
            $submissionCounts = $collection->pluck('count', 'submissions.pv_status')
                ->toArray();
        }

        foreach ($submissionCounts as $status => $count) {
            if (isset($submissionCounts[$status])) {
                $pvStatus[$status] = $submissionCounts[$status];
            }
        }

        return array_values($pvStatus);
    }

    public function getInterviewStatusData(Request $request)
    {
        $data['status'] = 0;
        if(empty($request->fromDate) || empty($request->toDate) || empty($request->type)){
            return $data;
        }
        $bdmUser = $request->bdmUser;
        $recUser = $request->recUser;
        if(empty($bdmUser)){
            $bdmUser = [0];
        }

        if(empty($recUser)){
            $recUser = [0];
        }
        $interviewStatus = Interview::$interviewStatusOptions;
        unset($interviewStatus['']);
        $data['labels'] = array_values($interviewStatus);
        $data['counts'] = $this->getInterviewStatusCounts($request->fromDate, $request->toDate, $request->type, $bdmUser, $recUser, $request->frame_type);
        $data['status'] = 1;
        return $data;
    }

    public function getInterviewStatusCounts($fromDate, $toDate, $type, $bdmUser, $recUser, $frameType)
    {
        $fromDate = \Carbon\Carbon::createFromFormat('m/d/Y', $fromDate)->format('Y-m-d');
        $toDate = \Carbon\Carbon::createFromFormat('m/d/Y', $toDate)->addDay()->format('Y-m-d');
        $interviewStatus = Interview::$interviewStatusOptions;
        unset($interviewStatus['']);
        $interviewStatus = array_fill_keys(array_keys($interviewStatus), 0);

        $submissionCounts = [];

        if($type == 'recruiter' && $recUser) {
            $collection = Submission::leftJoin('interviews', 'submissions.id', '=', 'interviews.submission_id')
                ->selectRaw('interviews.status, COUNT(interviews.id) as count')
                ->whereIn('submissions.user_id', $recUser)
                ->whereIn('interviews.status', array_keys($interviewStatus));
            if($frameType == 'submission_frame'){
                $collection->whereBetween('submissions.created_at', [$fromDate, $toDate]);
            } else {
                $collection->whereBetween('submissions.interview_status_updated_at', [$fromDate, $toDate]);
            }
            $intervewCounts = $collection->groupBy('interviews.status')
                ->pluck('count', 'interviews.status')
                ->toArray();
        } elseif ($type == 'bdm' && $bdmUser){
            $collection = Requirement::leftJoin('submissions', 'requirements.id', '=', 'submissions.requirement_id')
                ->leftJoin('interviews', 'submissions.id', '=', 'interviews.submission_id')
                ->selectRaw('interviews.status, COUNT(interviews.id) as count')
                ->whereIn('requirements.user_id', $bdmUser)
                ->whereIn('interviews.status', array_keys($interviewStatus));
                if($frameType == 'submission_frame'){
                    $collection->whereBetween('submissions.created_at', [$fromDate, $toDate]);
                } else {
                    $collection->whereBetween('submissions.interview_status_updated_at', [$fromDate, $toDate]);
                }
            $intervewCounts = $collection->groupBy('interviews.status')
                ->pluck('count', 'interviews.status')
                ->toArray();
        }

        foreach ($interviewStatus as $status => $count) {
            if (isset($intervewCounts[$status])) {
                $interviewStatus[$status] = $intervewCounts[$status];
            }
        }

        return array_values($interviewStatus);
    }

    public function getRequirementAssignedServedSubmission(Request $request)
    {
        $data['status'] = 0;
        if(empty($request->type)){
            return $data;
        }

        $labels = $this->getTypeWiseDateLabels($request->type, $request->fromDate, $request->toDate);
        $data['label'] = $labels;
        $data['assignedRequiremenrtCount'] = array_values($this->getAssignedRequirementCount($labels, $request->fromDate, $request->toDate, $request->type, $request->selected_user, $request->isUniReq));
        $data['recruiterservedCounts']     = array_values($this->getRecruiterServedCount($labels, $request->fromDate, $request->toDate, $request->type, $request->selected_user, $request->isUniSub));
        $data['submissionCount']           = array_values($this->getSubmissionsCount($labels, $request->fromDate, $request->toDate, $request->type, $request->selected_user, $request->isUniSub));
        $data['status'] = 1;
        return $data;
    }

    public function getAssignedRequirementCount($labels, $fromDate, $toDate, $type, $recruiters, $isUniq)
    {
        $startDate = Carbon::createFromFormat('m/d/Y', $fromDate);
        $endDate = Carbon::createFromFormat('m/d/Y', $toDate);
        if(!$recruiters){
            $recruiters[] = getLoggedInUserId();
        }

        $collection = AssignToRecruiter::join('requirements', 'requirements.id', '=', 'assign_to_recruiters.requirement_id')
            ->whereIN('recruiter_id', $recruiters)
            ->whereBetween('assign_to_recruiters.created_at', [$startDate, $endDate])
            ->select(\DB::raw('DATE(assign_to_recruiters.created_at) AS date'), \DB::raw('COUNT(assign_to_recruiters.requirement_id) as count'));

            if($isUniq) {
                $collection->where(function ($query) {
                    $query->where('requirements.id', '=', \DB::raw('requirements.parent_requirement_id'));
                    $query->orwhere('requirements.parent_requirement_id', '=', '0');
                });
            }
        $countsQuery = $collection->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        return $this->getLabelWiseData($type, $labels, $countsQuery);
    }

    public function getRecruiterServedCount($labels, $fromDate, $toDate, $type, $recruiters, $isUniq)
    {
        if(!$recruiters){
            return [];
        }
        $startDate = Carbon::createFromFormat('m/d/Y', $fromDate);
        $endDate = Carbon::createFromFormat('m/d/Y', $toDate);

        $collection = Submission::whereIn('user_id', $recruiters)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(\DB::raw('DATE(created_at) AS date'), \DB::raw('COUNT(DISTINCT requirement_id) as count'));

        if($isUniq){
            $collection->where('id' ,\DB::raw('candidate_id'));
        }
        $countsQuery = $collection->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        return $this->getLabelWiseData($type, $labels, $countsQuery);
    }

    public function getLabelWiseData($type, $labels, $counts)
    {
        $preparedData = array_fill_keys($labels, 0);
        if($type == 'time_frame'){
            $key = array_key_first($preparedData);
            $preparedData[$key] = array_sum($counts);
            return $preparedData;
        }
        foreach ($counts as $date => $value) {
            switch ($type) {
                case 'monthly':
                    $monthYear = Carbon::createFromFormat('Y-m-d', $date)->format('M-Y');
                    if (!isset($preparedData[$monthYear])) {
                        $preparedData[$monthYear] = 0;
                    }
                    $preparedData[$monthYear] += $value;
                    break;

                case 'weekly':
                    $weekKey = Carbon::createFromFormat('Y-m-d', $date)->startOfWeek()->format('m/d/y') .' to '. Carbon::createFromFormat('Y-m-d', $date)->endOfWeek()->format('m/d/y');
                    if (!isset($preparedData[$weekKey])) {
                        $preparedData[$weekKey] = 0;
                    }
                    $preparedData[$weekKey] += $value;
                    break;
                case 'daily':
                default:
                    $dayKey = Carbon::createFromFormat('Y-m-d', $date)->format('d-M');
                    if (!isset($preparedData[$dayKey])) {
                        $preparedData[$dayKey] = 0;
                    }
                    $preparedData[$dayKey] += $value;
                    break;
            }
        }
        return $preparedData;
    }

    public function getSubmissionsCount($labels, $fromDate, $toDate, $type, $recruiters, $isUniqSubmission = 0)
    {
        $startDate = Carbon::createFromFormat('m/d/Y', $fromDate);
        $endDate = Carbon::createFromFormat('m/d/Y', $toDate);

        if(!$recruiters){
            return [];
        }

        $collection = Submission::whereIn('user_id', $recruiters)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(\DB::raw('DATE(created_at) AS date'), \DB::raw('COUNT(requirement_id) as count'));

        if($isUniqSubmission){
            $collection->where('id' ,\DB::raw('candidate_id'));
        }


        $countsQuery = $collection->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        return $this->getLabelWiseData($type, $labels, $countsQuery);
    }

    public function getRequirementCountServedSubmission(Request $request)
    {
        $data['status'] = 0;
        if(empty($request->type)){
            return $data;
        }

        $labels = $this->getTypeWiseDateLabels($request->type, $request->fromDate, $request->toDate);
        $data['label'] = $labels;
        $data['requiremenrtCount'] = array_values($this->getRequirementCount($labels, $request->fromDate, $request->toDate, $request->type, $request->selected_user, $request->isUniReq));
        $data['servedCounts']      = array_values($this->getBdmServedCount($labels, $request->fromDate, $request->toDate, $request->type, $request->selected_user, $request->isUniSub));
        $data['submissionCount']   = array_values($this->getBdmSubmissionCount($labels, $request->fromDate, $request->toDate, $request->type, $request->selected_user, $request->isUniSub));
        $data['status'] = 1;
        return $data;
    }

    public function getRequirementCount($labels, $fromDate, $toDate, $type, $selectedUser, $isUniq)
    {
        $startDate = Carbon::createFromFormat('m/d/Y', $fromDate);
        $endDate = Carbon::createFromFormat('m/d/Y', $toDate);

       $collection = Requirement::whereIn('user_id', ($selectedUser ) ?? [0])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(\DB::raw('DATE(created_at) AS date'), \DB::raw('COUNT(id) as count'));

       if($isUniq){
           $collection->where(function ($query) {
               $query->where('requirements.id' ,'=', \DB::raw('requirements.parent_requirement_id'));
               $query->orwhere('requirements.parent_requirement_id', '=', '0');
           });
       }
       $countsQuery = $collection->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        return $this->getLabelWiseData($type, $labels, $countsQuery);
    }

    public function getBdmServedCount($labels, $fromDate, $toDate, $type, $selectedUser, $isUniq)
    {
        if(!$selectedUser){
            return [];
        }
        $startDate = Carbon::createFromFormat('m/d/Y', $fromDate);
        $endDate = Carbon::createFromFormat('m/d/Y', $toDate);

        $collection = Requirement::leftJoin('submissions', 'requirements.id', '=', 'submissions.requirement_id')
            ->whereBetween('submissions.created_at', [$startDate, $endDate])
            ->select(\DB::raw('DATE(submissions.created_at) AS date'), \DB::raw('COUNT(DISTINCT(submissions.requirement_id)) AS count'))
            ->whereIn('requirements.user_id', $selectedUser);

        if($isUniq){
//            $collection->where(function ($query) {
//                $query->where('requirements.id' ,'=', \DB::raw('requirements.parent_requirement_id'));
//                $query->orwhere('requirements.parent_requirement_id', '=', '0');
//            });
            $collection->where('submissions.id' ,\DB::raw('submissions.candidate_id'));
        }

        $countsQuery = $collection->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();
        return $this->getLabelWiseData($type, $labels, $countsQuery);
    }

    public function getBdmSubmissionCount($labels, $fromDate, $toDate, $type, $selectedUser, $isUniq)
    {
        $startDate = Carbon::createFromFormat('m/d/Y', $fromDate);
        $endDate = Carbon::createFromFormat('m/d/Y', $toDate);

        $collection = Requirement::leftJoin('submissions', 'requirements.id', '=', 'submissions.requirement_id')
            ->whereBetween('submissions.created_at', [$startDate, $endDate])
            ->select(\DB::raw('DATE(submissions.created_at) AS date'), \DB::raw('COUNT((submissions.requirement_id)) AS count'))
            ->whereIn('requirements.user_id', ($selectedUser ) ?? [0]);

        if($isUniq){
            $collection->where(function ($query) {
                $query->where('submissions.id' ,'=', \DB::raw('submissions.candidate_id'));
            });
        }

        $countsQuery = $collection->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();
        return $this->getLabelWiseData($type, $labels, $countsQuery);
    }

    public function getInterviewsCount(Request $request)
    {
        $data['status'] = 0;
        if(empty($request->type)){
            return $data;
        }

        $labels = $this->getTypeWiseDateLabels($request->type, $request->fromDate, $request->toDate);
        $data['label'] = $labels;
        $data['totalInterviewCount'] = array_values($this->getTotalInterviewsCounts($labels, $request->fromDate, $request->toDate, $request->user_type, $request->bdmUser, $request->recUser, $request->type));
        $data['status'] = 1;
        return $data;
    }

    public function getTotalInterviewsCounts($labels, $fromDate, $toDate, $userType, $bdmUser, $recruiterUser, $type)
    {
        $fromDate = \Carbon\Carbon::createFromFormat('m/d/Y', $fromDate)->format('Y-m-d');
        $toDate = \Carbon\Carbon::createFromFormat('m/d/Y', $toDate)->addDay()->format('Y-m-d');

        $interviewCounts = [];
        if($userType == 'bdm' && $bdmUser){
            $interviewCounts = Requirement::leftJoin('submissions', 'requirements.id', '=', 'submissions.requirement_id')
                ->leftJoin('interviews', 'submissions.id', '=', 'interviews.submission_id')
                ->whereIn('requirements.user_id', $bdmUser)
                ->whereBetween('interviews.created_at', [$fromDate, $toDate])
                ->select(\DB::raw('DATE(interviews.created_at) AS date'), \DB::raw('COUNT((interviews.id)) AS count'))
                ->groupBy('date')
                ->pluck('count','date')
                ->toArray();
        } elseif($userType == 'recruiter' && $recruiterUser) {
            $interviewCounts = Submission::leftJoin('interviews', 'submissions.id', '=', 'interviews.submission_id')
                ->whereIn('submissions.user_id', $recruiterUser)
                ->whereBetween('interviews.created_at', [$fromDate, $toDate])
                ->select(\DB::raw('DATE(interviews.created_at) AS date'), \DB::raw('COUNT((interviews.id)) AS count'))
                ->groupBy('date')
                ->pluck('count','date')
                ->toArray();
        }
        return $this->getLabelWiseData($type, $labels, $interviewCounts);
    }

    public function getAcceptSbumittedCount(Request $request)
    {
        $data['status'] = 0;
        if(empty($request->type)){
            return $data;
        }

        $labels                             = $this->getTypeWiseDateLabels($request->type, $request->fromDate, $request->toDate);
        $data['label']                      = $labels;
        $data['acceptCounts']               = array_values($this->getTotalBdmStatusCounts('status', Submission::STATUS_ACCEPT, $labels, $request->fromDate, $request->toDate, $request->user_type, $request->bdmUser, $request->recUser, $request->type, $request->frame_type));
        $data['submittedToEndClientCounts'] = array_values($this->getTotalBdmStatusCounts('pv_status', Submission::STATUS_SUBMITTED_TO_END_CLIENT, $labels, $request->fromDate, $request->toDate, $request->user_type, $request->bdmUser, $request->recUser, $request->type, $request->frame_type));
        $data['status']                     = 1;
        return $data;
    }

    public function getTotalBdmStatusCounts($filedName,$status, $labels, $fromDate, $toDate, $userType, $bdmUser, $recruiterUser, $type, $frameType)
    {
        $fromDate = \Carbon\Carbon::createFromFormat('m/d/Y', $fromDate)->format('Y-m-d');
        $toDate = \Carbon\Carbon::createFromFormat('m/d/Y', $toDate)->addDay()->format('Y-m-d');

        $date = 'submissions.bdm_status_updated_at';
        if($filedName == 'pv_status'){
            $date = 'submissions.pv_status_updated_at';
        }
        $statusCounts = [];
        if($userType == 'bdm' && $bdmUser){
            $collection = Requirement::leftJoin('submissions', 'requirements.id', '=', 'submissions.requirement_id')
                ->whereIn('requirements.user_id', $bdmUser)
                ->where('submissions.'.$filedName, $status);

            if($frameType == 'submission_frame'){
                $collection->whereBetween('submissions.created_at', [$fromDate, $toDate]);
            } else {
                $collection->whereBetween($date, [$fromDate, $toDate]);
            }

            $statusCounts = $collection->select(\DB::raw("DATE($date) AS date"), \DB::raw('COUNT((submissions.id)) AS count'))
                ->groupBy('date')
                ->pluck('count','date')
                ->toArray();

        } elseif($userType == 'recruiter' && $recruiterUser) {
            $collection = Submission::whereIn('user_id', $recruiterUser)
                ->where($filedName, $status);

            if($frameType == 'submission_frame'){
                $collection->whereBetween('created_at', [$fromDate, $toDate]);
            } else {
                $collection->whereBetween($date, [$fromDate, $toDate]);
            }

            $statusCounts = $collection->select(\DB::raw("DATE($date) AS date"), \DB::raw('COUNT((id)) AS count'))
                ->groupBy('date')
                ->pluck('count','date')
                ->toArray();
        }

        return $this->getLabelWiseData($type, $labels, $statusCounts);
    }

    public function getUserAndDateWiseCounts($type, $labels, $countData, $userType, $bdmUser, $recruiterUser){
        if($userType == 'bdm'){
            $userArray = Admin::where('role', 'bdm')->where('status', 'active')->whereIn('id', $bdmUser)->pluck('name', 'id')->toArray();
        } else {
            $userArray = Admin::where('role', 'recruiter')->where('status', 'active')->whereIn('id', $recruiterUser)->pluck('name', 'id')->toArray();
        }

        $preparedData  = [];
        $timeFrameKey = '';

        foreach ($userArray as  $userId => $name){
            foreach ($labels as $value) {
                if($type == 'time_frame'){
                    $timeFrameKey = $value;
                }
                $preparedData[$name][$value] = 0;
            }
        }

        foreach ($countData as $data) {
            $date   = isset($data['date']) ? $data['date'] : '';
            $value  = isset($data['count']) ? $data['count'] : '';
            $user   = isset($data['user']) ? $data['user'] : '';

            if(!$date){
                continue;
            }

            switch ($type) {
                case 'monthly':
                    $monthYear = Carbon::createFromFormat('Y-m-d', $date)->format('M-Y');
                    if (!isset($preparedData[$user][$monthYear])) {
                        $preparedData[$user][$monthYear] = 0;
                    }
                    $preparedData[$user][$monthYear] += $value;
                    break;

                case 'weekly':
                    $weekKey = Carbon::createFromFormat('Y-m-d', $date)->startOfWeek()->format('m/d/y') .' to '. Carbon::createFromFormat('Y-m-d', $date)->endOfWeek()->format('m/d/y');
                    if (!isset($preparedData[$user][$weekKey])) {
                        $preparedData[$user][$weekKey] = 0;
                    }
                    $preparedData[$user][$weekKey] += $value;
                    break;
                case 'time_frame':
                    if (!isset($preparedData[$user][$timeFrameKey])) {
                        $preparedData[$user][$timeFrameKey] = 0;
                    }
                    $preparedData[$user][$timeFrameKey] += $value;
                    break;
                case 'daily':
                default:
                    $dayKey = Carbon::createFromFormat('Y-m-d', $date)->format('d-M');
                    if (!isset($preparedData[$user][$dayKey])) {
                        $preparedData[$user][$dayKey] = 0;
                    }
                    $preparedData[$user][$dayKey] += $value;
                    break;
            }
        }

        return $preparedData;
    }
    public function getInterviewChartData(Request $request)
    {
        $data['status'] = 0;
        if(empty($request->type) || empty($request->fromDate) || empty($request->toDate)){
            return $data;
        }

        $bdmUser = $request->bdmUser;
        $recUser = $request->recUser;
        if(empty($bdmUser)){
            $bdmUser = [0];
        }

        if(empty($recUser)){
            $recUser = [0];
        }

        $labels = $this->getTypeWiseDateLabels($request->type, $request->fromDate, $request->toDate);
        $data['labels'] = $labels;
        $data['interviewCounts'] = $this->getIndividualInterviewCounts($request->fromDate, $request->toDate, $request->type, $labels, $request->user_type, $bdmUser, $recUser);
        $data['status'] = 1;
        return $data;
    }

    public function getIndividualInterviewCounts($fromDate, $toDate, $type, $labels, $userType, $bdmUser, $recruiterUser)
    {
        $fromDate = \Carbon\Carbon::createFromFormat('m/d/Y', $fromDate)->format('Y-m-d');
        $toDate = \Carbon\Carbon::createFromFormat('m/d/Y', $toDate)->addDay()->format('Y-m-d');

        if($userType == 'bdm'){
            $collection = Requirement::leftJoin('submissions', 'requirements.id', '=', 'submissions.requirement_id')
                ->leftJoin('interviews', 'submissions.id', '=', 'interviews.submission_id')
                ->leftJoin('admins', 'admins.id', '=', 'requirements.user_id')
                ->where('admins.role', 'bdm')
                ->where('admins.status', 'active')
                ->whereIn('admins.id', $bdmUser)
                ->whereBetween('interviews.created_at', [$fromDate, $toDate])
                ->groupBy('interview_date', 'id')
                ->select(\DB::raw('DATE(interviews.created_at) as date'),'admins.name AS user', 'admins.id', \DB::raw('COUNT(*) as count'));
            if(isManager()){
                $collection->whereIn('admins.id', getManagerAllUsers());
            } elseif (isLeadUser()){
                $collection->whereIn('admins.id', getTeamMembers());
            }
            $interviewCount = $collection->get();
        } else {
            $collection = Submission::leftJoin('interviews', 'submissions.id', '=', 'interviews.submission_id')
                ->leftJoin('admins', 'admins.id', '=', 'submissions.user_id')
                ->where('admins.role', 'recruiter')
                ->where('admins.status', 'active')
                ->whereIn('admins.id', $recruiterUser)
                ->whereBetween('interviews.created_at', [$fromDate, $toDate])
                ->groupBy('interview_date', 'id')
                ->select(\DB::raw('DATE(interviews.created_at) as date'), 'admins.name AS user', 'admins.id', \DB::raw('COUNT(*) as count'));
            if(isManager()){
                $collection->whereIn('admins.id', getManagerAllUsers());
            } elseif (isLeadUser()){
                $collection->whereIn('admins.id', getTeamMembers());
            }
            $interviewCount = $collection->get();
        }

        return $this->getUserAndDateWiseCounts($type, $labels, $interviewCount, $userType, $bdmUser, $recruiterUser);
    }

    public function getIndividualRequirementAssigned(Request $request)
    {
        $data['status'] = 0;
        if(empty($request->type) || empty($request->fromDate) || empty($request->toDate)){
            return $data;
        }

        $labels                           = $this->getTypeWiseDateLabels($request->type, $request->fromDate, $request->toDate);
        $data['labels']                   = $labels;
        $data['requirementAssigneeCount'] = $this->getIndividualRequirementAssignCounts($request->fromDate, $request->toDate, $request->type, $labels, $request->selected_user, $request->isUniReq);
        $data['status']                   = 1;
        return $data;
    }

    public function getIndividualRequirementAssignCounts($fromDate, $toDate, $type, $labels, $recruiters, $isUniq)
    {
        $startDate = Carbon::createFromFormat('m/d/Y', $fromDate);
        $endDate = Carbon::createFromFormat('m/d/Y', $toDate);

        if(!$recruiters){
            return [];
        }

        $collection = AssignToRecruiter::join('requirements', 'requirements.id', '=', 'assign_to_recruiters.requirement_id')
            ->leftJoin('admins', 'admins.id', '=', 'assign_to_recruiters.recruiter_id')
            ->whereIN('recruiter_id', $recruiters)
            ->whereBetween('assign_to_recruiters.created_at', [$startDate, $endDate])
            ->select(\DB::raw('DATE(assign_to_recruiters.created_at) AS date'), 'admins.name AS user', \DB::raw('COUNT(assign_to_recruiters.requirement_id) as count'), 'admins.id');

        if($isUniq) {
            $collection->where(function ($query) {
                $query->where('requirements.id', '=', \DB::raw('requirements.parent_requirement_id'));
                $query->orwhere('requirements.parent_requirement_id', '=', '0');
            });
        }
        $countsQuery = $collection->groupBy('date', 'recruiter_id')
            ->get();

        return $this->getUserAndDateWiseCounts($type, $labels, $countsQuery, 'recruiter', [], $recruiters);
    }

    public function getIndividualSubmission(Request $request)
    {
        $data['status'] = 0;
        if(empty($request->type) || empty($request->fromDate) || empty($request->toDate)){
            return $data;
        }

        $labels                   = $this->getTypeWiseDateLabels($request->type, $request->fromDate, $request->toDate);
        $data['labels']           = $labels;
        $data['submissionsCount'] = $this->getIndividualSubmissionCounts($request->fromDate, $request->toDate, $request->type, $labels, $request->bdmUser, $request->recUser, $request->isUniSub, $request->user_type);
        $data['status']           = 1;
        return $data;
    }

    public function getIndividualSubmissionCounts($fromDate, $toDate, $type, $labels, $bdmUser, $recruiters, $isUniSub, $userType)
    {
        $fromDate = \Carbon\Carbon::createFromFormat('m/d/Y', $fromDate)->format('Y-m-d');
        $toDate = \Carbon\Carbon::createFromFormat('m/d/Y', $toDate)->addDay()->format('Y-m-d');
        $submissionCounts = [];

        if($userType == 'recruiter' && $recruiters){
            $collection = Submission::leftJoin('admins', 'admins.id', '=', 'submissions.user_id')
                ->select(\DB::raw('DATE(submissions.created_at) AS date'), 'admins.name AS user', \DB::raw('COUNT(submissions.id) as count'), 'admins.id')
                ->whereIn('user_id', $recruiters)
                ->whereBetween('submissions.created_at', [$fromDate, $toDate]);

            if($isUniSub){
                $collection->where('submissions.id' ,\DB::raw('submissions.candidate_id'));
            }
            $submissionCounts = $collection->groupBy('date', 'submissions.user_id')
                ->get();
        } elseif ($userType == 'bdm' && $bdmUser){
            $collection = Requirement::leftjoin('submissions', 'submissions.requirement_id', '=', 'requirements.id')
                ->leftJoin('admins', 'admins.id', '=', 'requirements.user_id')
                ->select(\DB::raw('DATE(submissions.created_at) AS date'), 'admins.name AS user', \DB::raw('COUNT(submissions.id) as count'), 'admins.id')
                ->whereIn('requirements.user_id', $bdmUser)
                ->whereBetween('submissions.created_at', [$fromDate, $toDate]);
            if($isUniSub){
                $collection->where('submissions.id' ,\DB::raw('submissions.candidate_id'));
            }
            $submissionCounts = $collection->groupBy('date', 'requirements.user_id')
                ->get();
        }

        if(!$submissionCounts){
            return [];
        }

        return $this->getUserAndDateWiseCounts($type, $labels, $submissionCounts, $userType, $bdmUser, $recruiters);
    }

    public function getIndividualserved(Request $request)
    {
        $data['status'] = 0;
        if(empty($request->type) || empty($request->fromDate) || empty($request->toDate)){
            return $data;
        }

        $labels              = $this->getTypeWiseDateLabels($request->type, $request->fromDate, $request->toDate);
        $data['labels']      = $labels;
        $data['servedCount'] = $this->getIndividualServedCounts($request->fromDate, $request->toDate, $request->type, $labels, $request->bdmUser, $request->recUser, $request->isUniSub, $request->user_type);
        $data['status']      = 1;
        return $data;
    }

    public function getIndividualServedCounts($fromDate, $toDate, $type, $labels, $bdmUser, $recruiters, $isUniSub, $userType)
    {
        $fromDate = \Carbon\Carbon::createFromFormat('m/d/Y', $fromDate)->format('Y-m-d');
        $toDate = \Carbon\Carbon::createFromFormat('m/d/Y', $toDate)->addDay()->format('Y-m-d');
        $submissionCounts = [];

        if($userType == 'recruiter' && $recruiters){
            $collection = Submission::leftJoin('admins', 'admins.id', '=', 'submissions.user_id')
                ->select(\DB::raw('DATE(submissions.created_at) AS date'), 'admins.name AS user', \DB::raw('COUNT(DISTINCT submissions.requirement_id) as count'), 'admins.id')
                ->whereIn('user_id', $recruiters)
                ->whereBetween('submissions.created_at', [$fromDate, $toDate]);

            if($isUniSub){
                $collection->where('submissions.id' ,\DB::raw('submissions.candidate_id'));
            }
            $submissionCounts = $collection->groupBy('date', 'submissions.user_id')
                ->get();
        } elseif ($userType == 'bdm' && $bdmUser){
            $collection = Requirement::leftjoin('submissions', 'submissions.requirement_id', '=', 'requirements.id')
                ->leftJoin('admins', 'admins.id', '=', 'requirements.user_id')
                ->select(\DB::raw('DATE(submissions.created_at) AS date'), 'admins.name AS user', \DB::raw('COUNT(DISTINCT submissions.requirement_id) as count'), 'admins.id')
                ->whereIn('requirements.user_id', $bdmUser)
                ->whereBetween('submissions.created_at', [$fromDate, $toDate]);
            if($isUniSub){
                $collection->where('submissions.id' ,\DB::raw('submissions.candidate_id'));
            }
            $submissionCounts = $collection->groupBy('date', 'requirements.user_id')
                ->get();
        }

        if(!$submissionCounts){
            return [];
        }

        return $this->getUserAndDateWiseCounts($type, $labels, $submissionCounts, $userType, $bdmUser, $recruiters);
    }

    public function getIndividualRequirementCount(Request $request)
    {
        $data['status'] = 0;
        if(empty($request->type) || empty($request->fromDate) || empty($request->toDate)){
            return $data;
        }

        $labels                           = $this->getTypeWiseDateLabels($request->type, $request->fromDate, $request->toDate);
        $data['labels']                   = $labels;
        $data['requirementCount']         = $this->getIndividualRequirementCounts($request->fromDate, $request->toDate, $request->type, $labels, $request->selected_user, $request->isUniReq);
        $data['status']                   = 1;
        return $data;
    }

    public function getIndividualRequirementCounts($fromDate, $toDate, $type, $labels, $bdms, $isUniq)
    {
        $startDate = Carbon::createFromFormat('m/d/Y', $fromDate);
        $endDate = Carbon::createFromFormat('m/d/Y', $toDate);

        if(!$bdms){
            return [];
        }

        $collection = Requirement::leftJoin('admins', 'admins.id', '=', 'requirements.user_id')
            ->select(\DB::raw('DATE(requirements.created_at) AS date'), 'admins.name AS user', \DB::raw('COUNT(requirements.id) as count'), 'admins.id')
            ->whereIn('requirements.user_id', $bdms)
            ->whereBetween('requirements.created_at', [$startDate, $endDate]);

        if($isUniq) {
            $collection->where(function ($query) {
                $query->where('requirements.id', '=', \DB::raw('requirements.parent_requirement_id'));
                $query->orwhere('requirements.parent_requirement_id', '=', '0');
            });
        }

        $countsQuery = $collection->groupBy('date', 'requirements.user_id')
            ->get();

        return $this->getUserAndDateWiseCounts($type, $labels, $countsQuery, 'bdm', $bdms, []);
    }

    public function getIndividualbdmAccept(Request $request)
    {
        $data['status'] = 0;
        if(empty($request->type) || empty($request->fromDate) || empty($request->toDate)){
            return $data;
        }

        $labels                   = $this->getTypeWiseDateLabels($request->type, $request->fromDate, $request->toDate);
        $data['labels']           = $labels;
        $data['bdmAcceptCount']   = $this->getIndividualStatusCounts('status', Submission::STATUS_ACCEPT, $labels, $request->fromDate, $request->toDate, $request->user_type, $request->bdmUser, $request->recUser, $request->type, $request->frame_type);
        $data['status']           = 1;
        return $data;
    }

    public function getIndividualSubmittedEndClient(Request $request)
    {
        $data['status'] = 0;
        if(empty($request->type) || empty($request->fromDate) || empty($request->toDate)){
            return $data;
        }

        $labels                         = $this->getTypeWiseDateLabels($request->type, $request->fromDate, $request->toDate);
        $data['labels']                 = $labels;
        $data['bdmSubToEndClientCount'] = $this->getIndividualStatusCounts('pv_status', Submission::STATUS_SUBMITTED_TO_END_CLIENT, $labels, $request->fromDate, $request->toDate, $request->user_type, $request->bdmUser, $request->recUser, $request->type, $request->frame_type);
        $data['status']                 = 1;
        return $data;
    }

    public function getIndividualStatusCounts($filedName, $status, $labels, $fromDate, $toDate, $userType, $bdmUser, $recruiterUser, $type, $frameType)
    {
        $fromDate = \Carbon\Carbon::createFromFormat('m/d/Y', $fromDate)->format('Y-m-d');
        $toDate = \Carbon\Carbon::createFromFormat('m/d/Y', $toDate)->addDay()->format('Y-m-d');

        $date = 'submissions.bdm_status_updated_at';
        if($filedName == 'pv_status'){
            $date = 'submissions.pv_status_updated_at';
        }
        $statusCounts = [];
        if($userType == 'bdm' && $bdmUser){
            $collection = Requirement::leftJoin('submissions', 'requirements.id', '=', 'submissions.requirement_id')
                ->leftJoin('admins', 'admins.id', '=', 'requirements.user_id')
                ->whereIn('requirements.user_id', $bdmUser)
                ->where('submissions.'.$filedName, $status);
            if($frameType == 'submission_frame'){
                $collection->whereBetween('submissions.created_at', [$fromDate, $toDate]);
            } else {
                $collection->whereBetween($date, [$fromDate, $toDate]);
            }
            $statusCounts = $collection->select(\DB::raw("DATE($date) AS date"), 'admins.name AS user', \DB::raw('COUNT(submissions.id) as count'), 'admins.id')
                ->groupBy('date', 'requirements.user_id')
                ->get();

        } elseif($userType == 'recruiter' && $recruiterUser) {
            $collection = Submission::whereIn('user_id', $recruiterUser)
                ->leftJoin('admins', 'admins.id', '=', 'submissions.user_id')
                ->where('submissions.'.$filedName, $status);
            if($frameType == 'submission_frame'){
                $collection->whereBetween('created_at', [$fromDate, $toDate]);
            } else {
                $collection->whereBetween($date, [$fromDate, $toDate]);
            }
            $statusCounts = $collection->select(\DB::raw("DATE($date) AS date"), 'admins.name AS user', \DB::raw('COUNT(submissions.id) as count'), 'admins.id')
                ->groupBy('date', 'submissions.user_id')
                ->get();
        }

        if(!$statusCounts){
            return [];
        }

        return $this->getUserAndDateWiseCounts($type, $labels, $statusCounts, $userType, $bdmUser, $recruiterUser);
    }
}
