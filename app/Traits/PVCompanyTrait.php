<?php
namespace App\Traits;
use App\Models\Interview;
use App\Models\PVCompany;
use App\Models\Requirement;
use App\Models\Submission;

trait PVCompanyTrait {
    use CommonTrait;

    protected $_companyIdWiseCompanyName = [];
    protected $_companyIdWiseCompanyAddedDate = [];
    protected $_companyIdWisePvCompanyName = [];
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
    protected $_pvCompanyWisePocRequirementsCounts = [];
    protected $_pvCompanyWisePocAddedDate = [];
    protected $_pvCompanyWisePocLastReqDate = [];
    protected $_pvCompanyWisePocTotalSubmissionCounts = [];
    protected $_pvCompanyWisePocTotalStatusCounts = [];
    protected $_pocWiseTotalClientStatusCounts = [];
    protected $_pvCompanyWisepocTotalBDMCount = [];
    protected $_pvCompanyWisePocCategories = [];
    protected $_pvCompanyWisePocBDMs = [];


    public function getPvHeadingData(): array
    {
        return [
            'company_name'                      => 'Name',
            'added_date'                        => 'Date Added',
            'last_req_date'                     => 'Last Req.',
            'original_req_count'                => 'Org Req. #',
            'unique_req_count'                  => 'Uni Req. #',
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

    public function getCompanyWisePVCompanyData($companyId, $allCompanyIds, $request): array
    {
        $this->prepareCompanyIdWisePvCompanyData($allCompanyIds);
        $submissionModel = new Submission();
        $interviewModel  = new Interview();
        $date            = $this->getDate('time_frame', $request);

        $totalUniqueRequirement = $this->getRequirementCounts($companyId, $allCompanyIds, $date, 1);
        $totalPoc               = $this->getCompanyWiseTotalPocCount($companyId, $allCompanyIds, $date);
        $avg                    = 0;

        if($totalUniqueRequirement && $totalPoc){
            $avg = round($totalUniqueRequirement / $totalPoc, 2);
        }

        return [
            'company_name'                      => $this->getPvCompanyNameBasedOnId($companyId, $allCompanyIds),
            'added_date'                        => $this->getCompanyAddedDateBasedOnId($companyId, $allCompanyIds, $date),
            'last_req_date'                     => $this->getLastRequestDateBasecOnId($companyId, $allCompanyIds, $date),
            'original_req_count'                => $this->getRequirementCounts($companyId, $allCompanyIds, $date),
            'unique_req_count'                  => $totalUniqueRequirement,
            'submission_count'                  => $this->getTotalSubmissionCounts($companyId, $allCompanyIds, $date),
            'status_accepted'                   => $this->getPVCompanyWiseStatusCount('status',$submissionModel::STATUS_ACCEPT , $companyId, $allCompanyIds, $date),
            'status_rejected'                   => $this->getPVCompanyWiseStatusCount('status',$submissionModel::STATUS_REJECTED , $companyId, $allCompanyIds, $date),
            'status_pending'                    => $this->getPVCompanyWiseStatusCount('status',$submissionModel::STATUS_PENDING , $companyId, $allCompanyIds, $date),
            'status_unviewed'                   => $this->getPVCompanyWiseStatusCount('status',$submissionModel::STATUS_NOT_VIEWED , $companyId, $allCompanyIds, $date),
            'status_vendor_no_response'         => $this->getPVCompanyWiseStatusCount('pv_status',$submissionModel::STATUS_NO_RESPONSE_FROM_PV , $companyId, $allCompanyIds, $date),
            'status_vendor_rejected_by_pv'      => $this->getPVCompanyWiseStatusCount('pv_status',$submissionModel::STATUS_REJECTED_BY_PV , $companyId, $allCompanyIds, $date),
            'status_rejected_by_client'         => $this->getPVCompanyWiseStatusCount('pv_status',$submissionModel::STATUS_REJECTED_BY_END_CLIENT , $companyId, $allCompanyIds, $date),
            'status_submitted_to_end_client'    => $this->getPVCompanyWiseStatusCount('pv_status',$submissionModel::STATUS_SUBMITTED_TO_END_CLIENT , $companyId, $allCompanyIds, $date),
            'status_position_closed'            => $this->getPVCompanyWiseStatusCount('pv_status',$submissionModel::STATUS_POSITION_CLOSED , $companyId, $allCompanyIds, $date),
            'status_scheduled'                  => $this->getCompanyWiseTotalClientStatusCount($interviewModel::STATUS_SCHEDULED, $companyId, $allCompanyIds, $date),
            'status_re_scheduled'               => $this->getCompanyWiseTotalClientStatusCount($interviewModel::STATUS_RE_SCHEDULED, $companyId, $allCompanyIds, $date),
            'status_selected_for_another_round' => $this->getCompanyWiseTotalClientStatusCount($interviewModel::STATUS_SELECTED_FOR_NEXT_ROUND, $companyId, $allCompanyIds, $date),
            'status_waiting_feedback'           => $this->getCompanyWiseTotalClientStatusCount($interviewModel::STATUS_WAITING_FEEDBACK, $companyId, $allCompanyIds, $date),
            'status_position_confirm'           => $this->getCompanyWiseTotalClientStatusCount($interviewModel::STATUS_CONFIRMED_POSITION, $companyId, $allCompanyIds, $date),
            'status_client_rejected'            => $this->getCompanyWiseTotalClientStatusCount($interviewModel::STATUS_REJECTED, $companyId, $allCompanyIds, $date),
            'status_backout'                    => $this->getCompanyWiseTotalClientStatusCount($interviewModel::STATUS_BACKOUT, $companyId, $allCompanyIds, $date),
            'client_status_total'               => $this->getCompanyWiseTotalClientStatusCount('all', $companyId, $allCompanyIds, $date),
            'poc_count'                         => $totalPoc,
            'avg'                               => $avg,
            'highest_uni_req_by_poc'            => $this->getCompanyWiseHighestUniqueRequirementByPoc($companyId, $allCompanyIds, $date),
            'bdm_count'                         => $this->getCompanyWiseTotalBDMCount($companyId, $allCompanyIds, $date),
            'category_wise_count'               => $this->getCompanyWiseCategories($companyId, $allCompanyIds, $date),
            'bdm_wise_count'                    => $this->getCompanyWiseBDM($companyId, $allCompanyIds, $date),
        ];
    }

    public function getCompanyWisePocData($companyId, $allCompanyIds, $request): array
    {
        $submissionModel = new Submission();
        $interviewModel  = new Interview();
        $date            = $this->getDate('time_frame', $request);

        $pvCompany = isset($this->_companyIdWisePvCompanyName[$companyId]) ? $this->_companyIdWiseCompanyName[$companyId] : '';

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

            $pocNameWiseData[$pocName] = [
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
        }
        return $pocNameWiseData;
    }

    public function getPvCompanyNameBasedOnId($companyId, $allCompanyIds): string
    {
        if(!$this->_companyIdWiseCompanyName){
            $this->_companyIdWiseCompanyName = PVCompany::whereIn('id', $allCompanyIds)
                ->pluck('name', 'id')
                ->toArray();
        }

        if(isset($this->_companyIdWiseCompanyName[$companyId])){
            return $this->_companyIdWiseCompanyName[$companyId];
        }

        return '';
    }

    public function getCompanyAddedDateBasedOnId($companyId, $allCompanyIds, $date): string
    {
        if(!$this->_companyIdWiseCompanyAddedDate){
            $collection = PVCompany::select('id', \DB::raw("DATE_FORMAT(created_at, '%m-%d-%y') as formatted_date"))
                ->whereIn('id', $allCompanyIds);
                if($date && isset($date['from']) && $date['to']){
                    $collection->whereBetween('created_at', $date);
                }
            $this->_companyIdWiseCompanyAddedDate = $collection->pluck('formatted_date', 'id')->toArray();
        }

        if(isset($this->_companyIdWiseCompanyAddedDate[$companyId])){
            return $this->_companyIdWiseCompanyAddedDate[$companyId];
        }

        return '';
    }

    public function prepareCompanyIdWisePvCompanyData($allCompanyIds): Object
    {
        $this->_companyIdWisePvCompanyName = PVCompany::whereIn('id', $allCompanyIds)
            ->pluck('name', 'id')
            ->toArray();

        return $this;
    }

    public function getLastRequestDateBasecOnId($companyId, $allCompanyIds, $date): string
    {
        if(!$this->_companyWiseCompanyLastReqDate){
            if(!$this->_companyIdWisePvCompanyName){
                $this->prepareCompanyIdWisePvCompanyData($allCompanyIds);
            }

            $collection = Requirement::select('pv_company_name', \DB::raw("DATE_FORMAT(MAX(created_at), '%m-%d-%y') as latest_created_at"))
                ->whereIn('pv_company_name', $this->_companyIdWisePvCompanyName);
                if($date && isset($date['from']) && $date['to']){
                    $collection->whereBetween('created_at', $date);
                }
            $collection->groupBy('pv_company_name');
            $this->_companyWiseCompanyLastReqDate = $collection->pluck('latest_created_at', 'pv_company_name')->toArray();
        }

        if($this->_companyIdWisePvCompanyName && isset($this->_companyIdWisePvCompanyName[$companyId])){
            $pvCompanyName = $this->_companyIdWisePvCompanyName[$companyId];
            if(isset($this->_companyWiseCompanyLastReqDate[$pvCompanyName])){
                return $this->_companyWiseCompanyLastReqDate[$pvCompanyName];
            }
        }

        return '';
    }

    public function getRequirementCounts($companyId, $allCompanyIds, $date, $isUnique = 0): int
    {
        if(!$this->_companyWiseRequirementCounts || !isset($this->_companyWiseRequirementCounts[$isUnique])){
            if(!$this->_companyIdWisePvCompanyName){
                $this->prepareCompanyIdWisePvCompanyData($allCompanyIds);
            }
            $collection = Requirement::select('pv_company_name', \DB::raw("count(id) as count"))
            ->whereIn('pv_company_name', $this->_companyIdWisePvCompanyName);
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

        if($this->_companyIdWisePvCompanyName && isset($this->_companyIdWisePvCompanyName[$companyId])){
            $pvCompanyName = $this->_companyIdWisePvCompanyName[$companyId];
            if(isset($this->_companyWiseRequirementCounts[$isUnique][$pvCompanyName])){
                return $this->_companyWiseRequirementCounts[$isUnique][$pvCompanyName];
            }
        }

        return 0;
    }

    public function getTotalSubmissionCounts($companyId, $allCompanyIds, $date): int
    {
        if(!$this->_companyWiseTotalSubmissionCounts){
            if(!$this->_companyIdWisePvCompanyName){
                $this->prepareCompanyIdWisePvCompanyData($allCompanyIds);
            }

            $collection = Requirement::leftJoin('submissions', 'requirements.id', '=', 'submissions.requirement_id')
                ->whereIn('requirements.pv_company_name', $this->_companyIdWisePvCompanyName);
                if($date && isset($date['from']) && $date['to']){
                    $collection->whereBetween('submissions.created_at', $date);
                }
                $collection->groupBy('requirements.pv_company_name')
                ->selectRaw('requirements.pv_company_name, COUNT(submissions.id) as count');

            $this->_companyWiseTotalSubmissionCounts = $collection->pluck('count', 'requirements.pv_company_name')->toArray();
        }

        if($this->_companyIdWisePvCompanyName && isset($this->_companyIdWisePvCompanyName[$companyId])){
            $pvCompanyName = $this->_companyIdWisePvCompanyName[$companyId];
            if(isset($this->_companyWiseTotalSubmissionCounts[$pvCompanyName])){
                return $this->_companyWiseTotalSubmissionCounts[$pvCompanyName];
            }
        }

        return 0;
    }

    public function getPVCompanyWiseStatusCount($filedName, $status, $companyId, $allCompanyIds, $date): int
    {
        if(!$this->_companyWiseTotalStatusCounts || !isset($this->_companyWiseTotalStatusCounts[$status])){
            if(!$this->_companyIdWisePvCompanyName){
                $this->prepareCompanyIdWisePvCompanyData($allCompanyIds);
            }

            $collection = $this->getJoin($status, $filedName, $date);
            $collection->whereIn('requirements.pv_company_name', $this->_companyIdWisePvCompanyName)
                ->groupBy('requirements.pv_company_name')
                ->selectRaw('requirements.pv_company_name, COUNT(submissions.id) as count');

            $this->_companyWiseTotalStatusCounts[$status] = $collection->pluck('count', 'requirements.pv_company_name')->toArray();
        }

        if($this->_companyIdWisePvCompanyName && isset($this->_companyIdWisePvCompanyName[$companyId])){
            $pvCompanyName = $this->_companyIdWisePvCompanyName[$companyId];
            if(isset($this->_companyWiseTotalStatusCounts[$status][$pvCompanyName])){
                return $this->_companyWiseTotalStatusCounts[$status][$pvCompanyName];
            }
        }

        return 0;
    }

    public function getCompanyWiseTotalClientStatusCount($status, $companyId, $allCompanyIds, $date): int
    {
        if(!$this->_companyWiseTotalClientStatusCounts || !isset($this->_companyWiseTotalClientStatusCounts[$status])){
            if(!$this->_companyIdWisePvCompanyName){
                $this->prepareCompanyIdWisePvCompanyData($allCompanyIds);
            }
            $collection =  Requirement::leftJoin('submissions', 'requirements.id', '=', 'submissions.requirement_id')
                ->leftJoin('interviews', 'submissions.id', '=', 'interviews.submission_id');
                if($status != 'all'){
                    $collection->where('interviews.status', $status);

                }
                if($date && isset($date['from']) && $date['to']){
                    $collection->whereBetween('interviews.updated_at', $date);
                }
            $collection->whereIn('requirements.pv_company_name', $this->_companyIdWisePvCompanyName)
                ->groupBy('requirements.pv_company_name')
                ->selectRaw('requirements.pv_company_name, COUNT(interviews.id) as count');

            $this->_companyWiseTotalClientStatusCounts[$status] = $collection->pluck('count', 'requirements.pv_company_name')->toArray();
        }
        if($this->_companyIdWisePvCompanyName && isset($this->_companyIdWisePvCompanyName[$companyId])) {
            $pvCompanyName = $this->_companyIdWisePvCompanyName[$companyId];
            if (isset($this->_companyWiseTotalClientStatusCounts[$status][$pvCompanyName])) {
                return $this->_companyWiseTotalClientStatusCounts[$status][$pvCompanyName];
            }
        }

        return 0;
    }

    public function getCompanyWiseTotalPocCount($companyId, $allCompanyIds, $date): int
    {
        if(!$this->_companyWiseTotalPocCount){
            if(!$this->_companyIdWisePvCompanyName){
                $this->prepareCompanyIdWisePvCompanyData($allCompanyIds);
            }

            $collection = Requirement::select('pv_company_name', \DB::raw("COUNT(DISTINCT poc_name) as count"))
                ->whereIn('pv_company_name', $this->_companyIdWisePvCompanyName);
            if($date && isset($date['from']) && $date['to']){
                $collection->whereBetween('created_at', $date);
            }
            $collection->groupBy('pv_company_name');

            $this->_companyWiseTotalPocCount = $collection->pluck('count', 'requirements.pv_company_name')->toArray();
        }
        if($this->_companyIdWisePvCompanyName && isset($this->_companyIdWisePvCompanyName[$companyId])) {
            $pvCompanyName = $this->_companyIdWisePvCompanyName[$companyId];
            if (isset($this->_companyWiseTotalPocCount[$pvCompanyName])) {
                return $this->_companyWiseTotalPocCount[$pvCompanyName];
            }
        }

        return 0;
    }

    public function getCompanyWiseHighestUniqueRequirementByPoc($companyId, $allCompanyIds): int
    {
        if(!$this->_companyWiseHighestUniqueRequirementByPoc){
            if(!$this->_companyIdWisePvCompanyName){
                $this->prepareCompanyIdWisePvCompanyData($allCompanyIds);
            }

            $this->_companyWiseHighestUniqueRequirementByPoc = Requirement::select('pv_company_name', 'poc_name', \DB::raw('COUNT(*) as poc_count'))
                ->whereIn('pv_company_name', $this->_companyIdWisePvCompanyName)
                ->where(function ($query) {
                        $query->where('id' ,\DB::raw('parent_requirement_id'));
                        $query->orwhere('parent_requirement_id', '=', '0');
                })
                ->groupBy(['pv_company_name', 'poc_name'])
                ->get()
                ->groupBy('pv_company_name')
                ->map(function ($group) {
                    return $group->max('poc_count');
                })
                ->toArray();
        }

        if($this->_companyIdWisePvCompanyName && isset($this->_companyIdWisePvCompanyName[$companyId])) {
            $pvCompanyName = $this->_companyIdWisePvCompanyName[$companyId];
            if (isset($this->_companyWiseHighestUniqueRequirementByPoc[$pvCompanyName])) {
                return $this->_companyWiseHighestUniqueRequirementByPoc[$pvCompanyName];
            }
        }

        return 0;

    }

    public function getCompanyWiseTotalBDMCount($companyId, $allCompanyIds, $date): int
    {
        if(!$this->_companyWiseTotalBDMCount){
            if(!$this->_companyIdWisePvCompanyName){
                $this->prepareCompanyIdWisePvCompanyData($allCompanyIds);
            }

            $collection = Requirement::select('pv_company_name', \DB::raw("COUNT(DISTINCT user_id) as count"))
                ->whereIn('pv_company_name', $this->_companyIdWisePvCompanyName);
            if($date && isset($date['from']) && $date['to']){
                $collection->whereBetween('created_at', $date);
            }
            $collection->groupBy('pv_company_name');

            $this->_companyWiseTotalBDMCount = $collection->pluck('count', 'requirements.pv_company_name')->toArray();
        }
        if($this->_companyIdWisePvCompanyName && isset($this->_companyIdWisePvCompanyName[$companyId])) {
            $pvCompanyName = $this->_companyIdWisePvCompanyName[$companyId];
            if (isset($this->_companyWiseTotalBDMCount[$pvCompanyName])) {
                return $this->_companyWiseTotalBDMCount[$pvCompanyName];
            }
        }

        return 0;
    }

    public function getCompanyWiseCategories($companyId, $allCompanyIds, $date): array
    {
        if(!$this->_companyWiseCategories){
            if(!$this->_companyIdWisePvCompanyName){
                $this->prepareCompanyIdWisePvCompanyData($allCompanyIds);
            }

            $collection = Requirement::select(
                    'requirements.pv_company_name',
                    \DB::raw('GROUP_CONCAT(categories.name) as category_names'),
                )
                ->whereIn('pv_company_name', $this->_companyIdWisePvCompanyName)
                ->leftJoin('categories', 'requirements.category', '=', 'categories.id');
                if($date && isset($date['from']) && $date['to']){
                    $collection->whereBetween('requirements.created_at', $date);
                }

            $pvCompanyData = $collection->groupBy('requirements.pv_company_name')->get();

            if($pvCompanyData){
                foreach ($pvCompanyData as $data){
                    $pvCompanyName = $data->pv_company_name;
                    if(!$pvCompanyName){
                        continue;
                    }
                    $categoryNameArray = explode(',', $data->category_names);
                    $categoryCount = array_count_values($categoryNameArray);

                    $categoryString = [];

                    foreach (array_unique($categoryNameArray) as $category){
                        $categoryString[] = $category .' ('. (isset($categoryCount[$category]) ? $categoryCount[$category] : 0) . ')';
                    }

                    $this->_companyWiseCategories[$pvCompanyName] = $categoryString;
                }
            }
        }

        if($this->_companyIdWisePvCompanyName && isset($this->_companyIdWisePvCompanyName[$companyId])) {
            $pvCompanyName = $this->_companyIdWisePvCompanyName[$companyId];
            if (isset($this->_companyWiseCategories[$pvCompanyName])) {
                return $this->_companyWiseCategories[$pvCompanyName];
            }
        }

        return [];
    }

    public function getCompanyWiseBDM($companyId, $allCompanyIds, $date): array
    {
        if(!$this->_companyWiseBDMs){
            if(!$this->_companyIdWisePvCompanyName){
                $this->prepareCompanyIdWisePvCompanyData($allCompanyIds);
            }

            $collection = Requirement::select(
                    'requirements.pv_company_name',
                    \DB::raw('GROUP_CONCAT(admins.name) as admin'),
                )
                ->whereIn('pv_company_name', $this->_companyIdWisePvCompanyName);
                if($date && isset($date['from']) && $date['to']){
                    $collection->whereBetween('requirements.created_at', $date);
                }
            $collection->leftJoin('admins', 'requirements.user_id', '=', 'admins.id');

            $pvCompanyData = $collection->groupBy('requirements.pv_company_name')->get();

            if($pvCompanyData){
                foreach ($pvCompanyData as $data){
                    $pvCompanyName = $data->pv_company_name;
                    if(!$pvCompanyName){
                        continue;
                    }
                    $bdmNameArray = explode(',', $data->admin);
                    $bdmCount = array_count_values($bdmNameArray);

                    $bdmNameString = [];

                    foreach (array_unique($bdmNameArray) as $bdmName){
                        $bdmNameString[] = $bdmName .' ('. (isset($bdmCount[$bdmName]) ? $bdmCount[$bdmName] : 0) . ')';
                    }

                    $this->_companyWiseBDMs[$pvCompanyName] = $bdmNameString;
                }
            }
        }

        if($this->_companyIdWisePvCompanyName && isset($this->_companyIdWisePvCompanyName[$companyId])) {
            $pvCompanyName = $this->_companyIdWisePvCompanyName[$companyId];
            if (isset($this->_companyWiseBDMs[$pvCompanyName])) {
                return $this->_companyWiseBDMs[$pvCompanyName];
            }
        }

        return [];
    }

    public function getPVCompanyClass(): array
    {
        return [
            'company_name'                      => '',
            'added_date'                        => 'font-weight-bold',
            'last_req_date'                     => 'font-weight-bold',
            'original_req_count'                => 'font-weight-bold',
            'unique_req_count'                  => 'font-weight-bold border border-success p-2',
            'submission_count'                  => 'font-weight-bold',
            'status_accepted'                   => 'font-weight-bold border border-success p-2',
            'status_rejected'                   => 'font-weight-bold',
            'status_pending'                    => 'font-weight-bold',
            'status_unviewed'                   => 'font-weight-bold',
            'status_vendor_no_response'         => 'font-weight-bold border border-danger p-2',
            'status_vendor_rejected_by_pv'      => 'font-weight-bold border border-danger p-2',
            'status_rejected_by_client'         => 'font-weight-bold border border-danger p-2',
            'status_submitted_to_end_client'    => 'font-weight-bold border border-success p-2',
            'status_position_closed'            => 'font-weight-bold',
            'status_scheduled'                  => 'font-weight-bold',
            'status_re_scheduled'               => 'font-weight-bold',
            'status_selected_for_another_round' => 'font-weight-bold',
            'status_waiting_feedback'           => 'font-weight-bold border border-primary p-2',
            'status_position_confirm'           => 'font-weight-bold border border-success p-2',
            'status_client_rejected'            => 'font-weight-bold border border-danger p-2',
            'status_backout'                    => 'font-weight-bold border border-dark p-2',
            'client_status_total'               => 'font-weight-bold border border-success p-2',
            'poc_count'                         => 'font-weight-bold',
            'avg'                               => 'font-weight-bold',
            'highest_uni_req_by_poc'            => 'font-weight-bold',
            'bdm_count'                         => 'font-weight-bold',
            'category_wise_count'               => 'font-weight-bold',
            'bdm_wise_count'                    => 'font-weight-bold',
        ];
    }

    public function getPVCompanyWisePocRequirementCounts($pvCompanyName, $pocName, $allPocNames, $date, $isUnique = 0): int
    {
        if(!$this->_pvCompanyWisePocRequirementsCounts || !isset($this->_pvCompanyWisePocRequirementsCounts[$isUnique][$pvCompanyName])){
            $collection = Requirement::select('poc_name', \DB::raw("count(id) as count"))
                ->whereIn('poc_name', $allPocNames)
                ->where('pv_company_name', $pvCompanyName);
            if($isUnique){
                $collection->where(function ($query) {
                    $query->where('id' ,\DB::raw('parent_requirement_id'));
                    $query->orwhere('parent_requirement_id', '=', '0');
                });
            }
            if($date && isset($date['from']) && $date['to']){
                $collection->whereBetween('created_at', $date);
            }
            $collection->groupBy('poc_name');
            $this->_pvCompanyWisePocRequirementsCounts[$isUnique][$pvCompanyName] = $collection->pluck('count', 'poc_name')->toArray();
        }

        if(isset($this->_pvCompanyWisePocRequirementsCounts[$isUnique][$pvCompanyName][$pocName])){
            return $this->_pvCompanyWisePocRequirementsCounts[$isUnique][$pvCompanyName][$pocName];
        }

        return 0;
    }

    public function getPVCompanyWisePocAddedDate($pvCompanyName, $pocName, $allPocNames, $date): string
    {
        if(!$this->_pvCompanyWisePocAddedDate || !isset($this->_pvCompanyWisePocAddedDate[$pvCompanyName])){
            $collection = PVCompany::select('poc_name', \DB::raw("DATE_FORMAT(created_at, '%m-%d-%y') as formatted_date"))
                ->whereIn('poc_name', $allPocNames)
                ->where('name', $pvCompanyName);
            if($date && isset($date['from']) && $date['to']){
                $collection->whereBetween('created_at', $date);
            }
            $collection->groupBy('poc_name');
            $this->_pvCompanyWisePocAddedDate[$pvCompanyName] = $collection->pluck('formatted_date', 'poc_name')->toArray();
        }

        if(isset($this->_pvCompanyWisePocAddedDate[$pvCompanyName][$pocName])){
            return $this->_pvCompanyWisePocAddedDate[$pvCompanyName][$pocName];
        }

        return '';
    }

    public function getPVCompanyWisePocLastRequestDate($pvCompanyName, $pocName, $pocNames, $date): string
    {
        if(!$this->_pvCompanyWisePocLastReqDate || !isset($this->_pvCompanyWisePocLastReqDate[$pvCompanyName])){
            $collection = Requirement::select('poc_name', \DB::raw("DATE_FORMAT(MAX(created_at), '%m-%d-%y') as latest_created_at"))
                ->whereIn('poc_name', $pocNames)
                ->where('pv_company_name', $pvCompanyName);
            if($date && isset($date['from']) && $date['to']){
                $collection->whereBetween('created_at', $date);
            }
            $collection->groupBy('poc_name');
            $this->_pvCompanyWisePocLastReqDate[$pvCompanyName] = $collection->pluck('latest_created_at', 'poc_name')->toArray();
        }


        if(isset($this->_pvCompanyWisePocLastReqDate[$pvCompanyName][$pocName])){
            return $this->_pvCompanyWisePocLastReqDate[$pvCompanyName][$pocName];
        }

        return '';
    }

    public function getPVCompanyWisePocTotalSubmissionCounts($pvCompanyName, $pocName, $pocNames, $date): int
    {
        if(!$this->_pvCompanyWisePocTotalSubmissionCounts || !isset($this->_pvCompanyWisePocTotalSubmissionCounts[$pvCompanyName])){
            $collection = Requirement::leftJoin('submissions', 'requirements.id', '=', 'submissions.requirement_id')
                ->whereIn('requirements.poc_name', $pocNames)
                ->where('requirements.pv_company_name', $pvCompanyName);
            if($date && isset($date['from']) && $date['to']){
                $collection->whereBetween('submissions.created_at', $date);
            }
            $collection->groupBy('requirements.poc_name')
                ->selectRaw('requirements.poc_name, COUNT(submissions.id) as count');

            $this->_pvCompanyWisePocTotalSubmissionCounts[$pvCompanyName] = $collection->pluck('count', 'requirements.poc_name')->toArray();
        }

        if(isset($this->_pvCompanyWisePocTotalSubmissionCounts[$pvCompanyName][$pocName])){
            return $this->_pvCompanyWisePocTotalSubmissionCounts[$pvCompanyName][$pocName];
        }

        return 0;
    }

    public function getPVCompanyWisePocStatusCount($pvCompanyName, $filedName, $status, $pocName, $pocNames, $date): int
    {
        if(!$this->_pvCompanyWisePocTotalStatusCounts || !isset($this->_pvCompanyWisePocTotalStatusCounts[$pvCompanyName][$status])){
            $collection = $this->getJoin($status, $filedName, $date);
            $collection->whereIn('requirements.poc_name', $pocNames)
                ->where('requirements.pv_company_name', $pvCompanyName)
                ->groupBy('requirements.poc_name')
                ->selectRaw('requirements.poc_name, COUNT(submissions.id) as count');
            $this->_pvCompanyWisePocTotalStatusCounts[$pvCompanyName][$status] = $collection->pluck('count', 'requirements.poc_name')->toArray();
        }

        if(isset($this->_pvCompanyWisePocTotalStatusCounts[$pvCompanyName][$status][$pocName])){
            return $this->_pvCompanyWisePocTotalStatusCounts[$pvCompanyName][$status][$pocName];
        }

        return 0;
    }

    public function getPVCompanyWisePocClientStatusCount($pvCompanyName, $status, $pocName, $pocNames, $date): int
    {
        if(!$this->_pocWiseTotalClientStatusCounts || !isset($this->_pocWiseTotalClientStatusCounts[$pvCompanyName][$status])){
            $collection =  Requirement::leftJoin('submissions', 'requirements.id', '=', 'submissions.requirement_id')
                ->leftJoin('interviews', 'submissions.id', '=', 'interviews.submission_id');
            if($status != 'all'){
                $collection->where('interviews.status', $status);

            }
            if($date && isset($date['from']) && $date['to']){
                $collection->whereBetween('interviews.updated_at', $date);
            }
            $collection->whereIn('requirements.poc_name', $pocNames)
                ->where('requirements.pv_company_name', $pvCompanyName)
                ->groupBy('requirements.poc_name')
                ->selectRaw('requirements.poc_name, COUNT(interviews.id) as count');

            $this->_pocWiseTotalClientStatusCounts[$pvCompanyName][$status] = $collection->pluck('count', 'requirements.poc_name')->toArray();
        }
        if (isset($this->_pocWiseTotalClientStatusCounts[$pvCompanyName][$status][$pocName])) {
            return $this->_pocWiseTotalClientStatusCounts[$pvCompanyName][$status][$pocName];
        }

        return 0;
    }

    public function getPVCompanyWisePocTotalBDMCount($pvCompanyName, $pocName, $pocNames, $date): int
    {
        if(!$this->_pvCompanyWisepocTotalBDMCount || !isset($this->_pvCompanyWisepocTotalBDMCount[$pvCompanyName])){
            $collection = Requirement::select('poc_name', \DB::raw("COUNT(DISTINCT user_id) as count"))
                ->whereIn('poc_name', $pocNames)
                ->where('pv_company_name', $pvCompanyName);
            if($date && isset($date['from']) && $date['to']){
                $collection->whereBetween('created_at', $date);
            }
            $collection->groupBy('poc_name');

            $this->_pvCompanyWisepocTotalBDMCount[$pvCompanyName] = $collection->pluck('count', 'requirements.poc_name')->toArray();
        }

        if (isset($this->_pvCompanyWisepocTotalBDMCount[$pvCompanyName][$pocName])) {
            return $this->_pvCompanyWisepocTotalBDMCount[$pvCompanyName][$pocName];
        }

        return 0;
    }

    public function getPVCompanyWisePocCategories($pvCompanyName, $pocName, $pocNames, $date): array
    {
        if(!$this->_pvCompanyWisePocCategories || !isset($this->_pvCompanyWisePocCategories[$pvCompanyName])){
            $collection = Requirement::select(
                'requirements.poc_name',
                \DB::raw('GROUP_CONCAT(categories.name) as category_names'),
            )
                ->whereIn('poc_name', $pocNames)
                ->where('pv_company_name', $pvCompanyName)
                ->leftJoin('categories', 'requirements.category', '=', 'categories.id');
            if($date && isset($date['from']) && $date['to']){
                $collection->whereBetween('requirements.created_at', $date);
            }

            $pvCompanyData = $collection->groupBy('requirements.poc_name')->get();

            if($pvCompanyData){
                foreach ($pvCompanyData as $data){
                    $dataPocName = $data->poc_name;
                    if(!$dataPocName){
                        continue;
                    }
                    $categoryNameArray = explode(',', $data->category_names);
                    $categoryCount = array_count_values($categoryNameArray);

                    $categoryString = [];

                    foreach (array_unique($categoryNameArray) as $category){
                        $categoryString[] = $category .' ('. (isset($categoryCount[$category]) ? $categoryCount[$category] : 0) . ')';
                    }

                    $this->_pvCompanyWisePocCategories[$pvCompanyName][$dataPocName] = $categoryString;
                }
            }
        }

        if (isset($this->_pvCompanyWisePocCategories[$pvCompanyName][$pocName])) {
            return $this->_pvCompanyWisePocCategories[$pvCompanyName][$pocName];
        }
        return [];
    }

    public function getPVCompanyWisePocBDM($pvCompanyName, $pocName, $pocNames, $date): array
    {
        if(!$this->_pvCompanyWisePocBDMs || !isset($this->_pvCompanyWisePocBDMs[$pvCompanyName])){
            $collection = Requirement::select(
                'requirements.poc_name',
                \DB::raw('GROUP_CONCAT(admins.name) as admin'),
            )
                ->whereIn('poc_name', $pocNames)
                ->where('pv_company_name', $pvCompanyName)
                ->leftJoin('admins', 'requirements.user_id', '=', 'admins.id');

            if($date && isset($date['from']) && $date['to']){
                $collection->whereBetween('requirements.created_at', $date);
            }

            $pvCompanyData = $collection->groupBy('requirements.poc_name')->get();

            if($pvCompanyData){
                foreach ($pvCompanyData as $data){
                    $dataPocName = $data->poc_name;
                    if(!$dataPocName){
                        continue;
                    }
                    $bdmNameArray = explode(',', $data->admin);
                    $bdmCount = array_count_values($bdmNameArray);

                    $bdmNameString = [];

                    foreach (array_unique($bdmNameArray) as $bdmName){
                        $bdmNameString[] = $bdmName .' ('. (isset($bdmCount[$bdmName]) ? $bdmCount[$bdmName] : 0) . ')';
                    }

                    $this->_pvCompanyWisePocBDMs[$pvCompanyName][$dataPocName] = $bdmNameString;
                    \Log::info($this->_pvCompanyWisePocBDMs);
                }
            }
        }

        if (isset($this->_pvCompanyWisePocBDMs[$pvCompanyName][$pocName])) {
            \Log::info('called');
            \Log::info($this->_pvCompanyWisePocBDMs[$pvCompanyName][$pocName]);
            return $this->_pvCompanyWisePocBDMs[$pvCompanyName][$pocName];
        }

        return [];
    }

    public function getJoin($status, $filedName, $date)
    {
        $collection = Requirement::leftJoin('submissions', 'requirements.id', '=', 'submissions.requirement_id');
        if ($status == Submission::STATUS_NOT_VIEWED) {
            $collection->where('submissions.is_show', '0');
        } elseif ($status == Submission::STATUS_PENDING) {
            $collection->where('submissions.is_show', '1')
                ->where("submissions.$filedName", $status);
        } else {
            $collection->where("submissions.$filedName", $status);
        }
        if ($date && isset($date['from']) && $date['to']) {
            $collection->whereBetween('submissions.updated_at', $date);
        }
        return $collection;
    }
}
