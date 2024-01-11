<?php
namespace App\Traits;
use App\Models\PVCompany;
use App\Models\Requirement;
use App\Models\Submission;

trait CommonTrait {
    protected $_pvCompanyWisePocRequirementsCounts = [];
    protected $_pvCompanyWisePocAddedDate = [];
    protected $_pvCompanyWisePocLastReqDate = [];
    protected $_pvCompanyWisePocTotalSubmissionCounts = [];
    protected $_pvCompanyWisePocTotalStatusCounts = [];
    protected $_pocWiseTotalClientStatusCounts = [];
    protected $_pvCompanyWisepocTotalBDMCount = [];
    protected $_pvCompanyWisePocCategories = [];
    protected $_pvCompanyWisePocBDMs = [];

    protected $_pvCompanyWisePocEmail = [];
    protected $_pvCompanyWisePocPhone = [];
    protected $_isEmptyPOCRow = 0;
    protected $_emptyPOCRows = [];

    public function setIsEmptyPOCRow($value)
    {
        $this->_isEmptyPOCRow = $value;
        return $this;
    }

    public function getIsEmptyPOCRow()
    {
        return $this->_isEmptyPOCRow;
    }

    public function setEmptyPOCRows($key)
    {
        $this->_emptyPOCRows[$key] = $key;
        return $this;
    }

    public function getEmptyPOCRows()
    {
        return $this->_emptyPOCRows;
    }
    public function formatString($input): string
    {
        return ucwords(str_replace('_', ' ', $input));
    }

    public function getPercentage($value, $total): float
    {
        if(!$total){
            return 0;
        }
        return  round((($value * 100) / $total), 2);
    }

    public function getDate($type, $request): array
    {
        $date = [];
        switch ($type){
            case 'today':
                $date['from'] = \Carbon\Carbon::now()->format('Y-m-d');
                $date['to']   = \Carbon\Carbon::now()->addDay()->format('Y-m-d');
                break;
            case 'this_week':
                $date['from'] = \Carbon\Carbon::now()->startOfWeek()->format('Y-m-d');
                $date['to']   = \Carbon\Carbon::now()->endOfWeek()->addDay()->format('Y-m-d');
                break;
            case 'last_week':
                $date['from'] = \Carbon\Carbon::now()->subWeek()->startOfWeek()->format('Y-m-d');;
                $date['to']   = \Carbon\Carbon::now()->subWeek()->endOfWeek()->addDay()->format('Y-m-d');;
                break;
            case 'this_month':
                $date['from'] = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
                $date['to']   = \Carbon\Carbon::now()->endOfMonth()->addDay()->format('Y-m-d');
                break;
            case 'last_month':
                $date['from'] = \Carbon\Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
                $date['to']   = \Carbon\Carbon::now()->subMonth()->endOfMonth()->addDay()->format('Y-m-d');
                break;
            case 'time_frame':
                if($request->fromDate && $request->toDate){
                    $date['from'] = \Carbon\Carbon::createFromFormat('m/d/Y', $request->fromDate)->format('Y-m-d');
                    $date['to']   = \Carbon\Carbon::createFromFormat('m/d/Y', $request->toDate)->addDay()->format('Y-m-d');
                }
                break;
            default:
                $date['from'] = \Carbon\Carbon::now()->format('Y-m-d');
                $date['to']   = \Carbon\Carbon::now()->addDay()->format('Y-m-d');
        }
        return  $date;
    }

    public function getKeyWiseClass(): array
    {
        return [
            'heading_type'                   => 'font-weight-bold',
            'total_req'                      => 'font-weight-bold',
            'total_uni_req'                  => '',
            'served'                         => '',
            'unserved'                       => '',
            'servable_per'                   => '',
            'submission_received'            => 'font-weight-bold',
            'bdm_accept'                     => 'text-success',
            'bdm_rejected'                   => 'text-danger',
            'bdm_unviewed'                   => 'text-primary',
            'bdm_pending'                    => 'text-primary',
            'vendor_no_responce'             => 'text-secondary',
            'vendor_rejected_by_pv'          => 'text-danger',
            'vendor_rejected_by_client'      => 'font-weight-bold text-danger',
            'vendor_submitted_to_end_client' => 'font-weight-bold text-success',
            'vendor_position_closed'         => 'text-secondary',
            'client_rescheduled'             => 'text-warning',
            'client_selected_for_next_round' => 'font-weight-bold text-warning',
            'client_waiting_feedback'        => '',
            'client_confirmed_position'      => 'font-weight-bold text-success',
            'client_rejected'                => 'font-weight-bold text-danger',
            'client_backout'                 => 'font-weight-bold text-dark',
        ];
    }

    public function getPVCompanyWisePocRequirementCounts($pvCompanyName, $pocName, $allPocNames, $date, $isUnique = 0): int
    {
        $pvCompanyKey = $this->getKey($pvCompanyName);
        if(!$this->_pvCompanyWisePocRequirementsCounts || !isset($this->_pvCompanyWisePocRequirementsCounts[$isUnique][$pvCompanyKey])){
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
            $this->_pvCompanyWisePocRequirementsCounts[$isUnique][$pvCompanyKey] = $collection->pluck('count', 'poc_name')->toArray();
        }

        if(isset($this->_pvCompanyWisePocRequirementsCounts[$isUnique][$pvCompanyKey][$pocName])){
            $this->setIsEmptyPOCRow(0);
            return $this->_pvCompanyWisePocRequirementsCounts[$isUnique][$pvCompanyKey][$pocName];
        }

        return 0;
    }

    public function getPVCompanyWisePocAddedDate($pvCompanyName, $pocName, $allPocNames, $date): string
    {
        $pvCompanyKey = $this->getKey($pvCompanyName);
        if(!$this->_pvCompanyWisePocAddedDate || !isset($this->_pvCompanyWisePocAddedDate[$pvCompanyKey])){
            $collection = PVCompany::select('poc_name', \DB::raw("DATE_FORMAT(created_at, '%m-%d-%y') as formatted_date"))
                ->whereIn('poc_name', $allPocNames)
                ->where('name', $pvCompanyName);
            /*if($date && isset($date['from']) && $date['to']){
                $collection->whereBetween('created_at', $date);
            }*/
            $collection->groupBy('poc_name');
            $this->_pvCompanyWisePocAddedDate[$pvCompanyKey] = $collection->pluck('formatted_date', 'poc_name')->toArray();
        }

        if(isset($this->_pvCompanyWisePocAddedDate[$pvCompanyKey][$pocName])){
            /*$this->setIsEmptyPOCRow(0);*/
            return $this->_pvCompanyWisePocAddedDate[$pvCompanyKey][$pocName];
        }

        return '';
    }

    public function getPVCompanyWisePocLastRequestDate($pvCompanyName, $pocName, $pocNames, $date): string
    {
        $pvCompanyKey = $this->getKey($pvCompanyName);
        if(!$this->_pvCompanyWisePocLastReqDate || !isset($this->_pvCompanyWisePocLastReqDate[$pvCompanyKey])){
            $collection = Requirement::select('poc_name', \DB::raw("DATE_FORMAT(MAX(created_at), '%m-%d-%y') as latest_created_at"))
                ->whereIn('poc_name', $pocNames)
                ->where('pv_company_name', $pvCompanyName);
            /*if($date && isset($date['from']) && $date['to']){
                $collection->whereBetween('created_at', $date);
            }*/
            $collection->groupBy('poc_name');
            $this->_pvCompanyWisePocLastReqDate[$pvCompanyKey] = $collection->pluck('latest_created_at', 'poc_name')->toArray();
        }


        if(isset($this->_pvCompanyWisePocLastReqDate[$pvCompanyKey][$pocName])){
            /*$this->setIsEmptyPOCRow(0);*/
            return $this->_pvCompanyWisePocLastReqDate[$pvCompanyKey][$pocName];
        }

        return '';
    }

    public function getPVCompanyWisePocTotalSubmissionCounts($pvCompanyName, $pocName, $pocNames, $date): int
    {
        $pvCompanyKey = $this->getKey($pvCompanyName);
        if(!$this->_pvCompanyWisePocTotalSubmissionCounts || !isset($this->_pvCompanyWisePocTotalSubmissionCounts[$pvCompanyKey])){
            $collection = Requirement::leftJoin('submissions', 'requirements.id', '=', 'submissions.requirement_id')
                ->whereIn('requirements.poc_name', $pocNames)
                ->where('requirements.pv_company_name', $pvCompanyName);
            if($date && isset($date['from']) && $date['to']){
                $collection->whereBetween('submissions.created_at', $date);
            }
            $collection->groupBy('requirements.poc_name')
                ->selectRaw('requirements.poc_name, COUNT(submissions.id) as count');

            $this->_pvCompanyWisePocTotalSubmissionCounts[$pvCompanyKey] = $collection->pluck('count', 'requirements.poc_name')->toArray();
        }

        if(isset($this->_pvCompanyWisePocTotalSubmissionCounts[$pvCompanyKey][$pocName])){
            $this->setIsEmptyPOCRow(0);
            return $this->_pvCompanyWisePocTotalSubmissionCounts[$pvCompanyKey][$pocName];
        }

        return 0;
    }

    public function getPVCompanyWisePocStatusCount($pvCompanyName, $filedName, $status, $pocName, $pocNames, $date): int
    {
        $pvCompanyKey = $this->getKey($pvCompanyName);
        if(!$this->_pvCompanyWisePocTotalStatusCounts || !isset($this->_pvCompanyWisePocTotalStatusCounts[$pvCompanyKey][$status])){
            $collection = $this->getJoin($status, $filedName, $date);
            $collection->whereIn('requirements.poc_name', $pocNames)
                ->where('requirements.pv_company_name', $pvCompanyName)
                ->groupBy('requirements.poc_name')
                ->selectRaw('requirements.poc_name, COUNT(submissions.id) as count');
            $this->_pvCompanyWisePocTotalStatusCounts[$pvCompanyKey][$status] = $collection->pluck('count', 'requirements.poc_name')->toArray();
        }

        if(isset($this->_pvCompanyWisePocTotalStatusCounts[$pvCompanyKey][$status][$pocName])){
            $this->setIsEmptyPOCRow(0);
            return $this->_pvCompanyWisePocTotalStatusCounts[$pvCompanyKey][$status][$pocName];
        }

        return 0;
    }

    public function getPVCompanyWisePocClientStatusCount($pvCompanyName, $status, $pocName, $pocNames, $date): int
    {
        $pvCompanyKey = $this->getKey($pvCompanyName);
        if(!$this->_pocWiseTotalClientStatusCounts || !isset($this->_pocWiseTotalClientStatusCounts[$pvCompanyKey][$status])){
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

            $this->_pocWiseTotalClientStatusCounts[$pvCompanyKey][$status] = $collection->pluck('count', 'requirements.poc_name')->toArray();
        }
        if (isset($this->_pocWiseTotalClientStatusCounts[$pvCompanyKey][$status][$pocName])) {
            $this->setIsEmptyPOCRow(0);
            return $this->_pocWiseTotalClientStatusCounts[$pvCompanyKey][$status][$pocName];
        }

        return 0;
    }

    public function getPVCompanyWisePocTotalBDMCount($pvCompanyName, $pocName, $pocNames, $date): int
    {
        $pvCompanyKey = $this->getKey($pvCompanyName);
        if(!$this->_pvCompanyWisepocTotalBDMCount || !isset($this->_pvCompanyWisepocTotalBDMCount[$pvCompanyKey])){
            $collection = Requirement::select('poc_name', \DB::raw("COUNT(DISTINCT user_id) as count"))
                ->whereIn('poc_name', $pocNames)
                ->where('pv_company_name', $pvCompanyName);
            if($date && isset($date['from']) && $date['to']){
                $collection->whereBetween('created_at', $date);
            }
            $collection->groupBy('poc_name');

            $this->_pvCompanyWisepocTotalBDMCount[$pvCompanyKey] = $collection->pluck('count', 'requirements.poc_name')->toArray();
        }

        if (isset($this->_pvCompanyWisepocTotalBDMCount[$pvCompanyKey][$pocName])) {
            $this->setIsEmptyPOCRow(0);
            return $this->_pvCompanyWisepocTotalBDMCount[$pvCompanyKey][$pocName];
        }

        return 0;
    }

    public function getPVCompanyWisePocCategories($pvCompanyName, $pocName, $pocNames, $date): array
    {
        $pvCompanyKey = $this->getKey($pvCompanyName);
        if(!$this->_pvCompanyWisePocCategories || !isset($this->_pvCompanyWisePocCategories[$pvCompanyKey])){
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

                    $this->_pvCompanyWisePocCategories[$pvCompanyKey][$dataPocName] = $categoryString;
                }
            }
        }

        if (isset($this->_pvCompanyWisePocCategories[$pvCompanyKey][$pocName])) {
            $this->setIsEmptyPOCRow(0);
            return $this->_pvCompanyWisePocCategories[$pvCompanyKey][$pocName];
        }
        return [];
    }

    public function getPVCompanyWisePocBDM($pvCompanyName, $pocName, $pocNames, $date): array
    {
        $pvCompanyKey = $this->getKey($pvCompanyName);
        if(!$this->_pvCompanyWisePocBDMs || !isset($this->_pvCompanyWisePocBDMs[$pvCompanyKey])){
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

                    $this->_pvCompanyWisePocBDMs[$pvCompanyKey][$dataPocName] = $bdmNameString;
                }
            }
        }

        if (isset($this->_pvCompanyWisePocBDMs[$pvCompanyKey][$pocName])) {
            $this->setIsEmptyPOCRow(0);
            return $this->_pvCompanyWisePocBDMs[$pvCompanyKey][$pocName];
        }

        return [];
    }

    public function getPVCompanyWisePocEmail($pvCompanyName, $pocName, $pocNames, $date)
    {
        $pvCompanyKey = $this->getKey($pvCompanyName);
        if(!$this->_pvCompanyWisePocEmail || !isset($this->_pvCompanyWisePocEmail[$pvCompanyKey])){
            $collection = PVCompany::select('poc_name', 'email')
                ->whereIn('poc_name', $pocNames)
                ->where('name', $pvCompanyName)
                ->groupBy('poc_name');
            $this->_pvCompanyWisePocEmail[$pvCompanyKey] = $collection->pluck('email', 'poc_name')->toArray();
        }

        if(isset($this->_pvCompanyWisePocEmail[$pvCompanyKey][$pocName])){
            return $this->_pvCompanyWisePocEmail[$pvCompanyKey][$pocName];
        }

        return '';
    }

    public function getPVCompanyWisePocPhone($pvCompanyName, $pocName, $pocNames, $date)
    {
        $pvCompanyKey = $this->getKey($pvCompanyName);
        if(!$this->_pvCompanyWisePocPhone || !isset($this->_pvCompanyWisePocPhone[$pvCompanyKey])){
            $collection = PVCompany::select('poc_name', 'phone')
                ->whereIn('poc_name', $pocNames)
                ->where('name', $pvCompanyName)
                ->groupBy('poc_name');
            $this->_pvCompanyWisePocPhone[$pvCompanyKey] = $collection->pluck('phone', 'poc_name')->toArray();
        }

        if(isset($this->_pvCompanyWisePocPhone[$pvCompanyKey][$pocName])){
            return $this->_pvCompanyWisePocPhone[$pvCompanyKey][$pocName];
        }

        return '';
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

    public function getPVClass(): array
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

    public function getKey($data): string
    {
        if(!$data){
            return '';
        }
        return strtolower(str_replace([' ', '.'], ['_', '_'], preg_replace('/[^a-zA-Z0-9.]/', '_', $data)));
//        return strtolower(str_replace([' ', '.'], ['_', ''], $data));
    }
}
