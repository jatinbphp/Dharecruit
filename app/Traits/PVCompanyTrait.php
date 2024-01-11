<?php
namespace App\Traits;
use App\Models\Interview;
use App\Models\PVCompany;
use App\Models\Requirement;
use App\Models\Submission;

trait PVCompanyTrait {
    use CommonTrait;
    protected $_companyIdWiseCompanyAddedDate = [];
    protected $_companyWiseCompanyLastReqDate = [];
    protected $_companyWiseRequirementCounts = [];
    protected $_companyWiseTotalSubmissionCounts = [];
    protected $_companyWiseTotalStatusCounts = [];
    protected $_companyWiseTotalClientStatusCounts = [];
    protected $_companyWiseHighestUniqueRequirementByPoc = [];
    protected $_companyWiseTotalPocCount = [];
    protected $_companyWiseTotalBDMCount = [];
    protected $_companyWiseCategories = [];
    protected $_companyWiseBDMs = [];
    protected $_isEmptyPVRow = 0;
    protected $_emptyPVRows = [];

    public function setIsEmptyPVRow($value)
    {
        $this->_isEmptyPVRow = $value;
        return $this;
    }

    public function getIsEmptyPVRow()
    {
        return $this->_isEmptyPVRow;
    }

    public function setEmptyPVRows($key)
    {
        $this->_emptyPVRows[$key] = $key;
        return $this;
    }

    public function getEmptyPVRows()
    {
        return $this->_emptyPVRows;
    }

    public function getPvHeadingData(): array
    {
        return [
            'company_name'                      => 'Name',
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
            'poc_count'                         => 'POC#',
            'avg'                               => 'Avg',
            'req_h'                             => 'PREQH',
            'bdm_count'                         => 'BDM #',
            'category_wise_count'               => 'Category (Count)',
            'bdm_wise_count'                    => 'BDM (Count)',
        ];
    }

    public function getCompanyWisePVCompanyData($pvCompany, $pvCompanies, $request): array
    {
        $submissionModel = new Submission();
        $interviewModel  = new Interview();
        $date            = $this->getDate('time_frame', $request);

        $totalUniqueRequirement = $this->getRequirementCounts($pvCompany, $pvCompanies, $date, 1);
        $totalPoc               = $this->getCompanyWiseTotalPocCount($pvCompany, $pvCompanies, $date);
        $avg                    = 0;

        if($totalUniqueRequirement && $totalPoc){
            $avg = round($totalUniqueRequirement / $totalPoc, 2);
        }

        $this->setIsEmptyPVRow(1);
        $pvCompanyData = [
            'company_name'                      => $pvCompany,
            'added_date'                        => $this->getCompanyAddedDateBasedOnId($pvCompany, $pvCompanies, $date),
            'last_req_date'                     => $this->getLastRequestDateBasedOnId($pvCompany, $pvCompanies, $date),
            'original_req_count'                => $this->getRequirementCounts($pvCompany, $pvCompanies, $date),
            'unique_req_count'                  => $totalUniqueRequirement,
            'submission_count'                  => $this->getTotalSubmissionCounts($pvCompany, $pvCompanies, $date),
            'status_accepted'                   => $this->getPVCompanyWiseStatusCount('status',$submissionModel::STATUS_ACCEPT , $pvCompany, $pvCompanies, $date),
            'status_rejected'                   => $this->getPVCompanyWiseStatusCount('status',$submissionModel::STATUS_REJECTED , $pvCompany, $pvCompanies, $date),
            'status_pending'                    => $this->getPVCompanyWiseStatusCount('status',$submissionModel::STATUS_PENDING , $pvCompany, $pvCompanies, $date),
            'status_unviewed'                   => $this->getPVCompanyWiseStatusCount('status',$submissionModel::STATUS_NOT_VIEWED , $pvCompany, $pvCompanies, $date),
            'status_vendor_no_response'         => $this->getPVCompanyWiseStatusCount('pv_status',$submissionModel::STATUS_NO_RESPONSE_FROM_PV , $pvCompany, $pvCompanies, $date),
            'status_vendor_rejected_by_pv'      => $this->getPVCompanyWiseStatusCount('pv_status',$submissionModel::STATUS_REJECTED_BY_PV , $pvCompany, $pvCompanies, $date),
            'status_rejected_by_client'         => $this->getPVCompanyWiseStatusCount('pv_status',$submissionModel::STATUS_REJECTED_BY_END_CLIENT , $pvCompany, $pvCompanies, $date),
            'status_submitted_to_end_client'    => $this->getPVCompanyWiseStatusCount('pv_status',$submissionModel::STATUS_SUBMITTED_TO_END_CLIENT , $pvCompany, $pvCompanies, $date),
            'status_position_closed'            => $this->getPVCompanyWiseStatusCount('pv_status',$submissionModel::STATUS_POSITION_CLOSED , $pvCompany, $pvCompanies, $date),
            'status_scheduled'                  => $this->getCompanyWiseTotalClientStatusCount($interviewModel::STATUS_SCHEDULED, $pvCompany, $pvCompanies, $date),
            'status_re_scheduled'               => $this->getCompanyWiseTotalClientStatusCount($interviewModel::STATUS_RE_SCHEDULED, $pvCompany, $pvCompanies, $date),
            'status_selected_for_another_round' => $this->getCompanyWiseTotalClientStatusCount($interviewModel::STATUS_SELECTED_FOR_NEXT_ROUND, $pvCompany, $pvCompanies, $date),
            'status_waiting_feedback'           => $this->getCompanyWiseTotalClientStatusCount($interviewModel::STATUS_WAITING_FEEDBACK, $pvCompany, $pvCompanies, $date),
            'status_position_confirm'           => $this->getCompanyWiseTotalClientStatusCount($interviewModel::STATUS_CONFIRMED_POSITION, $pvCompany, $pvCompanies, $date),
            'status_client_rejected'            => $this->getCompanyWiseTotalClientStatusCount($interviewModel::STATUS_REJECTED, $pvCompany, $pvCompanies, $date),
            'status_backout'                    => $this->getCompanyWiseTotalClientStatusCount($interviewModel::STATUS_BACKOUT, $pvCompany, $pvCompanies, $date),
            'client_status_total'               => $this->getCompanyWiseTotalClientStatusCount('all', $pvCompany, $pvCompanies, $date),
            'poc_count'                         => $totalPoc,
            'avg'                               => $avg,
            'highest_uni_req_by_poc'            => $this->getCompanyWiseHighestUniqueRequirementByPoc($pvCompany, $pvCompanies, $date),
            'bdm_count'                         => $this->getCompanyWiseTotalBDMCount($pvCompany, $pvCompanies, $date),
            'category_wise_count'               => $this->getCompanyWiseCategories($pvCompany, $pvCompanies, $date),
            'bdm_wise_count'                    => $this->getCompanyWiseBDM($pvCompany, $pvCompanies, $date),
        ];

        if($this->getIsEmptyPVRow()){
            $pvCompanyKey = $this->getKey($pvCompany);
            $this->setEmptyPVRows($pvCompanyKey);
        }

        return  $pvCompanyData;
    }

    public function getCompanyWisePocData($pvCompany, $request): array
    {
        $submissionModel = new Submission();
        $interviewModel  = new Interview();
        $date            = $this->getDate('time_frame', $request);

        if(!$pvCompany){
            return [];
        }

        $pocNames = PVCompany::where('name', $pvCompany)->whereNotNull('poc_name')->groupBy('poc_name')->pluck('poc_name')->toArray();
        if(!$pocNames || !count($pocNames)){
            return [];
        }
        $pocNameWiseData = [];

        foreach ($pocNames as $pocName) {
            $totalUniqueRequirement = $this->getPVCompanyWisePocRequirementCounts($pvCompany, $pocName, $pocNames, $date, 1);

            $this->setIsEmptyPOCRow(1);

            $pocData = [
                'company_name'                      => $pocName,
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
                'poc_count'                         => '-',
                'avg'                               => '-',
                'highest_uni_req_by_poc'            => '-',
                'bdm_count'                         => $this->getPVCompanyWisePocTotalBDMCount($pvCompany, $pocName, $pocNames, $date),
                'category_wise_count'               => $this->getPVCompanyWisePocCategories($pvCompany, $pocName, $pocNames, $date),
                'bdm_wise_count'                    => $this->getPVCompanyWisePocBDM($pvCompany, $pocName, $pocNames, $date),
            ];

            if($this->getIsEmptyPOCRow()){
                $pvCompanyKey = $this->getKey($pvCompany);
                $this->setEmptyPOCRows($pvCompanyKey.'_'.$pocName);
            }

            $pocNameWiseData[$pocName] = $pocData;
        }

        return $pocNameWiseData;
    }

    public function getCompanyAddedDateBasedOnId($pvCompanyName, $allPvCompanies, $date): string
    {
        if(!$this->_companyIdWiseCompanyAddedDate){
            $collection = PVCompany::select('name', \DB::raw("DATE_FORMAT(created_at, '%m-%d-%y') as formatted_date"))
                ->whereIn('name', $allPvCompanies);
                /*if($date && isset($date['from']) && $date['to']){
                    $collection->whereBetween('created_at', $date);
                }*/
            $this->_companyIdWiseCompanyAddedDate = $collection->pluck('formatted_date', 'name')->toArray();
        }

        if(isset($this->_companyIdWiseCompanyAddedDate[$pvCompanyName])){
            /*$this->setIsEmptyPVRow(0);*/
            return $this->_companyIdWiseCompanyAddedDate[$pvCompanyName];
        }

        return '';
    }

    public function getLastRequestDateBasedOnId($pvCompanyName, $allPVCompanies, $date): string
    {
        if(!$this->_companyWiseCompanyLastReqDate){
            $collection = Requirement::select(\DB::raw('LOWER(pv_company_name) as pv_company_name'), \DB::raw("DATE_FORMAT(MAX(created_at), '%m-%d-%y') as latest_created_at"))
                ->whereIn('pv_company_name', $allPVCompanies);
                /*if($date && isset($date['from']) && $date['to']){
                    $collection->whereBetween('created_at', $date);
                }*/
            $collection->groupBy('pv_company_name');
            $this->_companyWiseCompanyLastReqDate = $collection->pluck('latest_created_at', 'pv_company_name')->toArray();
        }

        $pvCompanyName = strtolower($pvCompanyName);
        if(isset($this->_companyWiseCompanyLastReqDate[$pvCompanyName])){
            /*$this->setIsEmptyPVRow(0);*/
            return $this->_companyWiseCompanyLastReqDate[$pvCompanyName];
        }

        return '';
    }

    public function getRequirementCounts($pvCompanyName, $allPVCompanies, $date, $isUnique = 0): int
    {
        if(!$this->_companyWiseRequirementCounts || !isset($this->_companyWiseRequirementCounts[$isUnique])){
            $collection = Requirement::select(\DB::raw('LOWER(pv_company_name) as pv_company_name'), \DB::raw("count(id) as count"))
            ->whereIn('pv_company_name', $allPVCompanies);
            if($isUnique){
                $collection->where(function ($query) {
                    $query->where('id' ,\DB::raw('parent_requirement_id'));
                    $query->orwhere('parent_requirement_id', '=', '0');
                });
            }
            if($date && isset($date['from']) && $date['to']){
                $collection->whereBetween('created_at', $date);
            }
            $collection->groupBy('pv_company_name');
            $this->_companyWiseRequirementCounts[$isUnique] = $collection->pluck('count', 'pv_company_name')->toArray();
        }

        $pvCompanyName = strtolower($pvCompanyName);
        if(isset($this->_companyWiseRequirementCounts[$isUnique][$pvCompanyName])){
            $this->setIsEmptyPVRow(0);
            return $this->_companyWiseRequirementCounts[$isUnique][$pvCompanyName];
        }

        return 0;
    }

    public function getTotalSubmissionCounts($pvCompanyName, $allPVCompanies, $date): int
    {
        if(!$this->_companyWiseTotalSubmissionCounts){
           $collection = Requirement::leftJoin('submissions', 'requirements.id', '=', 'submissions.requirement_id')
                ->whereIn('requirements.pv_company_name', $allPVCompanies);
                if($date && isset($date['from']) && $date['to']){
                    $collection->whereBetween('submissions.created_at', $date);
                }
                $collection->groupBy('requirements.pv_company_name')
                ->selectRaw('LOWER(requirements.pv_company_name) as pv_company_name, COUNT(submissions.id) as count');

            $this->_companyWiseTotalSubmissionCounts = $collection->pluck('count', 'pv_company_name')->toArray();
        }

        $pvCompanyName = strtolower($pvCompanyName);
        if(isset($this->_companyWiseTotalSubmissionCounts[$pvCompanyName])){
            $this->setIsEmptyPVRow(0);
            return $this->_companyWiseTotalSubmissionCounts[$pvCompanyName];
        }

        return 0;
    }

    public function getPVCompanyWiseStatusCount($filedName, $status, $pvCompanyName, $allPVCompanies, $date): int
    {
        if(!$this->_companyWiseTotalStatusCounts || !isset($this->_companyWiseTotalStatusCounts[$status])){
            $collection = $this->getJoin($status, $filedName, $date);
            $collection->whereIn('requirements.pv_company_name', $allPVCompanies)
                ->groupBy('requirements.pv_company_name')
                ->selectRaw('LOWER(requirements.pv_company_name) as pv_company_name, COUNT(submissions.id) as count');

            $this->_companyWiseTotalStatusCounts[$status] = $collection->pluck('count', 'pv_company_name')->toArray();
        }

        $pvCompanyName = strtolower($pvCompanyName);
        if(isset($this->_companyWiseTotalStatusCounts[$status][$pvCompanyName])){
            $this->setIsEmptyPVRow(0);
            return $this->_companyWiseTotalStatusCounts[$status][$pvCompanyName];
        }

        return 0;
    }

    public function getCompanyWiseTotalClientStatusCount($status, $pvCompanyName, $allPVCompanies, $date): int
    {
        if(!$this->_companyWiseTotalClientStatusCounts || !isset($this->_companyWiseTotalClientStatusCounts[$status])){
            $collection =  Requirement::leftJoin('submissions', 'requirements.id', '=', 'submissions.requirement_id')
                ->leftJoin('interviews', 'submissions.id', '=', 'interviews.submission_id');
                if($status != 'all'){
                    $collection->where('interviews.status', $status);

                }
                if($date && isset($date['from']) && $date['to']){
                    $collection->whereBetween('interviews.updated_at', $date);
                }
            $collection->whereIn('requirements.pv_company_name', $allPVCompanies)
                ->groupBy('requirements.pv_company_name')
                ->selectRaw('LOWER(requirements.pv_company_name) as pv_company_name, COUNT(interviews.id) as count');

            $this->_companyWiseTotalClientStatusCounts[$status] = $collection->pluck('count', 'pv_company_name')->toArray();
        }

        $pvCompanyName = strtolower($pvCompanyName);
        if (isset($this->_companyWiseTotalClientStatusCounts[$status][$pvCompanyName])) {
            $this->setIsEmptyPVRow(0);
            return $this->_companyWiseTotalClientStatusCounts[$status][$pvCompanyName];
        }

        return 0;
    }

    public function getCompanyWiseTotalPocCount($pvCompanyName, $allPVCompanies, $date): int
    {
        if(!$this->_companyWiseTotalPocCount){
            $collection = Requirement::select(\DB::raw('LOWER(requirements.pv_company_name) as pv_company_name'), \DB::raw("COUNT(DISTINCT poc_name) as count"))
                ->whereIn('pv_company_name', $allPVCompanies);
            if($date && isset($date['from']) && $date['to']){
                $collection->whereBetween('created_at', $date);
            }
            $collection->groupBy('pv_company_name');

            $this->_companyWiseTotalPocCount = $collection->pluck('count', 'pv_company_name')->toArray();
        }

        $pvCompanyName = strtolower($pvCompanyName);
        if (isset($this->_companyWiseTotalPocCount[$pvCompanyName])) {
            $this->setIsEmptyPVRow(0);
            return $this->_companyWiseTotalPocCount[$pvCompanyName];
        }

        return 0;
    }

    public function getCompanyWiseHighestUniqueRequirementByPoc($pvCompanyName, $allPVCompanies, $date): int
    {
        if(!$this->_companyWiseHighestUniqueRequirementByPoc){
            $collection = Requirement::select(\DB::raw('LOWER(requirements.pv_company_name) as pv_company_name'), 'poc_name', \DB::raw('COUNT(*) as poc_count'))
                ->whereIn('pv_company_name', $allPVCompanies)
                ->where(function ($query) {
                        $query->where('id' ,\DB::raw('parent_requirement_id'));
                        $query->orwhere('parent_requirement_id', '=', '0');
                });
                if($date && isset($date['from']) && $date['to']){
                    $collection->whereBetween('created_at', $date);
                }
                $collection->groupBy(['pv_company_name', 'poc_name']);

            $this->_companyWiseHighestUniqueRequirementByPoc = $collection->get()
                ->groupBy('pv_company_name')
                ->map(function ($group) {
                    return $group->max('poc_count');
                })
                ->toArray();
        }

        $pvCompanyName = strtolower($pvCompanyName);
        if (isset($this->_companyWiseHighestUniqueRequirementByPoc[$pvCompanyName])) {
            $this->setIsEmptyPVRow(0);
            return $this->_companyWiseHighestUniqueRequirementByPoc[$pvCompanyName];
        }

        return 0;
    }

    public function getCompanyWiseTotalBDMCount($pvCompanyName, $allPVCompanies, $date): int
    {
        if(!$this->_companyWiseTotalBDMCount){
            $collection = Requirement::select(\DB::raw('LOWER(requirements.pv_company_name) as pv_company_name'), \DB::raw("COUNT(DISTINCT user_id) as count"))
                ->whereIn('pv_company_name', $allPVCompanies);
            if($date && isset($date['from']) && $date['to']){
                $collection->whereBetween('created_at', $date);
            }
            $collection->groupBy('pv_company_name');

            $this->_companyWiseTotalBDMCount = $collection->pluck('count', 'pv_company_name')->toArray();
        }

        $pvCompanyName = strtolower($pvCompanyName);
        if (isset($this->_companyWiseTotalBDMCount[$pvCompanyName])) {
            $this->setIsEmptyPVRow(0);
            return $this->_companyWiseTotalBDMCount[$pvCompanyName];
        }

        return 0;
    }

    public function getCompanyWiseCategories($pvCompanyName, $allPVCompanies, $date): array
    {
        if(!$this->_companyWiseCategories){
            $collection = Requirement::select(
                    \DB::raw('LOWER(requirements.pv_company_name) as pv_company_name'),
                    \DB::raw('GROUP_CONCAT(categories.name) as category_names'),
                )
                ->whereIn('pv_company_name', $allPVCompanies)
                ->leftJoin('categories', 'requirements.category', '=', 'categories.id');
                if($date && isset($date['from']) && $date['to']){
                    $collection->whereBetween('requirements.created_at', $date);
                }

            $pvCompanyData = $collection->groupBy('requirements.pv_company_name')->get();

            if($pvCompanyData){
                foreach ($pvCompanyData as $data){
                    $pvCompanyNameData = $data->pv_company_name;
                    if(!$pvCompanyNameData){
                        continue;
                    }
                    $categoryNameArray = explode(',', $data->category_names);
                    $categoryCount = array_count_values($categoryNameArray);

                    $categoryString = [];

                    foreach (array_unique($categoryNameArray) as $category){
                        $categoryString[] = $category .' ('. (isset($categoryCount[$category]) ? $categoryCount[$category] : 0) . ')';
                    }

                    $this->_companyWiseCategories[$pvCompanyNameData] = $categoryString;
                }
            }
        }

        $pvCompanyName = strtolower($pvCompanyName);
        if (isset($this->_companyWiseCategories[$pvCompanyName])) {
            $this->setIsEmptyPVRow(0);
            return $this->_companyWiseCategories[$pvCompanyName];
        }

        return [];
    }

    public function getCompanyWiseBDM($pvCompanyName, $allPVCompanies, $date): array
    {
        if(!$this->_companyWiseBDMs){
            $collection = Requirement::select(
                    \DB::raw('LOWER(requirements.pv_company_name) as pv_company_name'),
                    \DB::raw('GROUP_CONCAT(admins.name) as admin'),
                )
                ->whereIn('pv_company_name', $allPVCompanies);
                if($date && isset($date['from']) && $date['to']){
                    $collection->whereBetween('requirements.created_at', $date);
                }
            $collection->leftJoin('admins', 'requirements.user_id', '=', 'admins.id');

            $pvCompanyData = $collection->groupBy('requirements.pv_company_name')->get();

            if($pvCompanyData){
                foreach ($pvCompanyData as $data){
                    $pvCompanyNameData = $data->pv_company_name;
                    if(!$pvCompanyNameData){
                        continue;
                    }
                    $bdmNameArray = explode(',', $data->admin);
                    $bdmCount = array_count_values($bdmNameArray);

                    $bdmNameString = [];

                    foreach (array_unique($bdmNameArray) as $bdmName){
                        $bdmNameString[] = $bdmName .' ('. (isset($bdmCount[$bdmName]) ? $bdmCount[$bdmName] : 0) . ')';
                    }

                    $this->_companyWiseBDMs[$pvCompanyNameData] = $bdmNameString;
                }
            }
        }

       $pvCompanyName = strtolower($pvCompanyName);
       if (isset($this->_companyWiseBDMs[$pvCompanyName])) {
           $this->setIsEmptyPVRow(0);
           return $this->_companyWiseBDMs[$pvCompanyName];
       }

        return [];
    }
}
