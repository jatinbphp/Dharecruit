<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PVCompany;
use App\Traits\POCTrait;
use App\Traits\ReportsTrait;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    use ReportsTrait;

    public function __construct(){
        $this->middleware('auth');
        $this->middleware('accessright:manage_reports');
    }

    public function index(Request  $request, $type = 'efficiency', $subType = 'sub_received')
    {
        $data = [];
        $data['menu'] = 'Efficiency Report';
        $data['subType'] = $subType;
        $data['type'] = $type;

        switch ($type) {
            case "efficiency":
                $data['menu'] = 'Efficiency Report';
                if(!empty($request->all())){
                    return $this->getEfficiencyData($request, $subType);
                }
                break;
            case "p_v_report":
                $data['menu'] = 'PV Company Report';
                $data['subType'] = '';
                if(!empty($request->all())){
                    return $this->getPvCompanyData($request);
                }
                break;
            case "poc_report":
                $data['menu'] = 'POC Report';
                $data['subType'] = '';
                $data['hideColumns'] = $this->getPocHideColumns();
                $data['totalShowCompanyColumn'] = $this->getTotalShowCompanyColumns();
                if(!empty($request->all())){
                    return $this->getPOCData($request);
                }
                break;
            case "employer_report":
                $data['menu'] = 'Employer Report';
                $data['subType'] = '';
                if(!empty($request->all())){
                    return $this->getEmployerData($request);
                }
                break;
            default:
                $data = $this->getEfficiencyData($request, $subType);
        }
        return view('admin.reports.'.$type, $data);
    }

    public function getEfficiencyData ($request, $subType): array
    {
        if($subType == 'sub_sent'){
            $efficiencyData['recruitersData'] = $this->getAllRecruitersData($request);
            $efficiencyData['recruiterTimeFrame'] = $this->getRecruiterTimeFrameData($request);
        } else {
            $efficiencyData['bdmsData'] = $this->getAllBdmsData($request);
            $efficiencyData['bdmTimeFrame'] = $this->getBdmTimeFrameData($request);
        }

        $data['content'] = view('admin.reports.efficiency_data', $efficiencyData)->render();
        return $data;
    }

    public function getPvCompanyData($request): array
    {
        $pvData['pvFilterData'] = $this->getPvFilterData($request);
        $data['content']        = view('admin.reports.p_v_report_data', $pvData)->render();

        return $data;
    }

    public function getPOCData($request): array
    {
        $pvData['pocFilterData'] = $this->getPOCFilterData($request);
        $data['content']        = view('admin.reports.poc_report_data', $pvData)->render();

        return $data;
    }

    public function getPocNames(Request $request)
    {
        $companyName = $request->input('companyName');
        $pocNames = [];
        if($companyName){
            $pocNames = PVCompany::whereIn('name', $companyName)->distinct()->orderBY('name')->pluck('poc_name', 'poc_name');
        }
        return response()->json($pocNames);
    }

    public function getEmployerData($request)
    {
        $employerData['employerFilterData'] = $this->getEmployerFilterData($request);
        $data['content']                    = view('admin.reports.employer_report_data', $employerData)->render();

        return $data;
    }
}
