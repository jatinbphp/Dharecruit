<?php
namespace App\Traits;
use App\Models\Admin;
use App\Models\Interview;
use App\Models\PVCompany;
use App\Models\Requirement;
use App\Models\Submission;

trait EmployeeTrait {
    use CommonTrait;

    protected $_employeeWiseWhoAddedUserName = [];
    public function getEmployeeHeadingData(): array
    {
        return [
            'who_added'                         => 'Who Added',
            'employer_company_name'             => 'Employer Name',
            'employer_company_total_sub'        => 'Employer Total Sub.',
            'employee_name'                     => 'Employee Name',
            'employee_email'                    => 'Employee Email',
            'employee_phone'                    => 'Employee Phone',
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
            'category_wise_count'               => 'Category (Count)',
            'rec_wise_count'                    => 'Recruiter (Count)',
        ];
    }

    public function getEmployeeWiseData($employerName, $employerNames, $selectedEmployerNames, $request): array
    {
        $submissionModel = new Submission();
        $interviewModel  = new Interview();
        $date            = $this->getDate('time_frame', $request);

        if(!$employerName){
            return [];
        }

        $collection = Admin::where('name', $employerName);

        if(!empty($request->vendor_email)){
            $collection->where('email', $request->vendor_email);
        }

        if(!empty($request->vendor_phone)){
            $collection->where('phone', $request->vendor_phone);
        }

        if(!empty($request->who_added)){
            $collection->whereIn('added_by', $request->who_added);
        }

        $employerWiseAllEmployeeNames = $collection->distinct()->pluck('employee_name')->toArray();

        if(!empty($request->recruiter_names)){
            $collection = Submission::whereIn('user_id', $request->recruiter_names)->where('employer_name', $employerName);
            if($date && isset($date['from']) && $date['to']){
                $collection->whereBetween('created_at', $date);
            }
            $bdmWiseNames = $collection->distinct()->pluck('employee_name')->toArray();
            $employerWiseAllEmployeeNames = array_intersect($bdmWiseNames, $employerWiseAllEmployeeNames);
        }
        $employeeNames = array_intersect($selectedEmployerNames, $employerWiseAllEmployeeNames);

        if(!$employeeNames || !count($employeeNames)){
            return [];
        }

        $employeeNameWiseData = [];

        foreach ($employeeNames as $employeeName) {
            $this->setIsEmptyPOCRow(1);
            $totalUniqueSubmission = $this->getEmployerWiseEmployeeSubmissionCounts($employerName, $employeeName, $employeeNames, $date, 1);

            $pocData = [
                'who_added'                         => $this->getWhoAddedEmployeeName($employerName, $employeeName, $employeeNames),
                'employer_company_name'             => $employerName,
                'employer_company_total_sub'        => $this->getSubmissionCountsBasedOnEmployer($employerName, $employerNames, $date),
                'employee_name'                     => $employeeName,
                'employee_email'                    => $this->getEmployerWiseEmployeeEmail($employerName, $employeeName, $employeeNames, $date),
                'employee_phone'                    => $this->getEmployerWiseEmployeePhone($employerName, $employeeName, $employeeNames, $date),
                'added_date'                        => $this->getEmployerWiseEmployeeAddedDate($employerName, $employeeName, $employeeNames, $date),
                'last_sub_date'                     => $this->getEmployerWiseEmployeeLastSubmissionDate($employerName, $employeeName, $employeeNames, $date),
                'total_submission_count'            => $this->getEmployerWiseEmployeeTotalSubmissionCounts($employerName, $employeeName, $employeeNames, $date),
                'unique_sub_count'                  => $totalUniqueSubmission,
                'submission_count'                  => $this->getEmployerWiseEmployeeTotalSubmissionCounts($employerName, $employeeName, $employeeNames, $date),
                'status_accepted'                   => $this->getEmployerWiseEmployeeStatusCount($employerName, 'status',$submissionModel::STATUS_ACCEPT , $employeeName, $employeeNames, $date),
                'status_rejected'                   => $this->getEmployerWiseEmployeeStatusCount($employerName, 'status',$submissionModel::STATUS_REJECTED , $employeeName, $employeeNames, $date),
                'status_pending'                    => $this->getEmployerWiseEmployeeStatusCount($employerName, 'status',$submissionModel::STATUS_PENDING , $employeeName, $employeeNames, $date),
                'status_unviewed'                   => $this->getEmployerWiseEmployeeStatusCount($employerName, 'status',$submissionModel::STATUS_NOT_VIEWED , $employeeName, $employeeNames, $date),
                'status_vendor_no_response'         => $this->getEmployerWiseEmployeeStatusCount($employerName, 'pv_status',$submissionModel::STATUS_NO_RESPONSE_FROM_PV , $employeeName, $employeeNames, $date),
                'status_vendor_rejected_by_pv'      => $this->getEmployerWiseEmployeeStatusCount($employerName, 'pv_status',$submissionModel::STATUS_REJECTED_BY_PV , $employeeName, $employeeNames, $date),
                'status_rejected_by_client'         => $this->getEmployerWiseEmployeeStatusCount($employerName, 'pv_status',$submissionModel::STATUS_REJECTED_BY_END_CLIENT , $employeeName, $employeeNames, $date),
                'status_submitted_to_end_client'    => $this->getEmployerWiseEmployeeStatusCount($employerName, 'pv_status',$submissionModel::STATUS_SUBMITTED_TO_END_CLIENT , $employeeName, $employeeNames, $date),
                'status_position_closed'            => $this->getEmployerWiseEmployeeStatusCount($employerName, 'pv_status',$submissionModel::STATUS_POSITION_CLOSED , $employeeName, $employeeNames, $date),
                'status_scheduled'                  => $this->getEmployerWiseEmployeeClientStatusCount($employerName,$interviewModel::STATUS_SCHEDULED, $employeeName, $employeeNames, $date),
                'status_re_scheduled'               => $this->getEmployerWiseEmployeeClientStatusCount($employerName,$interviewModel::STATUS_RE_SCHEDULED, $employeeName, $employeeNames, $date),
                'status_selected_for_another_round' => $this->getEmployerWiseEmployeeClientStatusCount($employerName,$interviewModel::STATUS_SELECTED_FOR_NEXT_ROUND, $employeeName, $employeeNames, $date),
                'status_waiting_feedback'           => $this->getEmployerWiseEmployeeClientStatusCount($employerName,$interviewModel::STATUS_WAITING_FEEDBACK, $employeeName, $employeeNames, $date),
                'status_position_confirm'           => $this->getEmployerWiseEmployeeClientStatusCount($employerName,$interviewModel::STATUS_CONFIRMED_POSITION, $employeeName, $employeeNames, $date),
                'status_client_rejected'            => $this->getEmployerWiseEmployeeClientStatusCount($employerName,$interviewModel::STATUS_REJECTED, $employeeName, $employeeNames, $date),
                'status_backout'                    => $this->getEmployerWiseEmployeeClientStatusCount($employerName,$interviewModel::STATUS_BACKOUT, $employeeName, $employeeNames, $date),
                'client_status_total'               => $this->getEmployerWiseEmployeeClientStatusCount($employerName,'all', $employeeName, $employeeNames, $date),
                'category_wise_count'               => $this->getEmployerWiseEmployeeCategories($employerName, $employeeName, $employeeNames, $date),
                'bdm_wise_count'                    => $this->getEmployerWiseEmployeeRecruiter($employerName, $employeeName, $employeeNames, $date),
            ];

            if($this->getIsEmptyPOCRow()){
                $employerNameKey = $this->getKey($employerName);
                $this->setEmptyPOCRows($employerNameKey.'_'.$employeeName);
            }

            $employeeNameWiseData['data'][$employeeName] = $pocData;
            $employeeNameWiseData['employee_uni_sub_count'] = $this->getEmployeeWiseUniSubmissionCount($employerName, $employeeName);
        }
        return $employeeNameWiseData;
    }

    public function getWhoAddedEmployeeName($employerName, $employeeName, $allEmployeeName)
    {
        $employerNameKey = $this->getKey($employerName);
        if(!$this->_employeeWiseWhoAddedUserName || !isset($this->_employeeWiseWhoAddedUserName[$employerNameKey])){
            $this->_employeeWiseWhoAddedUserName[$employerNameKey] = Admin::select('added_by', 'employee_name')
                ->whereIn('employee_name', $allEmployeeName)
                ->where('name', $employerName)
                ->groupBy('employee_name')->pluck('added_by', 'employee_name');
        }

        if(isset($this->_employeeWiseWhoAddedUserName[$employerNameKey][$employeeName])){
            $userId =  $this->_employeeWiseWhoAddedUserName[$employerNameKey][$employeeName];
            return  Admin::getUserNameBasedOnId($userId);
        }

        return '';
    }

    public function getEmployeeHideColumns(): array
    {
        return [
            'employee_email',
            'employee_phone'
        ];
    }

    public function getTotalShowEmployeeColumns(): array
    {
        return [
            'who_added',
            'employer_company_name',
            'employer_company_total_sub',
            'employee_name',
        ];
    }

    public function getEmployerWiseUniSubmissionCount()
    {
        return Submission::select('employer_name', \DB::raw("COUNT(DISTINCT id) as count"))
            ->where('id' ,\DB::raw('candidate_id'))
            ->groupBy('employer_name')
            ->pluck('count', 'employer_name')->toArray();
    }

    public function getEmployeeWiseUniSubmissionCount($employerName, $employeeName)
    {
        return  Submission::where('id' ,\DB::raw('candidate_id'))
            ->groupBy('employee_name')
            ->where('employer_name', $employerName)
            ->where('employee_name', $employeeName)
            ->count();
    }
}
