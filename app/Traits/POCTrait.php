<?php
namespace App\Traits;
use App\Models\Interview;
use App\Models\PVCompany;
use App\Models\Requirement;
use App\Models\Submission;

trait POCTrait {
    use CommonTrait;

    protected $_pocWiseWhoAddedUserName = [];
    protected $_pocWiseRegisteredToUserName = [];
    public function getPOCHeadingData(): array
    {
        return [
            'who_added'                         => 'Who Added',
            'reg_to'                            => 'Reg. To',
            'bdm_wise_count'                    => 'BDM (Count)',
            'vendor_company_name'               => 'Vendor Company',
            'vendor_company_total_req'          => 'Vendor Company Total Req.',
            'poc_name'                          => 'POC Name',
            'poc_email'                         => 'POC Email',
            'poc_phone'                         => 'POC Phone',
            'added_date'                        => 'Date Added',
            'last_req_date'                     => 'Last Req.',
            'original_req_count'                => 'Total Req. #',
            'unique_req_count'                  => 'Org Req. #',
            'submission_count'                  => 'Sub #',
            'status_accepted'                   => 'Accpt',
            'status_rejected'                   => 'Rejected',
            'status_pending'                    => 'Pending',
            'status_unviewed'                   => 'Unviewed',
            'status_vendor_no_response'         => 'Vendor No Res',
            'status_vendor_rejected'            => 'Vendor Rejected',
            'status_client_rejected'            => 'Client Rejected',
            'status_submitted_to_end_client'    => 'Sub To End Client',
            'status_position_closed'            => 'Position Closed',
            'status_scheduled'                  => 'Scheduled',
            'status_re_scheduled'               => 'Re Scheduled',
            'status_selected_for_another_round' => 'Another Round',
            'status_waiting_feedback'           => 'Waiting Feedback',
            'status_position_confirm'           => 'Confirmed',
            'status_rejected_by_client'         => 'Rejected By Client',
            'status_backout'                    => 'Backout',
            'client_status_total'               => 'Total',
            'category_wise_count'               => 'Category (Count)',
        ];
    }

    public function getPocWiseData($pvCompany, $pvCompanies, $selectedPocNames, $request): array
    {
        $submissionModel = new Submission();
        $interviewModel  = new Interview();
        $date            = $this->getDate('time_frame', $request);

        if(!$pvCompany){
            return [];
        }

        $collection = PVCompany::where('name', $pvCompany);

        if(!empty($request->vendor_email)){
            $collection->where('email', $request->vendor_email);
        }

        if(!empty($request->vendor_phone)){
            $collection->where('phone', $request->vendor_phone);
        }

        if(!empty($request->who_added)){
            $collection->whereIn('user_id', $request->who_added);
        }

        if(!empty($request->registered_to)){
            $collection->whereIn('assigned_user_id', $request->registered_to);
        }

        $companyWiseAllPocNames = $collection->distinct()->pluck('poc_name')->toArray();

        if(!empty($request->bdm_names)){
            $collection = Requirement::whereIn('user_id', $request->bdm_names)->where('pv_company_name', $pvCompany);
            if($date && isset($date['from']) && $date['to']){
                $collection->whereBetween('created_at', $date);
            }
            $bdmWiseNames = $collection->distinct()->pluck('poc_name')->toArray();
            $companyWiseAllPocNames = array_intersect($bdmWiseNames, $companyWiseAllPocNames);
        }
        $pocNames = array_intersect($selectedPocNames, $companyWiseAllPocNames);

        if(!$pocNames || !count($pocNames)){
            return [];
        }

        $pocNameWiseData = [];

        foreach ($pocNames as $pocName) {
            $this->setIsEmptyPOCRow(1);
            $totalUniqueRequirement = $this->getPVCompanyWisePocRequirementCounts($pvCompany, $pocName, $pocNames, $date, 1);

            $pocData = [
                'who_added'                         => $this->getWhoAddedName($pvCompany, $pocName, $pocNames),
                'reg_to'                            => $this->getRegisteredToName($pvCompany, $pocName, $pocNames),
                'bdm_wise_count'                    => $this->getPVCompanyWisePocBDM($pvCompany, $pocName, $pocNames, $date),
                'vendor_company_name'               => $pvCompany,
                'vendor_company_total_req'          => $this->getRequirementCounts($pvCompany, $pvCompanies, $date),
                'poc_name'                          => $pocName,
                'poc_email'                         => $this->getPVCompanyWisePocEmail($pvCompany, $pocName, $pocNames, $date),
                'poc_phone'                         => $this->getPVCompanyWisePocPhone($pvCompany, $pocName, $pocNames, $date),
                'added_date'                        => $this->getPVCompanyWisePocAddedDate($pvCompany, $pocName, $pocNames, $date),
                'last_req_date'                     => $this->getPVCompanyWisePocLastRequestDate($pvCompany, $pocName, $pocNames, $date),
                'original_req_count'                => $this->getPVCompanyWisePocRequirementCounts($pvCompany, $pocName, $pocNames, $date),
                'unique_req_count'                  => $totalUniqueRequirement,
                'submission_count'                  => $this->getPVCompanyWisePocTotalSubmissionCounts($pvCompany, $pocName, $pocNames, $date),
                'status_accepted'                   => $this->getPVCompanyWisePocStatusCount($pvCompany, 'status',$submissionModel::STATUS_ACCEPT , $pocName, $pocNames, $date, $request->frame_type),
                'status_rejected'                   => $this->getPVCompanyWisePocStatusCount($pvCompany, 'status',$submissionModel::STATUS_REJECTED , $pocName, $pocNames, $date, $request->frame_type),
                'status_pending'                    => $this->getPVCompanyWisePocStatusCount($pvCompany, 'status',$submissionModel::STATUS_PENDING , $pocName, $pocNames, $date, $request->frame_type),
                'status_unviewed'                   => $this->getPVCompanyWisePocStatusCount($pvCompany, 'status',$submissionModel::STATUS_NOT_VIEWED , $pocName, $pocNames, $date, $request->frame_type),
                'status_vendor_no_response'         => $this->getPVCompanyWisePocStatusCount($pvCompany, 'pv_status',$submissionModel::STATUS_NO_RESPONSE_FROM_PV , $pocName, $pocNames, $date, $request->frame_type),
                'status_vendor_rejected_by_pv'      => $this->getPVCompanyWisePocStatusCount($pvCompany, 'pv_status',$submissionModel::STATUS_REJECTED_BY_PV , $pocName, $pocNames, $date, $request->frame_type),
                'status_rejected_by_client'         => $this->getPVCompanyWisePocStatusCount($pvCompany, 'pv_status',$submissionModel::STATUS_REJECTED_BY_END_CLIENT , $pocName, $pocNames, $date, $request->frame_type),
                'status_submitted_to_end_client'    => $this->getPVCompanyWisePocStatusCount($pvCompany, 'pv_status',$submissionModel::STATUS_SUBMITTED_TO_END_CLIENT , $pocName, $pocNames, $date, $request->frame_type),
                'status_position_closed'            => $this->getPVCompanyWisePocStatusCount($pvCompany, 'pv_status',$submissionModel::STATUS_POSITION_CLOSED , $pocName, $pocNames, $date, $request->frame_type),
                'status_scheduled'                  => $this->getPVCompanyWisePocClientStatusCount($pvCompany,$interviewModel::STATUS_SCHEDULED, $pocName, $pocNames, $date, $request->frame_type),
                'status_re_scheduled'               => $this->getPVCompanyWisePocClientStatusCount($pvCompany,$interviewModel::STATUS_RE_SCHEDULED, $pocName, $pocNames, $date, $request->frame_type),
                'status_selected_for_another_round' => $this->getPVCompanyWisePocClientStatusCount($pvCompany,$interviewModel::STATUS_SELECTED_FOR_NEXT_ROUND, $pocName, $pocNames, $date, $request->frame_type),
                'status_waiting_feedback'           => $this->getPVCompanyWisePocClientStatusCount($pvCompany,$interviewModel::STATUS_WAITING_FEEDBACK, $pocName, $pocNames, $date, $request->frame_type),
                'status_position_confirm'           => $this->getPVCompanyWisePocClientStatusCount($pvCompany,$interviewModel::STATUS_CONFIRMED_POSITION, $pocName, $pocNames, $date, $request->frame_type),
                'status_client_rejected'            => $this->getPVCompanyWisePocClientStatusCount($pvCompany,$interviewModel::STATUS_REJECTED, $pocName, $pocNames, $date, $request->frame_type),
                'status_backout'                    => $this->getPVCompanyWisePocClientStatusCount($pvCompany,$interviewModel::STATUS_BACKOUT, $pocName, $pocNames, $date, $request->frame_type),
                'client_status_total'               => $this->getPVCompanyWisePocClientStatusCount($pvCompany,'all', $pocName, $pocNames, $date, $request->frame_type),
                'category_wise_count'               => $this->getPVCompanyWisePocCategories($pvCompany, $pocName, $pocNames, $date),
            ];

            if($this->getIsEmptyPOCRow()){
                $pvCompanyKey = $this->getKey($pvCompany);
                $this->setEmptyPOCRows($pvCompanyKey.'_'.$pocName);
            }

            $pocNameWiseData['data'][$pocName] = $pocData;
            $pocNameWiseData['poc_org_req_count'][$pocName] = $this->getPocWiseOrgReqCount($pvCompany, $pocName);
            $pocNameWiseData['is_new_poc'][$pocName]        = $this->isNewAsPerConfiguration('poc_name', $pocName);
        }
        return $pocNameWiseData;
    }

    public function getWhoAddedName($pvCompany, $pocName, $pocNames)
    {
        $pvCompanyKey = $this->getKey($pvCompany);
        if(!$this->_pocWiseWhoAddedUserName || !isset($this->_pocWiseWhoAddedUserName[$pvCompanyKey])){
            $this->_pocWiseWhoAddedUserName[$pvCompanyKey] = PVCompany::select('admins.name as user_name', 'poc_name')
                ->leftJoin('admins', 'admins.id', '=', 'p_v_companies.user_id')
                ->whereIn('poc_name', $pocNames)
                ->where('p_v_companies.name', $pvCompany)
                ->groupBy('poc_name')->pluck('user_name', 'poc_name');
        }

        if(isset($this->_pocWiseWhoAddedUserName[$pvCompanyKey][$pocName])){
            return $this->_pocWiseWhoAddedUserName[$pvCompanyKey][$pocName];
        }

        return 0;
    }

    public function getRegisteredToName($pvCompany, $pocName, $pocNames)
    {
        $pvCompanyKey = $this->getKey($pvCompany);
        if(!$this->_pocWiseRegisteredToUserName || !isset($this->_pocWiseRegisteredToUserName[$pvCompanyKey])){
            $this->_pocWiseRegisteredToUserName[$pvCompanyKey] = PVCompany::select('admins.name as user_name', 'poc_name')
                ->leftJoin('admins', 'admins.id', '=', 'p_v_companies.assigned_user_id')
                ->whereIn('poc_name', $pocNames)
                ->where('p_v_companies.name', $pvCompany)
                ->groupBy('poc_name')->pluck('user_name', 'poc_name');
        }

        if(isset($this->_pocWiseRegisteredToUserName[$pvCompanyKey][$pocName])){
            return $this->_pocWiseRegisteredToUserName[$pvCompanyKey][$pocName];
        }

        return 0;
    }

    public function getPocHideColumns(): array
    {
        return [
            'poc_email',
            'poc_phone'
        ];
    }

    public function getTotalShowCompanyColumns(): array
    {
        return [
            'who_added',
            'reg_to',
            'bdm_wise_count',
            'vendor_company_name',
            'vendor_company_total_req',
            'poc_name'
        ];
    }

    public function getPVCompanyWiseOrgReqCount()
    {
        return Requirement::select('pv_company_name', \DB::raw("COUNT(DISTINCT id) as count"))
            ->where(function ($query) {
                $query->where('id' ,\DB::raw('parent_requirement_id'));
                $query->orwhere('parent_requirement_id', '=', '0');
            })
            ->groupBy('pv_company_name')
            ->pluck('count', 'pv_company_name')->toArray();
    }

    public function getPocWiseOrgReqCount($pvCompanyName, $pocName)
    {
        return  Requirement::where(function ($query) {
                $query->where('id' ,\DB::raw('parent_requirement_id'));
                $query->orwhere('parent_requirement_id', '=', '0');
            })
            ->where('pv_company_name', $pvCompanyName)
            ->where('poc_name', $pocName)
            ->count();
    }
}
