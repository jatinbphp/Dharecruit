<?php
namespace App\Traits;

use App\Models\Admin;
use App\Models\AssignToRecruiter;
use App\Models\Interview;
use App\Models\Requirement;
use App\Models\Submission;

trait SubmissionTrait{

    use CommonTrait;

    protected $_userIdWiseRecruiterAllotadRequirementCount = [];
    protected $_userIdWiseRecruiterServedRequirementCount = [];
    protected $_userIdWiseRecruiterUnServedRequirementCount = [];

    protected $_userIdWiseRecruiterSubmissionSentCount = [];
    protected $_userIdWiseRecruiterStatusCount = [];
    protected $_userIdWiseRecruiterClientStatusCount = [];
    protected $_userIdWisetotalReceivedSubmissionCount = [];
    protected $_userIdWiseTotalEmployeeCount = [];
    protected $_userIdWiseTotalEmployerCount = [];
    protected $_userIdWiseTotalNewEmployeeCount = [];
    protected $_userIdWiseTotalNewEmployerCount = [];

    public function getRecruiterUserHeadingData(): array
    {
        return [
            'heading_recruiter'          =>'Recruiter',
            'heading_total_employer'     => "Total Sales Comp",
            'heading_new_employer'       => "New Sales Comp",
            'heading_total_employee'     => "Total Bench POC",
            'heading_new_employee'       => "New Bench POC",
            'heading_alloted'            =>'Allotad',
            'heading_served'             =>'Served',
            'heading_unserved'           =>'Unserved',
            'heading_servable_per'       =>'Servable%',
            'heading_sub_sent'           =>'Sub Sent',
            'heading_uniq_sub'           =>'Uniq Sub',
            'heading_accept'             => 'Accept',
            'heading_rejected'           => 'Rejected',
            'heading_pending'            => 'Pending',
            'heading_un_viewed'          => 'Unviewed',
            'heading_vendor_no_responce' => 'Vendor No Res.',
            'heading_vendor_rejected'    => 'Vendor Rejected',
            'heading_client_rejected'    => 'Client Rejected',
            'heading_sub_to_end_client'  => 'Sub To End Client',
            'heading_position_closed'    => 'position Closed',
            'heading_re_scheduled'       => 'Re Scheduled',
            'heading_another_round'      => 'Another Round',
            'heading_waiting_feedback'   => 'Waiting FeedBack',
            'heading_confirmed'          => 'Conformed',
            'heading_rejected_by_client' => 'Rejected By Client',
            'heading_backout'            => 'Backout',
        ];
    }

    public function getRecruiterTimeFrameHeadingData(): array
    {
        return [
            'heading_time_frame'         => "Time Frame",
            'heading_total_employer'     => "Total Sales Comp",
            'heading_new_employer'       => "New Sales Comp",
            'heading_total_employee'     => "Total Bench POC",
            'heading_new_employee'       => "New Bench POC",
            'heading_alloted'            =>'Allotad',
            'heading_served'             =>'Served',
            'heading_unserved'           =>'Unserved',
            'heading_servable_per'       =>'Servable%',
            'heading_sub_sent'           =>'Sub Sent',
            'heading_uniq_sub'           =>'Uniq Sub',
            'heading_accept'             => 'Accept',
            'heading_rejected'           => 'Rejected',
            'heading_pending'            => 'Pending',
            'heading_un_viewed'          => 'Unviewed',
            'heading_vendor_no_responce' => 'Vendor No Res.',
            'heading_vendor_rejected'    => 'Vendor Rejected',
            'heading_client_rejected'    => 'Client Rejected',
            'heading_sub_to_end_client'  => 'Sub To End Client',
            'heading_position_closed'    => 'position Closed',
            'heading_re_scheduled'       => 'Re Scheduled',
            'heading_another_round'      => 'Another Round',
            'heading_waiting_feedback'   => 'Waiting FeedBack',
            'heading_confirmed'          => 'Conformed',
            'heading_rejected_by_client' => 'Rejected By Client',
            'heading_backout'            => 'Backout',
        ];
    }

    public function getDateWiseRecruiterData($type, $userId, $recruiters, $request, $isCompare = 0): array
    {
        $headingType = ($isCompare) ? Admin::getUserNameBasedOnId($userId) : $this->formatString($type);
        $date = $this->getDate($type, $request);
        if(!$date || !count($date) || !isset($date['from']) || !isset($date['to'])){
            return  [];
        }

        $submissionModel = new Submission();
        $interviewModel  = new Interview();

        $totalAllotedRequirements  = $this->getTotalRecruiterAllotedRequirementCount($date, $userId, $recruiters, $type);
        $servedRequirements        = $this->getTotalRecruiterServedRequirementCount($date, $userId, $recruiters, $type);
        $servablPer                = $this->getPercentage($servedRequirements, $totalAllotedRequirements);

        return [
            'heading_type'                   => $headingType,
            'total_employer'                 => $this->getUserIdWiseTotalEmployer($date, $userId, $recruiters, $type),
            'new_employer'                   => $this->getuserIdWiseTotalNewEmployer($date, $userId, $recruiters, $type),
            'total_employee'                 => $this->getUserIdWiseTotalEmployee($date, $userId, $recruiters, $type),
            'new_employee'                   => $this->getuserIdWiseTotalNewEmployee($date, $userId, $recruiters, $type),
            'alloted'                        => $totalAllotedRequirements,
            'served'                         => $servedRequirements,
            'unserved'                       => $this->getTotalRecruiterUnServedRequirementCount($date, $userId, $recruiters, $type),
            'servable_per'                   => $servablPer,
            'submission_sent'                => $this->getTotalRecruiterSubmissionSentCount($date, $userId, $recruiters, $type),
            'unique_submission_sent'         => $this->getTotalRecruiterSubmissionSentCount($date, $userId, $recruiters, $type, 1   ),
            'bdm_accept'                     => $this->getTotalRecruiterStatusCount('status', $submissionModel::STATUS_ACCEPT, $date, $userId, $recruiters, $type),
            'bdm_rejected'                   => $this->getTotalRecruiterStatusCount('status', $submissionModel::STATUS_REJECTED, $date, $userId, $recruiters, $type),
            'bdm_pending'                    => $this->getTotalRecruiterStatusCount('status', $submissionModel::STATUS_PENDING, $date, $userId, $recruiters, $type),
            'bdm_unviewed'                   => $this->getTotalRecruiterStatusCount('status', $submissionModel::STATUS_NOT_VIEWED, $date, $userId, $recruiters, $type),
            'vendor_no_responce'             => $this->getTotalRecruiterStatusCount('pv_status', $submissionModel::STATUS_NO_RESPONSE_FROM_PV, $date, $userId, $recruiters, $type),
            'vendor_rejected_by_pv'          => $this->getTotalRecruiterStatusCount('pv_status', $submissionModel::STATUS_REJECTED_BY_PV, $date, $userId, $recruiters, $type),
            'vendor_rejected_by_client'      => $this->getTotalRecruiterStatusCount('pv_status', $submissionModel::STATUS_REJECTED_BY_END_CLIENT, $date, $userId, $recruiters, $type),
            'vendor_submitted_to_end_client' => $this->getTotalRecruiterStatusCount('pv_status', $submissionModel::STATUS_SUBMITTED_TO_END_CLIENT, $date, $userId, $recruiters, $type),
            'vendor_position_closed'         => $this->getTotalRecruiterStatusCount('pv_status', $submissionModel::STATUS_POSITION_CLOSED, $date, $userId, $recruiters, $type),
            'client_rescheduled'             => $this->getTotalRecruiterClientStatusCount($interviewModel::STATUS_RE_SCHEDULED, $date, $userId, $recruiters, $type),
            'client_selected_for_next_round' => $this->getTotalRecruiterClientStatusCount($interviewModel::STATUS_SELECTED_FOR_NEXT_ROUND, $date, $userId, $recruiters, $type),
            'client_waiting_feedback'        => $this->getTotalRecruiterClientStatusCount($interviewModel::STATUS_WAITING_FEEDBACK, $date, $userId, $recruiters, $type),
            'client_confirmed_position'      => $this->getTotalRecruiterClientStatusCount($interviewModel::STATUS_CONFIRMED_POSITION, $date, $userId, $recruiters, $type),
            'client_rejected'                => $this->getTotalRecruiterClientStatusCount($interviewModel::STATUS_REJECTED, $date, $userId, $recruiters, $type),
            'client_backout'                 => $this->getTotalRecruiterClientStatusCount($interviewModel::STATUS_BACKOUT, $date, $userId, $recruiters, $type),
        ];
    }

    public function getTotalRecruiterAllotedRequirementCount($date, $userId, $recruiters, $type): int
    {

        if(!$this->_userIdWiseRecruiterAllotadRequirementCount || !isset($this->_userIdWiseRecruiterAllotadRequirementCount[$type])){
            $this->_userIdWiseRecruiterAllotadRequirementCount[$type] = AssignToRecruiter::whereIn('recruiter_id', $recruiters)
                ->whereBetween('created_at', $date)
                ->groupBy('recruiter_id')
                ->selectRaw('recruiter_id, COUNT(id) as count')
                ->pluck('count', 'recruiter_id')
                ->toArray();
        }

        if(isset($this->_userIdWiseRecruiterAllotadRequirementCount[$type][$userId])){
            return $this->_userIdWiseRecruiterAllotadRequirementCount[$type][$userId];
        }

        return 0;
    }

    public function getTotalRecruiterServedRequirementCount($date, $userId, $recruiters, $type): int
    {
        if(!$this->_userIdWiseRecruiterServedRequirementCount || !isset($this->_userIdWiseRecruiterServedRequirementCount[$type])){
            $this->_userIdWiseRecruiterServedRequirementCount[$type] = Submission::select('user_id', \DB::raw('COUNT(DISTINCT requirement_id) as count'))
                ->whereIn('user_id', $recruiters)
                ->whereBetween('created_at', $date)
                ->groupBy('user_id')
                ->pluck('count', 'user_id')
                ->toArray();
        }

        if(isset($this->_userIdWiseRecruiterServedRequirementCount[$type][$userId])){
            return $this->_userIdWiseRecruiterServedRequirementCount[$type][$userId];
        }

        return 0;
    }

    public function getTotalRecruiterUnServedRequirementCount($date, $userId, $recruiters, $type): int
    {
        if(!$this->_userIdWiseRecruiterUnServedRequirementCount || !isset($this->_userIdWiseRecruiterUnServedRequirementCount[$type])){
            $this->_userIdWiseRecruiterUnServedRequirementCount[$type] = AssignToRecruiter::select('recruiter_id')
                ->selectRaw('COUNT(DISTINCT requirement_id) as count')
                ->whereNotIn('requirement_id', function ($query) {
                    $query->select('requirement_id')
                        ->from('submissions')
                        ->whereRaw('submissions.user_id = assign_to_recruiters.recruiter_id');
                })
                ->whereIn('recruiter_id', $recruiters)
                ->whereBetween('assign_to_recruiters.created_at', $date)
                ->groupBy('assign_to_recruiters.recruiter_id')
                ->pluck('count', 'assign_to_recruiters.recruiter_id')
                ->toArray();
        }

        if(isset($this->_userIdWiseRecruiterUnServedRequirementCount[$type][$userId])){
            return $this->_userIdWiseRecruiterUnServedRequirementCount[$type][$userId];
        }

        return 0;
    }

    public function getTotalRecruiterSubmissionSentCount($date, $userId, $recruiters, $type, $isUnique = 0): int
    {
        if(!$this->_userIdWiseRecruiterSubmissionSentCount || !isset($this->_userIdWiseRecruiterSubmissionSentCount[$type]) || !isset($this->_userIdWiseRecruiterSubmissionSentCount[$type][$isUnique])){
            $collection = Submission::select('user_id')
                ->whereIn('user_id', $recruiters)
                ->whereBetween('created_at', $date);
            if($isUnique){
                $collection->where('id' ,\DB::raw('candidate_id'));
            }
            $collection->selectRaw(\DB::raw('COUNT(id) as count'))
                ->groupBy('user_id');

            $this->_userIdWiseRecruiterSubmissionSentCount[$type][$isUnique] = $collection->pluck('count', 'user_id')->toArray();
        }

        if(isset($this->_userIdWiseRecruiterSubmissionSentCount[$type][$isUnique][$userId])){
            return $this->_userIdWiseRecruiterSubmissionSentCount[$type][$isUnique][$userId];
        }

        return 0;
    }

    public function getTotalRecruiterStatusCount($filedName, $status, $date, $userId, $recruiters, $type): int
    {
        if(!$this->_userIdWiseRecruiterStatusCount || !isset($this->_userIdWiseRecruiterStatusCount[$type]) || !isset($this->_userIdWiseRecruiterStatusCount[$type][$status])){
            $collection = Submission::select('user_id')
                ->where($filedName, $status)
                ->whereIn('user_id', $recruiters)
                ->whereBetween('updated_at', $date)
                ->selectRaw(\DB::raw('COUNT(id) as count'))
                ->groupBy('user_id');

            $this->_userIdWiseRecruiterStatusCount[$type][$status] = $collection->pluck('count', 'user_id')->toArray();
        }

        if(isset($this->_userIdWiseRecruiterStatusCount[$type][$status][$userId])){
            return  $this->_userIdWiseRecruiterStatusCount[$type][$status][$userId];
        }

        return 0;
    }

    public function getTotalRecruiterClientStatusCount($status, $date, $userId, $recruiters, $type): int
    {
        if(!$this->_userIdWiseRecruiterClientStatusCount || !isset($this->_userIdWiseRecruiterClientStatusCount[$type]) || !isset($this->_userIdWiseRecruiterClientStatusCount[$type][$status])){
            $this->_userIdWiseRecruiterClientStatusCount[$type][$status] = Submission::leftJoin('interviews', 'submissions.id', '=', 'interviews.submission_id')
                ->where('interviews.status', $status)
                ->whereBetween('interviews.updated_at', $date)
                ->whereIn('submissions.user_id', $recruiters)
                ->groupBy('submissions.user_id')
                ->selectRaw('submissions.user_id, COUNT(interviews.id) as count')
                ->pluck('count', 'user_id')
                ->toArray();
        }

        if(isset($this->_userIdWiseRecruiterClientStatusCount[$type][$status][$userId])){
            return $this->_userIdWiseRecruiterClientStatusCount[$type][$status][$userId];
        }

        return 0;
    }

    public function getUserIdWiseTotalEmployee($date, $userId, $recruiters, $type)
    {
        if(!$this->_userIdWiseTotalEmployeeCount || !isset($this->_userIdWiseTotalEmployeeCount[$type])){
            $this->_userIdWiseTotalEmployeeCount[$type] = Submission::select('user_id', \DB::raw('COUNT(DISTINCT(CONCAT(employee_name, "-", employer_name))) as count'))
                ->whereIn('user_id', $recruiters)
                ->whereBetween('created_at', $date)
                ->groupBy('user_id')
                ->pluck('count', 'user_id')
                ->toArray();
        }

        if(isset($this->_userIdWiseTotalEmployeeCount[$type][$userId])){
            return $this->_userIdWiseTotalEmployeeCount[$type][$userId];
        }

        return 0;
    }

    public function getUserIdWiseTotalEmployer($date, $userId, $recruiters, $type)
    {
        if(!$this->_userIdWiseTotalEmployerCount || !isset($this->_userIdWiseTotalEmployerCount[$type])){
            $this->_userIdWiseTotalEmployerCount[$type] = Submission::select('user_id', \DB::raw('COUNT(DISTINCT LOWER(employer_name)) as count'))
                ->whereIn('user_id', $recruiters)
                ->whereBetween('created_at', $date)
                ->groupBy('user_id')
                ->pluck('count', 'user_id')
                ->toArray();
        }

        if(isset($this->_userIdWiseTotalEmployerCount[$type][$userId])){
            return $this->_userIdWiseTotalEmployerCount[$type][$userId];
        }

        return 0;
    }
    public function getuserIdWiseTotalNewEmployer($date, $userId, $recruiters, $type)
    {
        if(!$this->_userIdWiseTotalNewEmployerCount || !isset($this->_userIdWiseTotalNewEmployerCount[$type])){
            $this->_userIdWiseTotalNewEmployerCount[$type] = Admin::select('added_by', \DB::raw('COUNT(DISTINCT LOWER(name)) as count'))
                ->whereIn('added_by', $recruiters)
                ->where('role', 'employee')
                ->whereBetween('created_at', $date)
                ->groupBy('added_by')
                ->pluck('count', 'added_by')
                ->toArray();
        }

        if(isset($this->_userIdWiseTotalNewEmployerCount[$type][$userId])){
            return $this->_userIdWiseTotalNewEmployerCount[$type][$userId];
        }

        return 0;
    }
    public function getuserIdWiseTotalNewEmployee($date, $userId, $recruiters, $type)
    {
        if(!$this->_userIdWiseTotalNewEmployeeCount || !isset($this->_userIdWiseTotalNewEmployeeCount[$type])){
            $this->_userIdWiseTotalNewEmployeeCount[$type] = Admin::select('added_by', \DB::raw('COUNT(DISTINCT (CONCAT(employee_name, "-", name))) as count'))
                ->whereIn('added_by', $recruiters)
                ->where('role', 'employee')
                ->whereBetween('created_at', $date)
                ->groupBy('added_by')
                ->pluck('count', 'added_by')
                ->toArray();
        }

        if(isset($this->_userIdWiseTotalNewEmployeeCount[$type][$userId])){
            return $this->_userIdWiseTotalNewEmployeeCount[$type][$userId];
        }

        return 0;
    }
}
