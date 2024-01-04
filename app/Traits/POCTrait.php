<?php
namespace App\Traits;
use App\Models\Interview;
use App\Models\PVCompany;
use App\Models\Requirement;
use App\Models\Submission;

trait POCTrait {
    use CommonTrait;

    public function getPOCHeadingData(): array
    {
        return [
            'who_added'                         => 'Who Added',
            'reg_to'                            => 'Reg. To',
            'vendor_company_name'               => 'Vendor Company',
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
            'bdm_wise_count'                    => 'BDM (Count)',
        ];
    }

    public function getPocWiseData($pvCompany, $selectedPocNames, $request): array
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

        $companyWiseAllPocNames = $collection->distinct()->pluck('poc_name')->toArray();

        if(!empty($request->bdm_names)){
            $bdmWiseNames = Requirement::whereIn('user_id', $request->bdm_names)->where('pv_company_name', $pvCompany)->distinct()->pluck('poc_name')->toArray();
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
                'who_added'                         => '-',
                'reg_to'                            => '-',
                'vendor_company_name'               => $pvCompany,
                'poc_name'                          => $pocName,
                'poc_email'                         => $this->getPVCompanyWisePocEmail($pvCompany, $pocName, $pocNames, $date),
                'poc_phone'                         => $this->getPVCompanyWisePocPhone($pvCompany, $pocName, $pocNames, $date),
                'added_date'                        => $this->getPVCompanyWisePocAddedDate($pvCompany, $pocName, $pocNames, $date),
                'last_req_date'                     => $this->getPVCompanyWisePocLastRequestDate($pvCompany, $pocName, $pocNames, $date),
                'original_req_count'                => $this->getPVCompanyWisePocRequirementCounts($pvCompany, $pocName, $pocNames, $date),
                'unique_req_count'                  => $totalUniqueRequirement,
                'submission_count'                  => $this->getPVCompanyWisePocTotalSubmissionCounts($pvCompany, $pocName, $pocNames, $date),
                'status_accepted'                   => $this->getPVCompanyWisePocStatusCount($pvCompany, 'status',$submissionModel::STATUS_ACCEPT , $pocName, $pocNames, $date),
                'status_rejected'                   => $this->getPVCompanyWisePocStatusCount($pvCompany, 'status',$submissionModel::STATUS_REJECTED , $pocName, $pocNames, $date),
                'status_pending'                    => $this->getPVCompanyWisePocStatusCount($pvCompany, 'status',$submissionModel::STATUS_PENDING , $pocName, $pocNames, $date),
                'status_unviewed'                   => $this->getPVCompanyWisePocStatusCount($pvCompany, 'status',$submissionModel::STATUS_NOT_VIEWED , $pocName, $pocNames, $date),
                'status_vendor_no_response'         => $this->getPVCompanyWisePocStatusCount($pvCompany, 'pv_status',$submissionModel::STATUS_NO_RESPONSE_FROM_PV , $pocName, $pocNames, $date),
                'status_vendor_rejected_by_pv'      => $this->getPVCompanyWisePocStatusCount($pvCompany, 'pv_status',$submissionModel::STATUS_REJECTED_BY_PV , $pocName, $pocNames, $date),
                'status_rejected_by_client'         => $this->getPVCompanyWisePocStatusCount($pvCompany, 'pv_status',$submissionModel::STATUS_REJECTED_BY_END_CLIENT , $pocName, $pocNames, $date),
                'status_submitted_to_end_client'    => $this->getPVCompanyWisePocStatusCount($pvCompany, 'pv_status',$submissionModel::STATUS_SUBMITTED_TO_END_CLIENT , $pocName, $pocNames, $date),
                'status_position_closed'            => $this->getPVCompanyWisePocStatusCount($pvCompany, 'pv_status',$submissionModel::STATUS_POSITION_CLOSED , $pocName, $pocNames, $date),
                'status_scheduled'                  => $this->getPVCompanyWisePocClientStatusCount($pvCompany,$interviewModel::STATUS_SCHEDULED, $pocName, $pocNames, $date),
                'status_re_scheduled'               => $this->getPVCompanyWisePocClientStatusCount($pvCompany,$interviewModel::STATUS_RE_SCHEDULED, $pocName, $pocNames, $date),
                'status_selected_for_another_round' => $this->getPVCompanyWisePocClientStatusCount($pvCompany,$interviewModel::STATUS_SELECTED_FOR_NEXT_ROUND, $pocName, $pocNames, $date),
                'status_waiting_feedback'           => $this->getPVCompanyWisePocClientStatusCount($pvCompany,$interviewModel::STATUS_WAITING_FEEDBACK, $pocName, $pocNames, $date),
                'status_position_confirm'           => $this->getPVCompanyWisePocClientStatusCount($pvCompany,$interviewModel::STATUS_CONFIRMED_POSITION, $pocName, $pocNames, $date),
                'status_client_rejected'            => $this->getPVCompanyWisePocClientStatusCount($pvCompany,$interviewModel::STATUS_REJECTED, $pocName, $pocNames, $date),
                'status_backout'                    => $this->getPVCompanyWisePocClientStatusCount($pvCompany,$interviewModel::STATUS_BACKOUT, $pocName, $pocNames, $date),
                'client_status_total'               => $this->getPVCompanyWisePocClientStatusCount($pvCompany,'all', $pocName, $pocNames, $date),
                'category_wise_count'               => $this->getPVCompanyWisePocCategories($pvCompany, $pocName, $pocNames, $date),
                'bdm_wise_count'                    => $this->getPVCompanyWisePocBDM($pvCompany, $pocName, $pocNames, $date),
            ];

            if($this->getIsEmptyPOCRow()){
                $this->setEmptyPOCRows($pocName);
            }

            $pocNameWiseData[$pocName] = $pocData;
        }
        return $pocNameWiseData;
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
            'vendor_company_name',
            'poc_name'
        ];
    }
}
