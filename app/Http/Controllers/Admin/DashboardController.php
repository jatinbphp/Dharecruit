<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AssignToRecruiter;
use App\Models\Interview;
use App\Models\Requirement;
use App\Models\Submission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        $data['menu'] = 'Dashboard';
        $data['loggedUser'] = $this->getUser();
        $data['users'] = Admin::where('id', '!=', Auth::user()->id)->where('role','admin')->count();
        $data['monthlyLabels'] = $this->getCurrentMonthDateLabels('monthly');
        $data['monthlyRequiremtCounts'] = $this->getCountsForModel('App\Models\Requirement', 'monthly', 0);
        $data['monthlySubmissionCounts'] = $this->getCountsForModel('App\Models\Submission', 'monthly', 0);
        return view('admin.dashboard',$data);
    }

    public function getTypeWiseChartData(Request $request)
    {
        $data['status'] = 0;
        if(empty($request->type)){
            return $data;
        }

        $data['label'] = $this->getCurrentMonthDateLabels($request->type);
        $data['requiremtCounts'] = $this->getCountsForModel('App\Models\Requirement', $request->type, $request->is_uni_req);
        $data['submissionCounts'] = $this->getCountsForModel('App\Models\Submission', $request->type, $request->is_uni_sub);
        $data['status'] = 1;
        return $data;
    }

    public function getCurrentMonthDateLabels($type)
    {
        $currentDate = \Carbon\Carbon::now();
        switch ($type) {
            case 'weekly':
                $numWeeks = $currentDate->endOfMonth()->weekOfMonth;
                $labels = [];
                $startDate = $currentDate->startOfMonth();

                for ($week = 1; $week <= $numWeeks; $week++) {
                    $startOfWeek = $startDate->copy()->startOfWeek();
                    $endOfWeek = $startDate->copy()->endOfWeek();

                    $labels[] = $startOfWeek->format('m/d/y') . ' to ' . $endOfWeek->format('m/d/y');
                    $startDate->addWeek();
                }
                break;
            case 'monthly':
                $startDate = 1;
                $endDate = 12;
                $step = 1;
                $labels = [];
                for ($i = $startDate; $i <= $endDate; $i += $step) {
                    $labels[] = \Carbon\Carbon::createFromDate(null, $i, null)->format('M');
                }
                break;
            case 'daily':
            default:
                $startDate = 1;
                $endDate = $currentDate->daysInMonth;
                $labels = [];
                for ($i = $startDate; $i <= $endDate; $i++) {
                    $date = $i . '-' . $currentDate->format('M');
                    $labels[] = $date;
                }

            break;
        }
        return $labels;
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

    public function getInterviewChartData(Request $request)
    {
        $data['status'] = 0;
        if(empty($request->type) || empty($request->fromDate) || empty($request->toDate)){
            return $data;
        }

        $data['interviewCounts'] = $this->getInterviewCounts($request->fromDate, $request->toDate, $request->type);
        $data['status'] = 1;
        return $data;
    }

    public function getInterviewCounts($fromDate, $toDate, $type)
    {
        $fromDate = \Carbon\Carbon::createFromFormat('m/d/Y', $fromDate)->format('Y-m-d');
        $toDate = \Carbon\Carbon::createFromFormat('m/d/Y', $toDate)->addDay()->format('Y-m-d');
        $userWiseInterviewData = [];
        if($type == 'bdm'){
            $collection = Requirement::leftJoin('submissions', 'requirements.id', '=', 'submissions.requirement_id')
                ->leftJoin('interviews', 'submissions.id', '=', 'interviews.submission_id')
                ->leftJoin('admins', 'admins.id', '=', 'requirements.user_id')
                ->where('admins.role', 'bdm')
                ->where('admins.status', 'active')
                ->whereBetween('interviews.created_at', [$fromDate, $toDate])
                ->groupBy('requirements.user_id')
                ->selectRaw('admins.id as id, COUNT(interviews.id) as count');
                if(isManager()){
                    $collection->whereIn('admins.id', getManagerAllUsers());
                } elseif (isLeadUser()){
                    $collection->whereIn('admins.id', getTeamMembers());
                }
            $interviewCount = $collection->pluck('count', 'id')
                ->toArray();
        } else {
            $collection = Submission::leftJoin('interviews', 'submissions.id', '=', 'interviews.submission_id')
                ->leftJoin('admins', 'admins.id', '=', 'submissions.user_id')
                ->where('admins.role', 'recruiter')
                ->where('admins.status', 'active')
                ->whereBetween('interviews.created_at', [$fromDate, $toDate])
                ->groupBy('submissions.user_id')
                ->selectRaw('admins.id as id, COUNT(interviews.id) as count');
                if(isManager()){
                    $collection->whereIn('admins.id', getManagerAllUsers());
                } elseif (isLeadUser()){
                    $collection->whereIn('admins.id', getTeamMembers());
                }
            $interviewCount = $collection->pluck('count', 'id')
                ->toArray();
        }

        asort($interviewCount);

        if($interviewCount && count($interviewCount)){
            foreach ($interviewCount as $userId => $count){
                if($type == 'bdm'){
                    $withTeamNameData = Admin::getActiveBDM();
                } else {
                    $withTeamNameData = Admin::getActiveRecruiter();
                }
                if(array_key_exists($userId, $withTeamNameData)){
                    $userWiseInterviewData[$withTeamNameData[$userId]] = $count;
                }
            }
        }

        return $userWiseInterviewData;
    }

    public function getRequirementVsServed(Request $request)
    {
        $data['status'] = 0;
        if(empty($request->type)){
            return $data;
        }

        $data['label'] = $this->getCurrentMonthDateLabels($request->type);
        $data['requiremtCounts'] = $this->getCountsForModel('App\Models\Requirement', $request->type, $request->is_uniq_req);
        $data['servedCounts'] = $this->getservedRequirementCount($request->type, $request->is_uniq_req);
        $data['status'] = 1;
        return $data;
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

    public function getMonthlyInterviewChartData(Request $request)
    {
        $data['status'] = 0;
        if(empty($request->fromDate) || empty($request->toDate)){
            return $data;
        }
        $labels = $this->getCurrentTypeWiseDateLabels($request->type, $request->fromDate, $request->toDate);
        $data['status'] = 1;
        $data['label'] = $labels;
        $data['interviewCounts'] = $this->getMonthlyInterviewCounts($labels, $request->type, $request->fromDate, $request->toDate);
        return $data;
    }

    public function getMonthlyInterviewCounts($label, $type, $fromDate, $toDate)
    {
        $startDate = Carbon::createFromFormat('m/d/Y', $fromDate);
        $endDate = Carbon::createFromFormat('m/d/Y', $toDate);

        if(getLoggedInUserRole() == 'bdm'){
            $countsQuery = Requirement::leftJoin('submissions', 'requirements.id', '=', 'submissions.requirement_id')
                ->leftJoin('interviews', 'submissions.id', '=', 'interviews.submission_id')
                ->leftJoin('admins', 'admins.id', '=', 'requirements.user_id')
                ->where('admins.role', 'bdm')
                ->where('admins.status', 'active')
                ->whereBetween('interviews.created_at', [$startDate, $endDate])
                ->where('requirements.user_id', getLoggedInUserId())
                ->groupBy('date')
                ->select(\DB::raw('DATE(interviews.created_at) AS date'), \DB::raw('COUNT(interviews.id) as count'))
                ->pluck('count', 'date')
                ->toArray();
        } else {
            $countsQuery = Submission::leftJoin('requirements', 'submissions.requirement_id', '=', 'requirements.id')
                ->leftJoin('interviews', 'submissions.id', '=', 'interviews.submission_id')
                ->leftJoin('admins', 'admins.id', '=', 'submissions.user_id')
                ->where('admins.role', 'recruiter')
                ->where('admins.status', 'active')
                ->whereBetween('interviews.created_at', [$startDate, $endDate])
                ->where('submissions.user_id', getLoggedInUserId())
                ->groupBy('date')
                ->select(\DB::raw('DATE(interviews.created_at) AS date'), \DB::raw('COUNT(interviews.id) as count'))
                ->pluck('count', 'date')
                ->toArray();
        }
        return $this->getLabelWiseData($type, $label, $countsQuery);
    }

    public function getBdmStatusData(Request $request)
    {
        $data['status'] = 0;
        if(empty($request->fromDate) || empty($request->toDate) || empty($request->type)){
            return $data;
        }
        $data['labels'] = array_values(Submission::$status);
        $data['counts'] = $this->getBdmStatusCounts($request->fromDate, $request->toDate, $request->type, $request->bdmUser, $request->recUser);
        $data['status'] = 1;
        return $data;
    }

    public function getBdmStatusCounts($fromDate, $toDate, $type, $bdmUser, $recruiterUser)
    {
        $fromDate = \Carbon\Carbon::createFromFormat('m/d/Y', $fromDate)->format('Y-m-d');
        $toDate = \Carbon\Carbon::createFromFormat('m/d/Y', $toDate)->addDay()->format('Y-m-d');
        $bdmStatus = Submission::$status;
        $bdmStatus = array_fill_keys(array_keys($bdmStatus), 0);
        $submissionCounts = [];

        if($type == 'recruiter' && $recruiterUser){
            $submissionCounts = Submission::select('status', \DB::raw('count(*) as count'))
                ->whereIn('status', array_keys($recruiterUser))
                ->whereIn('user_id', $recruiterUser)
                ->whereBetween('bdm_status_updated_at', [$fromDate, $toDate])
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
        } elseif ($type == 'bdm' && $bdmUser){
            $submissionCounts = Requirement::leftjoin('submissions', 'submissions.requirement_id', '=', 'requirements.id')
                ->select('submissions.status', \DB::raw('count(*) as count'))
                ->whereIn('requirements.user_id', $bdmUser)
                ->whereIn('submissions.status', array_keys($bdmStatus))
                ->whereBetween('submissions.bdm_status_updated_at', [$fromDate, $toDate])
                ->groupBy('submissions.status')
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
        $data['labels'] = array_values(Submission::$pvStatus);
        $data['counts'] = $this->getPvStatusCount($request->fromDate, $request->toDate, $request->type, $request->bdmUser, $request->recUser);
        $data['status'] = 1;
        return $data;
    }
    public function getPvStatusCount($fromDate, $toDate, $type, $bdmUser, $recruiterUser)
    {
        $fromDate = \Carbon\Carbon::createFromFormat('m/d/Y', $fromDate)->format('Y-m-d');
        $toDate = \Carbon\Carbon::createFromFormat('m/d/Y', $toDate)->addDay()->format('Y-m-d');
        $pvStatus = Submission::$pvStatus;
        $pvStatus = array_fill_keys(array_keys($pvStatus), 0);

        $submissionCounts = [];

        if($type == 'recruiter' && $recruiterUser) {
            $submissionCounts = Submission::select('pv_status', \DB::raw('count(*) as count'))
                ->whereIn('pv_status', array_keys($pvStatus))
                ->whereIn('user_id', $recruiterUser)
                ->whereBetween('pv_status_updated_at', [$fromDate, $toDate])
                ->groupBy('pv_status')
                ->pluck('count', 'pv_status')
                ->toArray();
        } elseif ($type == 'bdm' && $bdmUser){
            $submissionCounts = Requirement::leftjoin('submissions', 'submissions.requirement_id', '=', 'requirements.id')
                ->select('submissions.pv_status', \DB::raw('count(*) as count'))
                ->whereIn('requirements.user_id', $bdmUser)
                ->whereIn('submissions.pv_status', array_keys($pvStatus))
                ->whereBetween('submissions.pv_status_updated_at', [$fromDate, $toDate])
                ->groupBy('submissions.pv_status')
                ->pluck('count', 'submissions.pv_status')
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
        $interviewStatus = Interview::$interviewStatusOptions;
        unset($interviewStatus['']);
        $data['labels'] = array_values($interviewStatus);
        $data['counts'] = $this->getInterviewStatusCounts($request->fromDate, $request->toDate, $request->type, $request->bdmUser, $request->recUser);
        $data['status'] = 1;
        return $data;
    }

    public function getInterviewStatusCounts($fromDate, $toDate, $type, $bdmUser, $recUser)
    {
        $fromDate = \Carbon\Carbon::createFromFormat('m/d/Y', $fromDate)->format('Y-m-d');
        $toDate = \Carbon\Carbon::createFromFormat('m/d/Y', $toDate)->addDay()->format('Y-m-d');
        $interviewStatus = Interview::$interviewStatusOptions;
        unset($interviewStatus['']);
        $interviewStatus = array_fill_keys(array_keys($interviewStatus), 0);

        $submissionCounts = [];

        if($type == 'recruiter' && $recUser) {
            $intervewCounts = Submission::leftJoin('interviews', 'submissions.id', '=', 'interviews.submission_id')
                ->selectRaw('interviews.status, COUNT(interviews.id) as count')
                ->whereBetween('submissions.interview_status_updated_at', [$fromDate, $toDate])
                ->whereIn('submissions.user_id', $recUser)
                ->whereIn('interviews.status', array_keys($interviewStatus))
                ->groupBy('interviews.status')
                ->pluck('count', 'interviews.status')
                ->toArray();
        } elseif ($type == 'bdm' && $bdmUser){
            $intervewCounts = Requirement::leftJoin('submissions', 'requirements.id', '=', 'submissions.requirement_id')
                ->leftJoin('interviews', 'submissions.id', '=', 'interviews.submission_id')
                ->selectRaw('interviews.status, COUNT(interviews.id) as count')
                ->whereBetween('submissions.interview_status_updated_at', [$fromDate, $toDate])
                ->whereIn('requirements.user_id', $bdmUser)
                ->whereIn('interviews.status', array_keys($interviewStatus))
                ->groupBy('interviews.status')
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

    public function getCurrentTypeWiseDateLabels($type, $fromDate, $toDate)
    {
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

    public function getRequirementAssignedVsServed(Request $request)
    {
        $data['status'] = 0;
        if(empty($request->type)){
            return $data;
        }

        $labels = $this->getCurrentTypeWiseDateLabels($request->type, $request->fromDate, $request->toDate);
        $data['label'] = $labels;
        $data['assignedRequiremenrtCount'] = array_values($this->getAssignedRequirementCount($labels, $request->fromDate, $request->toDate, $request->type, $request->selected_user));
        $data['recruiterservedCounts']     = array_values($this->getRecruiterServedCount($labels, $request->fromDate, $request->toDate, $request->type, $request->selected_user));
        $data['status'] = 1;
        return $data;
    }

    public function getAssignedRequirementCount($labels, $fromDate, $toDate, $type, $recruiters)
    {
        $startDate = Carbon::createFromFormat('m/d/Y', $fromDate);
        $endDate = Carbon::createFromFormat('m/d/Y', $toDate);
        if(!$recruiters){
            $recruiters[] = getLoggedInUserId();
        }

        $countsQuery = AssignToRecruiter::whereIN('recruiter_id', $recruiters)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(\DB::raw('DATE(created_at) AS date'), \DB::raw('COUNT(requirement_id) as count'))
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        return $this->getLabelWiseData($type, $labels, $countsQuery);
    }

    public function getRecruiterServedCount($labels, $fromDate, $toDate, $type, $recruiters)
    {
        $startDate = Carbon::createFromFormat('m/d/Y', $fromDate);
        $endDate = Carbon::createFromFormat('m/d/Y', $toDate);

        if(!$recruiters){
            $recruiters[] = getLoggedInUserId();
        }

        $countsQuery = Submission::whereIn('user_id', $recruiters)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(\DB::raw('DATE(created_at) AS date'), \DB::raw('COUNT(DISTINCT requirement_id) as count'))
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        return $this->getLabelWiseData($type, $labels, $countsQuery);
    }

    public function getLabelWiseData($type, $labels, $counts)
    {
        $preparedData = array_fill_keys($labels, 0);

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

    public function getRequirementAssignedVsSubmissions(Request $request)
    {
        $data['status'] = 0;
        if(empty($request->type)){
            return $data;
        }

        $labels = $this->getCurrentTypeWiseDateLabels($request->type, $request->fromDate, $request->toDate);
        $data['label'] = $labels;
        $data['assignedRequiremenrtCount'] = array_values($this->getAssignedRequirementCount($labels, $request->fromDate, $request->toDate, $request->type, $request->selected_user));
        $data['submissionCount']           = array_values($this->getSubmissionsCount($labels, $request->fromDate, $request->toDate, $request->type, $request->selected_user, $request->isUniSubmission));
        $data['status'] = 1;
        return $data;
    }

    public function getSubmissionsCount($labels, $fromDate, $toDate, $type, $recruiters, $isUniqSubmission = 0)
    {
        $startDate = Carbon::createFromFormat('m/d/Y', $fromDate);
        $endDate = Carbon::createFromFormat('m/d/Y', $toDate);

        if(!$recruiters){
            $recruiters[] = getLoggedInUserId();
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
}
