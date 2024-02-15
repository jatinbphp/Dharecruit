<?php
namespace App\Traits;
use App\Models\Admin;
use App\Models\Interview;
use App\Models\PVCompany;
use App\Models\Requirement;
use App\Models\Submission;

trait EmployerTrait {
    use CommonTrait;
    protected $_employerIdWiseCompanyAddedDate = [];
    protected $_employerWiseCompanyLastSubDate = [];
    protected $_employerWiseSubmissionCounts = [];
    protected $_employerWiseTotalSubmissionCounts = [];
    protected $_employerWiseTotalStatusCounts = [];
    protected $_employerWiseTotalClientStatusCounts = [];
    protected $_employerWiseHighestUniqueSubmissionByEmployee = [];
    protected $_employerWiseTotalPocCount = [];
    protected $_companyWiseTotalRecCount = [];
    protected $_employerWiseCategories = [];
    protected $_employerWiseRecruiters = [];
    protected $_isEmptyEmployerRow = 0;
    protected $_emptyEmployerRows = [];

    public function setIsEmptyEmployerRow($value)
    {
        $this->_isEmptyEmployerRow = $value;
        return $this;
    }

    public function getIsEmptyEmployerRow()
    {
        return $this->_isEmptyEmployerRow;
    }

    public function setEmptyEmployerRows($key)
    {
        $this->_emptyEmployerRows[$key] = $key;
        return $this;
    }

    public function getEmptyEmployerRows()
    {
        return $this->_emptyEmployerRows;
    }

    public function getEmployerHeadingData(): array
    {
        return [
            'company_name'                      => 'Name',
            'employee_count'                    => 'Employee#',
            'added_date'                        => 'Date Added',
            'last_sub_date'                     => 'Last Sub.',
            'total_submission_count'            => 'Total Sub. #',
            'unique_sub_count'                  => 'Uni Sub. #',
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
            'avg'                               => 'Avg',
            'sub_h'                             => 'ESUBH',
            'rec_count'                         => 'Recruiter #',
            'category_wise_count'               => 'Category (Count)',
            'rec_wise_count'                    => 'Recruiter (Count)',
        ];
    }

    public function getEmployerWiseData($employer, $employers, $request): array
    {
        $submissionModel = new Submission();
        $interviewModel  = new Interview();
        $date            = $this->getDate('time_frame', $request);

        $totalUniqueSubmission  = $this->getSubmissionCountsBasedOnEmployer($employer, $employers, $date, 1);
        $totalEmployee          = $this->getEmployerWiseTotalEmployeeCount($employer, $employers, $date);
        $avg                    = 0;

        if($totalUniqueSubmission && $totalEmployee){
            $avg = round($totalUniqueSubmission / $totalEmployee, 2);
        }

        $this->setIsEmptyEmployerRow(1);
        $employerData = [
            'company_name'                      => $employer,
            'employee_count'                    => $totalEmployee,
            'added_date'                        => $this->getEmployerAddedDateBasedOnId($employer, $employers, $date),
            'last_sub_date'                     => $this->getEmployerLastSubmissionDateBasedOnId($employer, $employers, $date),
            'total_submission_count'            => $this->getSubmissionCountsBasedOnEmployer($employer, $employers, $date),
            'unique_sub_count'                  => $totalUniqueSubmission,
            'submission_count'                  => $this->getTotalSubmissionCountsBasedOnEmployer($employer, $employers, $date),
            'status_accepted'                   => $this->getEmployerWiseStatusCount('status',$submissionModel::STATUS_ACCEPT , $employer, $employers, $date),
            'status_rejected'                   => $this->getEmployerWiseStatusCount('status',$submissionModel::STATUS_REJECTED , $employer, $employers, $date),
            'status_pending'                    => $this->getEmployerWiseStatusCount('status',$submissionModel::STATUS_PENDING , $employer, $employers, $date),
            'status_unviewed'                   => $this->getEmployerWiseStatusCount('status',$submissionModel::STATUS_NOT_VIEWED , $employer, $employers, $date),
            'status_vendor_no_response'         => $this->getEmployerWiseStatusCount('pv_status',$submissionModel::STATUS_NO_RESPONSE_FROM_PV , $employer, $employers, $date),
            'status_vendor_rejected_by_pv'      => $this->getEmployerWiseStatusCount('pv_status',$submissionModel::STATUS_REJECTED_BY_PV , $employer, $employers, $date),
            'status_rejected_by_client'         => $this->getEmployerWiseStatusCount('pv_status',$submissionModel::STATUS_REJECTED_BY_END_CLIENT , $employer, $employers, $date),
            'status_submitted_to_end_client'    => $this->getEmployerWiseStatusCount('pv_status',$submissionModel::STATUS_SUBMITTED_TO_END_CLIENT , $employer, $employers, $date),
            'status_position_closed'            => $this->getEmployerWiseStatusCount('pv_status',$submissionModel::STATUS_POSITION_CLOSED , $employer, $employers, $date),
            'status_scheduled'                  => $this->getEmployerTotalClientStatusCount($interviewModel::STATUS_SCHEDULED, $employer, $employers, $date),
            'status_re_scheduled'               => $this->getEmployerTotalClientStatusCount($interviewModel::STATUS_RE_SCHEDULED, $employer, $employers, $date),
            'status_selected_for_another_round' => $this->getEmployerTotalClientStatusCount($interviewModel::STATUS_SELECTED_FOR_NEXT_ROUND, $employer, $employers, $date),
            'status_waiting_feedback'           => $this->getEmployerTotalClientStatusCount($interviewModel::STATUS_WAITING_FEEDBACK, $employer, $employers, $date),
            'status_position_confirm'           => $this->getEmployerTotalClientStatusCount($interviewModel::STATUS_CONFIRMED_POSITION, $employer, $employers, $date),
            'status_client_rejected'            => $this->getEmployerTotalClientStatusCount($interviewModel::STATUS_REJECTED, $employer, $employers, $date),
            'status_backout'                    => $this->getEmployerTotalClientStatusCount($interviewModel::STATUS_BACKOUT, $employer, $employers, $date),
            'client_status_total'               => $this->getEmployerTotalClientStatusCount('all', $employer, $employers, $date),
            'avg'                               => $avg,
            'highest_uni_sub_by_employee'       => $this->getEmployerWiseHighestUniqueSubmissionByEmployee($employer, $employers, $date),
            'rec_count'                         => $this->getEmployerWiseTotalRecruiterCount($employer, $employers, $date),
            'category_wise_count'               => $this->getEmployerWiseCategories($employer, $employers, $date),
            'rec_wise_count'                    => $this->getEmployerWiseRecruiter($employer, $employers, $date),
        ];

        if($this->getIsEmptyEmployerRow()){
            $employerKey = $this->getKey($employer);
            $this->setEmptyEmployerRows($employerKey);
        }

        return  $employerData;
    }

    public function getEmployerWiseEmployeeData($employer, $request): array
    {
        $submissionModel = new Submission();
        $interviewModel  = new Interview();
        $date            = $this->getDate('time_frame', $request);

        if(!$employer){
            return [];
        }

        $employeeNames = Admin::where('name', $employer)->whereNotNull('employee_name')->groupBy('employee_name')->pluck('employee_name')->toArray();
        if(!$employeeNames || !count($employeeNames)){
            return [];
        }
        $employeeNameWiseData = [];

        foreach ($employeeNames as $employeeName) {
            $totalUniqueRequirement = $this->getEmployerWiseEmployeeSubmissionCounts($employer, $employeeName, $employeeNames, $date, 1);

            $this->setIsEmptyPOCRow(1);

            $pocData = [
                'company_name'                      => $employeeName,
                'employee_count'                    => '',
                'added_date'                        => $this->getEmployerWiseEmployeeAddedDate($employer, $employeeName, $employeeNames, $date),
                'last_sub_date'                     => $this->getEmployerWiseEmployeeLastSubmissionDate($employer, $employeeName, $employeeNames, $date),
                'total_submission_count'            => $this->getEmployerWiseEmployeeSubmissionCounts($employer, $employeeName, $employeeNames, $date),
                'unique_sub_count'                  => $totalUniqueRequirement,
                'submission_count'                  => $this->getEmployerWiseEmployeeTotalSubmissionCounts($employer, $employeeName, $employeeNames, $date),
                'status_accepted'                   => $this->getEmployerWiseEmployeeStatusCount($employer, 'status',$submissionModel::STATUS_ACCEPT , $employeeName, $employeeNames, $date),
                'status_rejected'                   => $this->getEmployerWiseEmployeeStatusCount($employer, 'status',$submissionModel::STATUS_REJECTED , $employeeName, $employeeNames, $date),
                'status_pending'                    => $this->getEmployerWiseEmployeeStatusCount($employer, 'status',$submissionModel::STATUS_PENDING , $employeeName, $employeeNames, $date),
                'status_unviewed'                   => $this->getEmployerWiseEmployeeStatusCount($employer, 'status',$submissionModel::STATUS_NOT_VIEWED , $employeeName, $employeeNames, $date),
                'status_vendor_no_response'         => $this->getEmployerWiseEmployeeStatusCount($employer, 'pv_status',$submissionModel::STATUS_NO_RESPONSE_FROM_PV , $employeeName, $employeeNames, $date),
                'status_vendor_rejected_by_pv'      => $this->getEmployerWiseEmployeeStatusCount($employer, 'pv_status',$submissionModel::STATUS_REJECTED_BY_PV , $employeeName, $employeeNames, $date),
                'status_rejected_by_client'         => $this->getEmployerWiseEmployeeStatusCount($employer, 'pv_status',$submissionModel::STATUS_REJECTED_BY_END_CLIENT , $employeeName, $employeeNames, $date),
                'status_submitted_to_end_client'    => $this->getEmployerWiseEmployeeStatusCount($employer, 'pv_status',$submissionModel::STATUS_SUBMITTED_TO_END_CLIENT , $employeeName, $employeeNames, $date),
                'status_position_closed'            => $this->getEmployerWiseEmployeeStatusCount($employer, 'pv_status',$submissionModel::STATUS_POSITION_CLOSED , $employeeName, $employeeNames, $date),
                'status_scheduled'                  => $this->getEmployerWiseEmployeeClientStatusCount($employer,$interviewModel::STATUS_SCHEDULED, $employeeName, $employeeNames, $date),
                'status_re_scheduled'               => $this->getEmployerWiseEmployeeClientStatusCount($employer,$interviewModel::STATUS_RE_SCHEDULED, $employeeName, $employeeNames, $date),
                'status_selected_for_another_round' => $this->getEmployerWiseEmployeeClientStatusCount($employer,$interviewModel::STATUS_SELECTED_FOR_NEXT_ROUND, $employeeName, $employeeNames, $date),
                'status_waiting_feedback'           => $this->getEmployerWiseEmployeeClientStatusCount($employer,$interviewModel::STATUS_WAITING_FEEDBACK, $employeeName, $employeeNames, $date),
                'status_position_confirm'           => $this->getEmployerWiseEmployeeClientStatusCount($employer,$interviewModel::STATUS_CONFIRMED_POSITION, $employeeName, $employeeNames, $date),
                'status_client_rejected'            => $this->getEmployerWiseEmployeeClientStatusCount($employer,$interviewModel::STATUS_REJECTED, $employeeName, $employeeNames, $date),
                'status_backout'                    => $this->getEmployerWiseEmployeeClientStatusCount($employer,$interviewModel::STATUS_BACKOUT, $employeeName, $employeeNames, $date),
                'client_status_total'               => $this->getEmployerWiseEmployeeClientStatusCount($employer,'all', $employeeName, $employeeNames, $date),
                'avg'                               => '',
                'highest_uni_sub_by_employee'       => '',
                'rec_count'                         => $this->getEmployerWiseEmployeeTotalRecruiterCount($employer, $employeeName, $employeeNames, $date),
                'category_wise_count'               => $this->getEmployerWiseEmployeeCategories($employer, $employeeName, $employeeNames, $date),
                'rec_wise_count'                    => $this->getEmployerWiseEmployeeRecruiter($employer, $employeeName, $employeeNames, $date),
            ];

            if($this->getIsEmptyPOCRow()){
                $employerKey = $this->getKey($employer);
                $this->setEmptyPOCRows($employerKey.'_'.$employeeName);
            }

            $employeeNameWiseData[$employeeName] = $pocData;
        }

        return $employeeNameWiseData;
    }

    public function getEmployerAddedDateBasedOnId($employerName, $allEmoloyers, $date): string
    {
        if(!$this->_employerIdWiseCompanyAddedDate){
            $collection = Admin::select('name', \DB::raw("DATE_FORMAT(created_at, '%m-%d-%y') as formatted_date"))
                ->whereIn('name', $allEmoloyers);

            $this->_employerIdWiseCompanyAddedDate = $collection->pluck('formatted_date', 'name')->toArray();
        }

        if(isset($this->_employerIdWiseCompanyAddedDate[$employerName])){
            return $this->_employerIdWiseCompanyAddedDate[$employerName];
        }

        return '';
    }

    public function getEmployerLastSubmissionDateBasedOnId($employerName, $allEmoloyers, $date): string
    {
        if(!$this->_employerWiseCompanyLastSubDate){
            $collection = Submission::whereIn('employer_name', $allEmoloyers)
                ->groupBy('employer_name')
                ->selectRaw("LOWER(employer_name) as employer_name, DATE_FORMAT(MAX(created_at),    '%m-%d-%y') as latest_created_at");

            $this->_employerWiseCompanyLastSubDate = $collection->pluck('latest_created_at', 'employer_name')->toArray();
        }

        $employerName = strtolower($employerName);
        if(isset($this->_employerWiseCompanyLastSubDate[$employerName])){
            return $this->_employerWiseCompanyLastSubDate[$employerName];
        }

        return '';
    }

    public function getSubmissionCountsBasedOnEmployer($employerName, $allEmoloyers, $date, $isUnique = 0): int
    {
        if(!$this->_employerWiseSubmissionCounts || !isset($this->_employerWiseSubmissionCounts[$isUnique])){
            $collection = Submission::whereIn('employer_name', $allEmoloyers);
            if($isUnique){
                $collection->where('id' ,\DB::raw('candidate_id'));
            }
            $collection->groupBy('employer_name')
                ->selectRaw("LOWER(employer_name) as employer_name, count(id) as count");
            if($date && isset($date['from']) && $date['to']){
                $collection->whereBetween('created_at', $date);
            }
            $this->_employerWiseSubmissionCounts[$isUnique] = $collection->pluck('count', 'employer_name')->toArray();
        }

        $employerName = strtolower($employerName);
        if(isset($this->_employerWiseSubmissionCounts[$isUnique][$employerName])){
            $this->setIsEmptyEmployerRow(0);
            return $this->_employerWiseSubmissionCounts[$isUnique][$employerName];
        }

        return 0;
    }

    public function getTotalSubmissionCountsBasedOnEmployer($employerName, $allEmoloyers, $date): int
    {
        if(!$this->_employerWiseTotalSubmissionCounts){
            $collection = Submission::select(\DB::raw('LOWER(employer_name) as employer_name'), \DB::raw("count(id) as count"))
                ->whereIn('employer_name', $allEmoloyers);
                if($date && isset($date['from']) && $date['to']){
                    $collection->whereBetween('created_at', $date);
                }
            $collection->groupBy('employer_name');

            $this->_employerWiseTotalSubmissionCounts = $collection->pluck('count', 'employer_name')->toArray();
        }

        $employerName = strtolower($employerName);
        if(isset($this->_employerWiseTotalSubmissionCounts[$employerName])){
            $this->setIsEmptyEmployerRow(0);
            return $this->_employerWiseTotalSubmissionCounts[$employerName];
        }

        return 0;
    }

    public function getEmployerWiseStatusCount($filedName, $status, $employerName, $allEmoloyers, $date): int
    {
        if(!$this->_employerWiseTotalStatusCounts || !isset($this->_employerWiseTotalStatusCounts[$status])){
            $collection = Submission::select(\DB::raw('LOWER(employer_name) as employer_name'), \DB::raw("count(id) as count"))
                ->whereIn('employer_name', $allEmoloyers);
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

            $collection->groupBy('employer_name');

            $this->_employerWiseTotalStatusCounts[$status] = $collection->pluck('count', 'employer_name')->toArray();
        }

        $employerName = strtolower($employerName);
        if(isset($this->_employerWiseTotalStatusCounts[$status][$employerName])){
            $this->setIsEmptyEmployerRow(0);
            return $this->_employerWiseTotalStatusCounts[$status][$employerName];
        }

        return 0;
    }

    public function getEmployerTotalClientStatusCount($status, $employerName, $allEmoloyers, $date): int
    {
        if(!$this->_employerWiseTotalClientStatusCounts || !isset($this->_employerWiseTotalClientStatusCounts[$status])){
            $collection =  Submission::leftJoin('interviews', 'submissions.id', '=', 'interviews.submission_id');
            if($status != 'all'){
                $collection->where('interviews.status', $status);

            }
            if($date && isset($date['from']) && $date['to']){
                $collection->whereBetween('interviews.updated_at', $date);
            }
            $collection->whereIn('submissions.employer_name', $allEmoloyers)
                ->groupBy('submissions.employer_name')
                ->selectRaw('LOWER(submissions.employer_name) as employer_name, COUNT(interviews.id) as count');

            $this->_employerWiseTotalClientStatusCounts[$status] = $collection->pluck('count', 'employer_name')->toArray();
        }

        $employerName = strtolower($employerName);
        if (isset($this->_employerWiseTotalClientStatusCounts[$status][$employerName])) {
            $this->setIsEmptyEmployerRow(0);
            return $this->_employerWiseTotalClientStatusCounts[$status][$employerName];
        }

        return 0;
    }

    public function getEmployerWiseTotalEmployeeCount($employerName, $allEmoloyers, $date): int
    {
        if(!$this->_employerWiseTotalPocCount){
            $collection = Submission::select(\DB::raw('LOWER(employer_name) as employer_name'), \DB::raw("COUNT(DISTINCT employee_name) as count"))
                ->whereIn('employer_name', $allEmoloyers);
            if($date && isset($date['from']) && $date['to']){
                $collection->whereBetween('created_at', $date);
            }
            $collection->groupBy('employer_name');

            $this->_employerWiseTotalPocCount = $collection->pluck('count', 'employer_name')->toArray();
        }

        $employerName = strtolower($employerName);
        if (isset($this->_employerWiseTotalPocCount[$employerName])) {
            $this->setIsEmptyEmployerRow(0);
            return $this->_employerWiseTotalPocCount[$employerName];
        }

        return 0;
    }

    public function getEmployerWiseHighestUniqueSubmissionByEmployee($employerName, $allEmoloyers, $date): int
    {
        if(!$this->_employerWiseHighestUniqueSubmissionByEmployee){
            $collection = Submission::select(\DB::raw('LOWER(employer_name) as employer_name'), 'employee_name', \DB::raw('COUNT(*) as employee_count'))
                ->whereIn('employer_name', $allEmoloyers)
                ->where(function ($query) {
                    $query->where('id' ,\DB::raw('candidate_id'));
                });
            if($date && isset($date['from']) && $date['to']){
                $collection->whereBetween('created_at', $date);
            }
            $collection->groupBy(['employer_name', 'employee_name']);

            $this->_employerWiseHighestUniqueSubmissionByEmployee = $collection->get()
                ->groupBy('employer_name')
                ->map(function ($group) {
                    return $group->max('employee_count');
                })
                ->toArray();
        }

        $employerName = strtolower($employerName);
        if (isset($this->_employerWiseHighestUniqueSubmissionByEmployee[$employerName])) {
            $this->setIsEmptyEmployerRow(0);
            return $this->_employerWiseHighestUniqueSubmissionByEmployee[$employerName];
        }

        return 0;
    }

    public function getEmployerWiseTotalRecruiterCount($employerName, $allEmoloyers, $date): int
    {
        if(!$this->_companyWiseTotalRecCount){
            $collection = Submission::select(\DB::raw('LOWER(employer_name) as employer_name'), \DB::raw("COUNT(DISTINCT user_id) as count"))
                ->whereIn('employer_name', $allEmoloyers);
            if($date && isset($date['from']) && $date['to']){
                $collection->whereBetween('created_at', $date);
            }
            $collection->groupBy('employer_name');

            $this->_companyWiseTotalRecCount = $collection->pluck('count', 'employer_name')->toArray();
        }

        $employerName = strtolower($employerName);
        if (isset($this->_companyWiseTotalRecCount[$employerName])) {
            $this->setIsEmptyEmployerRow(0);
            return $this->_companyWiseTotalRecCount[$employerName];
        }

        return 0;
    }

    public function getEmployerWiseCategories($employerName, $allEmoloyers, $date): array
    {
        if(!$this->_employerWiseCategories){
            $collection = Requirement::select(
                \DB::raw('LOWER(submissions.employer_name) as employer_name'),
                \DB::raw('GROUP_CONCAT(categories.name) as category_names'),
            )
                ->whereIn('submissions.employer_name', $allEmoloyers)
                ->leftJoin('categories', 'requirements.category', '=', 'categories.id')
                ->leftJoin('submissions', 'requirements.id', '=', 'submissions.requirement_id');
            if($date && isset($date['from']) && $date['to']){
                $collection->whereBetween('submissions.created_at', $date);
            }

            $employerData = $collection->groupBy('submissions.employer_name')->get();

            if($employerData){
                foreach ($employerData as $data){
                    $employerNameData = $data->employer_name;
                    if(!$employerNameData){
                        continue;
                    }
                    $categoryNameArray = explode(',', $data->category_names);
                    $categoryCount = array_count_values($categoryNameArray);

                    $categoryString = [];

                    foreach (array_unique($categoryNameArray) as $category){
                        $categoryString[] = $category .' ('. (isset($categoryCount[$category]) ? $categoryCount[$category] : 0) . ')';
                    }

                    $this->_employerWiseCategories[$employerNameData] = $categoryString;
                }
            }
        }

        $employerName = strtolower($employerName);
        if (isset($this->_employerWiseCategories[$employerName])) {
            $this->setIsEmptyEmployerRow(0);
            return $this->_employerWiseCategories[$employerName];
        }

        return [];
    }

    public function getEmployerWiseRecruiter($employerName, $allEmoloyers, $date): array
    {
        if(!$this->_employerWiseRecruiters){
            $collection = Submission::select(
                \DB::raw('LOWER(employer_name) as employer_name'),
                \DB::raw('GROUP_CONCAT(admins.name) as admin'),
            )
                ->whereIn('employer_name', $allEmoloyers);
            if($date && isset($date['from']) && $date['to']){
                $collection->whereBetween('submissions.created_at', $date);
            }
            $collection->leftJoin('admins', 'user_id', '=', 'admins.id');

            $employerData = $collection->groupBy('employer_name')->get();

            if($employerData){
                foreach ($employerData as $data){
                    $employerNameData = $data->employer_name;
                    if(!$employerNameData){
                        continue;
                    }
                    $recNameArray = explode(',', $data->admin);
                    $recCount = array_count_values($recNameArray);

                    $recNameString = [];

                    foreach (array_unique($recNameArray) as $recName){
                        $recNameString[] = $recName .' ('. (isset($recCount[$recName]) ? $recCount[$recName] : 0) . ')';
                    }

                    $this->_employerWiseRecruiters[$employerNameData] = $recNameString;
                }
            }
        }

        $employerName = strtolower($employerName);
        if (isset($this->_employerWiseRecruiters[$employerName])) {
            $this->setIsEmptyEmployerRow(0);
            return $this->_employerWiseRecruiters[$employerName];
        }

        return [];
    }
}
