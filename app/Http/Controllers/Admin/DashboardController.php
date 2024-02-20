<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
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

    public function getMonthlyInterviewChartData()
    {
        $data['status'] = 1;
        $data['label'] = $this->getCurrentMonthDateLabels('monthly');
        $data['interviewCounts'] = $this->getMonthlyInterviewCounts();
        return $data;
    }

    public function getMonthlyInterviewCounts()
    {
        $currentDate = now();
        $startDate = $currentDate->copy()->startOfYear();
        $endDate = $currentDate->copy()->endOfMonth();

        $currentYear = Carbon::now()->year;

        $startDateOfYear = Carbon::create($currentYear, 1, 1)->startOfDay()->format('Y-m-d H:i:s');
        $endDateOfYear = Carbon::create($currentYear, 12, 31)->endOfDay()->format('Y-m-d H:i:s');

        if(getLoggedInUserRole() == 'bdm'){
            $countsQuery = Requirement::leftJoin('submissions', 'requirements.id', '=', 'submissions.requirement_id')
                ->leftJoin('interviews', 'submissions.id', '=', 'interviews.submission_id')
                ->leftJoin('admins', 'admins.id', '=', 'requirements.user_id')
                ->where('admins.role', 'bdm')
                ->where('admins.status', 'active')
                ->whereBetween('interviews.created_at', [$startDateOfYear, $endDateOfYear])
                ->where('requirements.user_id', getLoggedInUserId())
                ->groupBy('date')
                ->select(\DB::raw('DATE(requirements.created_at) AS date'), \DB::raw('COUNT(interviews.id) as count'))
                ->pluck('count', 'date')
                ->toArray();
        } else {
            $countsQuery = Submission::leftJoin('requirements', 'submissions.requirement_id', '=', 'requirements.id')
                ->leftJoin('interviews', 'submissions.id', '=', 'interviews.submission_id')
                ->leftJoin('admins', 'admins.id', '=', 'submissions.user_id')
                ->where('admins.role', 'recruiter')
                ->where('admins.status', 'active')
                ->whereBetween('interviews.created_at', [$startDateOfYear, $endDateOfYear])
                ->where('submissions.user_id', getLoggedInUserId())
                ->groupBy('date')
                ->select(\DB::raw('DATE(requirements.created_at) AS date'), \DB::raw('COUNT(interviews.id) as count'))
                ->pluck('count', 'date')
                ->toArray();
        }

        $counts = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $date = $currentDate->format('Y-m-d');
            $month = $currentDate->format('Y-m');
            $counts[] = array_sum(array_filter($countsQuery, function ($key) use ($month) {
                return strpos($key, $month) === 0;
            }, ARRAY_FILTER_USE_KEY));
            $currentDate->addMonthNoOverflow();
        }
        return $counts;
    }

    public function getBdmStatusData()
    {
        $data['labels'] = array_values(Submission::$status);
        $data['counts'] = $this->getBdmStatusCounts();
        $data['status'] = 1;
        return $data;
    }

    public function getBdmStatusCounts()
    {
        $bdmStatus = Submission::$status;
        $bdmStatus = array_fill_keys(array_keys($bdmStatus), 0);

        $submissionCounts = Submission::select('status', \DB::raw('count(*) as count'))
            ->whereIn('status', array_keys($bdmStatus))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        foreach ($bdmStatus as $status => $count) {
            if (isset($submissionCounts[$status])) {
                $bdmStatus[$status] = $submissionCounts[$status];
            }
        }

        return array_values($bdmStatus);
    }

    public function getPvStatusData()
    {
        $data['labels'] = array_values(Submission::$pvStatus);
        $data['counts'] = $this->getPvStatusCount();
        $data['status'] = 1;
        return $data;
    }
    public function getPvStatusCount()
    {
        $pvStatus = Submission::$pvStatus;
        $pvStatus = array_fill_keys(array_keys($pvStatus), 0);
        $submissionCounts = Submission::select('pv_status', \DB::raw('count(*) as count'))
            ->whereIn('pv_status', array_keys($pvStatus))
            ->groupBy('pv_status')
            ->get()
            ->pluck('count', 'pv_status')
            ->toArray();

        foreach ($submissionCounts as $status => $count) {
            if (isset($submissionCounts[$status])) {
                $pvStatus[$status] = $submissionCounts[$status];
            }
        }

        return array_values($pvStatus);
    }

    public function getInterviewStatusData()
    {
        $interviewStatus = Interview::$interviewStatusOptions;
        unset($interviewStatus['']);
        $data['labels'] = array_values($interviewStatus);
        $data['counts'] = $this->getInterviewStatusCounts();
        $data['status'] = 1;
        return $data;
    }

    public function getInterviewStatusCounts()
    {
        $interviewStatus = Interview::$interviewStatusOptions;
        unset($interviewStatus['']);
        $interviewStatus = array_fill_keys(array_keys($interviewStatus), 0);

        $intervewCounts = Interview::select('status', \DB::raw('count(*) as count'))
            ->whereIn('status', array_keys($interviewStatus))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        foreach ($interviewStatus as $status => $count) {
            if (isset($intervewCounts[$status])) {
                $interviewStatus[$status] = $intervewCounts[$status];
            }
        }

        return array_values($interviewStatus);
    }
}
