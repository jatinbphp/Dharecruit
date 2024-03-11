<?php
namespace App\Traits;

use App\Models\Admin;
use App\Models\AssignToRecruiter;
use App\Models\Interview;
use App\Models\Requirement;
use App\Models\Submission;
use App\Models\TeamMember;

trait SubmissionTrait{

    use CommonTrait;

    protected $_userIdWiseRecruiterAllotadRequirementCount = [];
    protected $_userIdWiseRecruiterServedRequirementCount = [];
    protected $_userIdWiseRecruiterUnServedRequirementCount = [];

    protected $_userIdWiseRecruiterSubmissionSentCount = [];
    protected $_userIdWiseRecruiterStatusCount = [];
    protected $_userIdWiseRecruiterClientStatusCount = [];
    protected $_userIdWiseRecruiterNewInterviewCount = [];
    protected $_userIdWisetotalReceivedSubmissionCount = [];
    protected $_userIdWiseTotalEmployeeCount = [];
    protected $_userIdWiseTotalEmployerCount = [];
    protected $_userIdWiseTotalNewEmployeeCount = [];
    protected $_userIdWiseTotalNewEmployerCount = [];
    protected $_userIdWiseRecruiterTotalAvgTime = [];

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
            'heading_avg_time'           => 'Avg Time (in minutes)',
            'heading_accept'             => 'Accept',
            'heading_accept_percentage'  => 'Accept %',
            'heading_rejected'           => 'Rejected',
            'heading_pending'            => 'Pending',
            'heading_un_viewed'          => 'Unviewed',
            'heading_sub_to_end_client'  => 'Sub To End Client',
            'heading_sub_to_end_client_per'  => 'Sub To End Client %',
            'heading_vendor_no_responce' => 'Vendor No Res.',
            'heading_vendor_rejected'    => 'Vendor Rejected',
            'heading_client_rejected'    => 'Client Rejected',
            'heading_position_closed'    => 'position Closed',
            'heading_interview_count'    => 'Interview Count (Time Frame)',
            'heading_interview_count_submission_frame'    => 'Interview Count (Submission Frame)',
            'heading_scheduled'          => 'Scheduled',
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
            'heading_avg_time'           => 'Avg Time (in minutes)',
            'heading_accept'             => 'Accept',
            'heading_accept_percentage'  => 'Accept %',
            'heading_rejected'           => 'Rejected',
            'heading_pending'            => 'Pending',
            'heading_un_viewed'          => 'Unviewed',
            'heading_sub_to_end_client'  => 'Sub To End Client',
            'heading_sub_to_end_client_per'  => 'Sub To End Client %',
            'heading_vendor_no_responce' => 'Vendor No Res.',
            'heading_vendor_rejected'    => 'Vendor Rejected',
            'heading_client_rejected'    => 'Client Rejected',
            'heading_position_closed'    => 'position Closed',
            'heading_interview_count'    => 'Interview Count (Time Frame)',
            'heading_interview_count_submission_frame'    => 'Interview Count (Submission Frame)',
            'heading_scheduled'          => 'Scheduled',
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
        $submissions               = $this->getTotalRecruiterSubmissionSentCount($date, $userId, $recruiters, $type);
        $bdmAcceptCount            = $this->getTotalRecruiterStatusCount('status', $submissionModel::STATUS_ACCEPT, $date, $userId, $recruiters, $type, $request->frame_type);
        $subToEndClientCount       = $this->getTotalRecruiterStatusCount('pv_status', $submissionModel::STATUS_SUBMITTED_TO_END_CLIENT, $date, $userId, $recruiters, $type, $request->frame_type);

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
            'submission_sent'                => $submissions,
            'unique_submission_sent'         => $this->getTotalRecruiterSubmissionSentCount($date, $userId, $recruiters, $type, 1   ),
            'avg_time'                       => $this->getRecAvgTime($date, $userId, $recruiters, $type),
            'bdm_accept'                     => $bdmAcceptCount,
            'bdm_accept_percentage'          => $this->getStatusPercentage($bdmAcceptCount, $submissions),
            'bdm_rejected'                   => $this->getTotalRecruiterStatusCount('status', $submissionModel::STATUS_REJECTED, $date, $userId, $recruiters, $type, $request->frame_type),
            'bdm_pending'                    => $this->getTotalRecruiterStatusCount('status', $submissionModel::STATUS_PENDING, $date, $userId, $recruiters, $type, $request->frame_type),
            'bdm_unviewed'                   => $this->getTotalRecruiterStatusCount('status', $submissionModel::STATUS_NOT_VIEWED, $date, $userId, $recruiters, $type, $request->frame_type),
            'vendor_submitted_to_end_client' => $subToEndClientCount,
            'vendor_submitted_to_end_client_percentage' => $this->getStatusPercentage($subToEndClientCount, $submissions),
            'vendor_no_responce'             => $this->getTotalRecruiterStatusCount('pv_status', $submissionModel::STATUS_NO_RESPONSE_FROM_PV, $date, $userId, $recruiters, $type, $request->frame_type),
            'vendor_rejected_by_pv'          => $this->getTotalRecruiterStatusCount('pv_status', $submissionModel::STATUS_REJECTED_BY_PV, $date, $userId, $recruiters, $type, $request->frame_type),
            'vendor_rejected_by_client'      => $this->getTotalRecruiterStatusCount('pv_status', $submissionModel::STATUS_REJECTED_BY_END_CLIENT, $date, $userId, $recruiters, $type, $request->frame_type),
            'vendor_position_closed'         => $this->getTotalRecruiterStatusCount('pv_status', $submissionModel::STATUS_POSITION_CLOSED, $date, $userId, $recruiters, $type, $request->frame_type),
            'interview_count'                => $this->getTotalRecruiterNewInterviewCount($date, $userId, $recruiters, $type),
            'interview_count_submission_frame' => $this->getTotalRecruiterNewInterviewCount($date, $userId, $recruiters, $type, 1),
            'client_scheduled'               => $this->getTotalRecruiterClientStatusCount($interviewModel::STATUS_SCHEDULED, $date, $userId, $recruiters, $type, $request->frame_type),
            'client_rescheduled'             => $this->getTotalRecruiterClientStatusCount($interviewModel::STATUS_RE_SCHEDULED, $date, $userId, $recruiters, $type, $request->frame_type),
            'client_selected_for_next_round' => $this->getTotalRecruiterClientStatusCount($interviewModel::STATUS_SELECTED_FOR_NEXT_ROUND, $date, $userId, $recruiters, $type, $request->frame_type),
            'client_waiting_feedback'        => $this->getTotalRecruiterClientStatusCount($interviewModel::STATUS_WAITING_FEEDBACK, $date, $userId, $recruiters, $type, $request->frame_type),
            'client_confirmed_position'      => $this->getTotalRecruiterClientStatusCount($interviewModel::STATUS_CONFIRMED_POSITION, $date, $userId, $recruiters, $type, $request->frame_type),
            'client_rejected'                => $this->getTotalRecruiterClientStatusCount($interviewModel::STATUS_REJECTED, $date, $userId, $recruiters, $type, $request->frame_type),
            'client_backout'                 => $this->getTotalRecruiterClientStatusCount($interviewModel::STATUS_BACKOUT, $date, $userId, $recruiters, $type, $request->frame_type),
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

    public function getTotalRecruiterStatusCount($filedName, $status, $date, $userId, $recruiters, $type, $frameType): int
    {
        $dateFiled = 'submissions.bdm_status_updated_at';
        if($filedName == 'pv_status'){
            $dateFiled = 'submissions.pv_status_updated_at';
        }
        if(!$this->_userIdWiseRecruiterStatusCount || !isset($this->_userIdWiseRecruiterStatusCount[$type]) || !isset($this->_userIdWiseRecruiterStatusCount[$type][$status])){
            $collection = Submission::select('user_id')
                ->where($filedName, $status)
                ->whereIn('user_id', $recruiters);

            if($frameType == 'submission_frame'){
                $collection->whereBetween('submissions.created_at', $date);
            } else {
                $collection->whereBetween($dateFiled, $date);
            }

            $collection->selectRaw(\DB::raw('COUNT(id) as count'))
                ->groupBy('user_id');

            $this->_userIdWiseRecruiterStatusCount[$type][$status] = $collection->pluck('count', 'user_id')->toArray();
        }

        if(isset($this->_userIdWiseRecruiterStatusCount[$type][$status][$userId])){
            return  $this->_userIdWiseRecruiterStatusCount[$type][$status][$userId];
        }

        return 0;
    }

    public function getTotalRecruiterClientStatusCount($status, $date, $userId, $recruiters, $type, $frameType): int
    {
        if(!$this->_userIdWiseRecruiterClientStatusCount || !isset($this->_userIdWiseRecruiterClientStatusCount[$type]) || !isset($this->_userIdWiseRecruiterClientStatusCount[$type][$status])){
            $collection = Submission::leftJoin('interviews', 'submissions.id', '=', 'interviews.submission_id')
                ->where('interviews.status', $status)
                ->whereIn('submissions.user_id', $recruiters);

            if($frameType == 'submission_frame'){
                $collection->whereBetween('submissions.created_at', $date);
            } else {
                $collection->whereBetween('submissions.interview_status_updated_at', $date);
            }

            $this->_userIdWiseRecruiterClientStatusCount[$type][$status] = $collection->groupBy('submissions.user_id')
                ->selectRaw('submissions.user_id, COUNT(interviews.id) as count')
                ->pluck('count', 'user_id')
                ->toArray();
        }

        if(isset($this->_userIdWiseRecruiterClientStatusCount[$type][$status][$userId])){
            return $this->_userIdWiseRecruiterClientStatusCount[$type][$status][$userId];
        }

        return 0;
    }

    public function getTotalRecruiterNewInterviewCount($date, $userId, $recruiters, $type, $isSubmissionFrame = 0): int
    {
        if(!$this->_userIdWiseRecruiterNewInterviewCount || !isset($this->_userIdWiseRecruiterNewInterviewCount[$isSubmissionFrame]) || !isset($this->_userIdWiseRecruiterNewInterviewCount[$isSubmissionFrame][$type])){
            $this->_userIdWiseRecruiterNewInterviewCount[$isSubmissionFrame][$type] = Submission::leftJoin('interviews', 'submissions.id', '=', 'interviews.submission_id')
                ->whereBetween(($isSubmissionFrame) ? 'submissions.created_at' : 'interviews.created_at' , $date)
                ->whereIn('submissions.user_id', $recruiters)
                ->groupBy('submissions.user_id')
                ->selectRaw('submissions.user_id, COUNT(interviews.id) as count')
                ->pluck('count', 'user_id')
                ->toArray();
        }

        if(isset($this->_userIdWiseRecruiterNewInterviewCount[$isSubmissionFrame][$type][$userId])){
            return $this->_userIdWiseRecruiterNewInterviewCount[$isSubmissionFrame][$type][$userId];
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

    public function getRecruiterTeamData($teams)
    {
        if(empty($teams)){
            return [];
        }

        $teamWiseData = [];
        $heading = [
            'Team',
            'Size',
            'Avg Interview/Member',
        ];
        $teamWiseData['heading'] = $heading;
        $teamIdWuswName = getTeamIdWiseTeamName();

        foreach ($teams as $team){

            $allTeamUsers = TeamMember::where('team_id', $team)->pluck('member_id')->toArray();
            $allTeamUsers[] = $team;

            $teamData = [];
            $teamData['name'] = isset($teamIdWuswName[$team]) ? $teamIdWuswName[$team] : '';
            $teamData['team_size'] = count($allTeamUsers);
            $interviewCount = $this->getRecruiterInterviewCounts($allTeamUsers);
            $teamData['percentage'] = round($interviewCount / (count($allTeamUsers)) ?? 1,2);

            $teamWiseData['team_wise_data'][$team] = $teamData;
        }

        return $teamWiseData;
    }

    public function getRecruiterInterviewCounts($allTeamUsers)
    {
        if(empty($allTeamUsers)){
            return 0;
        }

        $counts = Submission::leftJoin('interviews', 'submissions.id', '=', 'interviews.submission_id')
            ->whereIn('submissions.user_id', $allTeamUsers)
            ->groupBy('submissions.user_id')
            ->selectRaw('submissions.user_id, COUNT(interviews.id) as count')
            ->pluck('count', 'user_id')
            ->toArray();

        return array_sum($counts);
    }

    public function getRecAvgTime($date, $userId, $recruiters, $type): int
    {
        if(!$this->_userIdWiseRecruiterTotalAvgTime || !isset($this->_userIdWiseRecruiterTotalAvgTime[$type])){
            $usersData = Submission::selectRaw('submissions.user_id, SUM(TIMESTAMPDIFF(MINUTE, requirements.created_at, submissions.created_at)) AS total_time, COUNT(*) AS count')
                ->join('requirements', 'requirements.id', '=', 'submissions.requirement_id')
                ->whereIn('submissions.user_id', $recruiters)
                ->whereBetween('submissions.created_at', $date)
                ->groupBy('submissions.user_id')
                ->get();

            $result = [];

            foreach ($usersData as $userData) {
                $result[$userData->user_id] = round(($userData->total_time / (($userData->count) ? $userData->count : 1)), 2);
            }

            $this->_userIdWiseRecruiterTotalAvgTime[$type] = $result;
        }

        if(isset($this->_userIdWiseRecruiterTotalAvgTime[$type][$userId])){
            return $this->_userIdWiseRecruiterTotalAvgTime[$type][$userId];
        }

        return 0;
    }
}
