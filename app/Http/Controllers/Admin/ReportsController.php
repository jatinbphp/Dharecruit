<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ReportsTrait;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    use ReportsTrait;
    public function index(Request  $request, $type = 'efficiency')
    {
        $data = [];
        $data['menu'] = 'Efficiency Report';

        switch ($type) {
            case "efficiency":
                if(!empty($request->all())){
                    return $this->getEfficiencyData($request);
                }
                break;
            default:
                $data = $this->getEfficiencyData($request);
        }
        return view('admin.reports.'.$type, $data);
    }

    public function getEfficiencyData ($request)
    {
        $fromDate   = $request->fromDate;
        $toDate     = $request->toDate;

        $data['content'] = '';

        if(!$fromDate || !$toDate){
            return  $data;
        }

        $efficiencyData['bdmsData'] = $this->getAllBdmsData($request);
        $efficiencyData['recruitersData'] = $this->getAllRecruitersData($request);
        $view = view('admin.reports.efficiency_data', $efficiencyData)->render();

        $data['content'] = $view;
        return $data;
    }
}
