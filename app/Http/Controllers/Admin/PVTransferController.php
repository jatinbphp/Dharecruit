<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\POCTransfer;
use App\Models\PVCompany;
use App\Models\Requirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PVTransferController extends Controller
{
    protected $_configurationDays = 0;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['menu'] = "PV Data & Transfer";
        $data['headings'] = $this->getHeadings();
        $data['pvCompaniesData'] = $this->getPvCompaniesData();

        return view('admin.pv_transfer.index', $data);
    }

    public function getPvCompanyData(Request $request)
    {
        $status = 0;
        if(!empty($request->id)){
            $pvCompany = PVCompany::where('id',$request->id)->first();

            if(!empty($pvCompany)){
                $status = 1;
                $data['pv_company_data'] = $pvCompany;
            }
        }
        $data['status'] = $status;

        return $data;
    }

    public function transferPoc(Request $request)
    {
        $status = 0;
        if(!empty($request->user_id) && !empty($request->company_id)){
            $pvCompanyRow = PVCompany::where('id', $request->company_id)->first();
            if(!empty($pvCompanyRow)){
                $this->transferPocData($pvCompanyRow, $request->user_id, POCTransfer::TRANSFER_TYPE_SELF);
                $status = 1;
            }
        }

        $data['status'] = $status;

        return $data;
    }

    public function getDaysConfiguration()
    {
        if($this->_configurationDays){
            return $this->_configurationDays;
        }

        $defaultDays = 14;
        $settingRow =  \App\Models\Setting::where('name', 'transfer_poc_if_req_not_post_days')->first();

        if(!empty($settingRow) && $settingRow->value){
            $defaultDays = $settingRow->value;
        }

        return $this->_configurationDays = $defaultDays;
    }

    public function getHeadings()
    {
        $configDays = $this->getDaysConfiguration();
        return [
            'Date Added',
            'Who Added',
            'Date Transformed',
            'Last Register To',
            'Key Transfer',
            "Last $configDays Days Any Req.",
            'Your Last Post',
            '# Of Postings',
            'Vendor Company',
            'Vendor Name',
            'Vendor Email',
            'Vendor Phone Number',
            'Transfer POC',
        ];
    }

    public function getPvCompaniesData()
    {
        $loggedInUserId = getLoggedInUserId();
        $pvCompanies = PVCompany::with('pocTransfers')->where('assigned_user_id', $loggedInUserId)
            ->get();

        if(empty($pvCompanies)){
            return [];
        }
        $allPvCompanyData = [];

        foreach ($pvCompanies as $pvCompany) {
            $pvCompanyData = [];
            $id = $pvCompany->id;
            $transferType = optional($pvCompany->pocTransfers->first())->transfer_type;
            $transferType1 = isset(POCTransfer::$transferKeyValuePair[$transferType]) ? POCTransfer::$transferKeyValuePair[$transferType] : '';

            $pvCompanyData['date_added'] = ($loggedInUserId == $pvCompany->user_id) ? $this->getFormattedDate($pvCompany->created_at) : '';
            $pvCompanyData['who_added'] = ($loggedInUserId == $pvCompany->user_id) ? 'Me' : '';
            $pvCompanyData['date_transformed'] = $this->getFormattedDate(optional($pvCompany->pocTransfers->first())->created_at);
            $pvCompanyData['who_transformed'] = (optional($pvCompany->pocTransfers->first())->transfer_by) ? Admin::getUserNameBasedOnId(optional($pvCompany->pocTransfers->first())->transfer_by) ." (" .$transferType1 .")" : '';
            $pvCompanyData['key_transfered'] = ($transferType == POCTransfer::TRANSFER_TYPE_KEY) ? Admin::getUserNameBasedOnId($pvCompany->used_key_owner) : '';
            $pvCompanyData['any_req_in_past_days'] = $this->anyReqInLastNDays($pvCompany);
            $pvCompanyData['last_post'] = $this->getLastPostDate($pvCompany);
            $pvCompanyData['no_of_posting'] = $this->getNoOfPostings($pvCompany);
            $pvCompanyData['vendor_company'] = $pvCompany->name;
            $pvCompanyData['vendor_name'] = $pvCompany->poc_name;
            $pvCompanyData['vendor_email'] = $pvCompany->email;
            $pvCompanyData['vendor_phone'] = $pvCompany->phone;
            $pvCompanyData['transfer_poc'] = '';

            $allPvCompanyData[$id] = $pvCompanyData;
          }

        return $allPvCompanyData;
    }

    public function anyReqInLastNDays($pvCompany)
    {
        if(empty($pvCompany)){
            return '';
        }
        $startDate = \Carbon\Carbon::now()->subDays($this->getDaysConfiguration());
        $totalReqirement =  Requirement::where('user_id', $pvCompany->assigned_user_id)
            ->where(function ($query) {
                $query->where('id' ,'=', \DB::raw('parent_requirement_id'));
                $query->orwhere('parent_requirement_id', '=', '0');
            })
            ->where('poc_name', $pvCompany->poc_name)
            ->where('pv_company_name', $pvCompany->name)
            ->where('poc_email', $pvCompany->email)
            ->where('created_at' , '>=', $startDate)
            ->count();

        if($totalReqirement){
            return "Yes";
        }
        return "No";
    }

    public function getLastPostDate($pvCompany)
    {
        if(empty($pvCompany)){
            return '';
        }

        $requirement =  Requirement::select('created_at')
            ->where(function ($query) {
                $query->where('id' ,'=', \DB::raw('parent_requirement_id'));
                $query->orwhere('parent_requirement_id', '=', '0');
            })
            ->where('user_id', $pvCompany->assigned_user_id)
            ->where('poc_name', $pvCompany->poc_name)
            ->where('pv_company_name', $pvCompany->name)
            ->where('poc_email', $pvCompany->email)
            ->first();

        if(!empty($requirement)){
            return $this->getFormattedDate($requirement->created_at);
        }

        return '';

    }

    public function getNoOfPostings($pvCompany)
    {
        if(empty($pvCompany)){
            return '';
        }

        return Requirement::where(function ($query) {
                $query->where('id' ,'=', \DB::raw('parent_requirement_id'));
                $query->orwhere('parent_requirement_id', '=', '0');
            })
            ->where('user_id', $pvCompany->assigned_user_id)
            ->where('poc_name', $pvCompany->poc_name)
            ->where('pv_company_name', $pvCompany->name)
            ->where('poc_email', $pvCompany->email)
            ->count();
    }

    public function getFormattedDate($date)
    {
        if(!$date){
            return $date;
        }

        return date('m-d-y', strtotime($date));

    }
}
