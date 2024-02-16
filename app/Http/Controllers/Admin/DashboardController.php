<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Requirement;
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
        $data['monthlyRequiremtCounts'] = $this->getCountsForModel('App\Models\Requirement', 'monthly');
        $data['monthlySubmissionCounts'] = $this->getCountsForModel('App\Models\Submission', 'monthly');
        return view('admin.dashboard',$data);
    }

    public function getTypeWiseChartData(Request $request)
    {
        $data['status'] = 0;
        if(empty($request->type)){
            return $data;
        }

        $data['label'] = $this->getCurrentMonthDateLabels($request->type);
        $data['requiremtCounts'] = $this->getCountsForModel('App\Models\Requirement', $request->type);
        $data['submissionCounts'] = $this->getCountsForModel('App\Models\Submission', $request->type);
        $data['status'] = 1;
        return $data;
    }

    public function getCurrentMonthDateLabels($type)
    {
        $currentDate = \Carbon\Carbon::now();
        switch ($type) {
            case 'weekly':
                $numWeeks = $currentDate->endOfMonth()->weekOfMonth;
                $allWeeks = range(1, $numWeeks);
                $labels = [];
                foreach ($allWeeks as $allWeeks) {
                    // Concatenate "- Week" after the label and add it to the modifiedLabels array
                    $labels[] = 'Week-'.$allWeeks;
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

    private function getCountsForModel($modelName, $type)
    {
        $currentDate = now();
        switch ($type) {
            case 'monthly':
                // Get the start and end dates of each month from January to the current month
                $startDate = $currentDate->copy()->startOfYear();
                $endDate = $currentDate->copy()->endOfMonth();
                break;
            case 'weekly':
                // Get the start and end dates of each week from the first week to the current week of the current month
                $startDate = $currentDate->copy()->startOfMonth()->startOfWeek();
                $endDate = $currentDate->copy()->endOfMonth()->endOfWeek();
                break;
            case 'daily':
            default:
                // Get the start and end dates of the current month from the first day to the current day
                $startDate = $currentDate->copy()->startOfMonth();
                $endDate = $currentDate->copy()->endOfMonth();
                break;
        }

        $countsQuery = $modelName::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d") as date, COUNT(*) as count')
            ->groupBy('date')
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
                    $week = $currentDate->weekOfMonth;
                    $counts[] = array_sum(array_filter($countsQuery, function ($key) use ($week) {
                        return \Carbon\Carbon::parse($key)->weekOfMonth === $week;
                    }, ARRAY_FILTER_USE_KEY));
                    $currentDate->addWeek(); // Move to the next week
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
}
