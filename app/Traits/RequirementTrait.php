<?php
namespace App\Traits;

use App\Models\Interview;
use App\Models\Requirement;
use App\Models\Submission;

trait RequirementTrait
{
    use CommonTrait;
    protected $_userIdWiseTotalRequirementCount = [];
    protected $_userIdWiseServedRequirementCount = [];
    protected $_userIdWiseUnServedRequirementCount = [];
    protected $_userIdWiseStatusCount = [];
    protected $_userIdWiseClientStatusCount = [];
    protected $_userIdWisetotalReceivedSubmissionCount = [];

    public function getBdmUserHeadingData(): array
    {
        return [
            'BDM',
            'Total',
            'Unique',
            'Served',
            'Unserved',
            'Servable%',
            'Sub Rec',
            'Accept',
            'Rejected',
            'Pending',
            'Unviewed',
            'Vendor No Res.',
            'Vendor Rejected',
            'Client Rejected',
            'Sub To End Client',
            'position Closed',
            'Re Scheduled',
            'Another Round',
            'Waiting FeedBack',
            'Conformed',
            'Rejected By Client',
            'Backout',
        ];
    }

    public function getDateWiseBdmData($type, $userId, $bdms, $request): array
    {
        $headingType = $this->formatString($type);
        $date = $this->getDate($type, $request);
        if(!$date || !count($date) || !isset($date['from']) || !isset($date['to'])){
            return  [];
        }

        $submissionModel = new Submission();
        $interviewModel  = new Interview();

        $totalRequirements  = $this->getTotalRequirementCount($date, $bdms, $userId, $type);
        $servedRequirements = $this->getTotalServedRequirementsCount($date, $bdms, $userId, $type);
        $servablPer         = $this->getPercentage($servedRequirements, $totalRequirements);

        return [
            'heading_type'                   => $headingType,
            'total_req'                      => $totalRequirements,
            'total_uni_req'                  => $this->getTotalRequirementCount($date, $bdms, $userId, $type, 1),
            'served'                         => $servedRequirements,
            'unserved'                       => $this->getTotalUnServedRequirementsCount($date, $bdms, $userId, $type),
            'servable_per'                   => $servablPer,
            'submission_received'            => $this->getTotalReceivedSubmissionCount($date, $bdms, $userId, $type),
            'bdm_accept'                     => $this->getTotalStatusCount('status', $submissionModel::STATUS_ACCEPT, $date, $bdms, $userId, $type),
            'bdm_rejected'                   => $this->getTotalStatusCount('status', $submissionModel::STATUS_REJECTED, $date, $bdms, $userId, $type),
            'bdm_unviewed'                   => $this->getTotalStatusCount('status', $submissionModel::STATUS_NOT_VIEWED, $date, $bdms, $userId, $type),
            'bdm_pending'                    => $this->getTotalStatusCount('status', $submissionModel::STATUS_PENDING, $date, $bdms, $userId, $type),
            'vendor_no_responce'             => $this->getTotalStatusCount('pv_status', $submissionModel::STATUS_NO_RESPONSE_FROM_PV, $date, $bdms, $userId, $type),
            'vendor_rejected_by_pv'          => $this->getTotalStatusCount('pv_status', $submissionModel::STATUS_REJECTED_BY_PV, $date, $bdms, $userId, $type),
            'vendor_rejected_by_client'      => $this->getTotalStatusCount('pv_status', $submissionModel::STATUS_REJECTED_BY_END_CLIENT, $date, $bdms, $userId, $type),
            'vendor_submitted_to_end_client' => $this->getTotalStatusCount('pv_status', $submissionModel::STATUS_SUBMITTED_TO_END_CLIENT, $date, $bdms, $userId, $type),
            'vendor_position_closed'         => $this->getTotalStatusCount('pv_status', $submissionModel::STATUS_POSITION_CLOSED, $date, $bdms, $userId, $type),
            'client_rescheduled'             => $this->getTotalClientStatusCount($interviewModel::STATUS_RE_SCHEDULED, $date, $bdms, $userId, $type),
            'client_selected_for_next_round' => $this->getTotalClientStatusCount($interviewModel::STATUS_SELECTED_FOR_NEXT_ROUND, $date, $bdms, $userId, $type),
            'client_waiting_feedback'        => $this->getTotalClientStatusCount($interviewModel::STATUS_WAITING_FEEDBACK, $date, $bdms, $userId, $type),
            'client_confirmed_position'      => $this->getTotalClientStatusCount($interviewModel::STATUS_CONFIRMED_POSITION, $date, $bdms, $userId, $type),
            'client_rejected'                => $this->getTotalClientStatusCount($interviewModel::STATUS_REJECTED_TEXT, $date, $bdms, $userId, $type),
            'client_backout'                 => $this->getTotalClientStatusCount($interviewModel::STATUS_BACKOUT, $date, $bdms, $userId, $type),
        ];
    }

    public function getTotalRequirementCount($date, $bdms, $userId, $type, $isUnique = 0)
    {
        if(!$this->_userIdWiseTotalRequirementCount || !isset($this->_userIdWiseTotalRequirementCount[$isUnique]) || !isset($this->_userIdWiseTotalRequirementCount[$isUnique][$type])){
            $collection = Requirement::select('user_id', \DB::raw('count(id) as count'))
                ->whereIn('user_id', $bdms)
                ->whereBetween('created_at', $date);

            if($isUnique){
                $collection->where(function ($query) {
                    $query->where('id' ,\DB::raw('parent_requirement_id'));
                    $query->orwhere('parent_requirement_id', '=', '0');
                });
            }

            $collection->groupBy('user_id');
            $this->_userIdWiseTotalRequirementCount[$isUnique][$type] = $collection->pluck('count', 'user_id')->toArray();
        }

        if(isset($this->_userIdWiseTotalRequirementCount[$isUnique][$type][$userId])){
            return $this->_userIdWiseTotalRequirementCount[$isUnique][$type][$userId];
        }

        return 0;
    }

    public function getTotalServedRequirementsCount($date, $bdms, $userId, $type)
    {
        if(!$this->_userIdWiseServedRequirementCount || !isset($this->_userIdWiseServedRequirementCount[$type])){
            $this->_userIdWiseServedRequirementCount[$type] =  Requirement::has('submissions')
                ->select('user_id', \DB::raw('count(id) as count'))
                ->whereIn('user_id', $bdms)
                ->whereBetween('created_at', $date)
                ->groupBy('user_id')
                ->pluck('count', 'user_id')
                ->toArray();
        }

        if(isset($this->_userIdWiseServedRequirementCount[$type][$userId])){
            return $this->_userIdWiseServedRequirementCount[$type][$userId];
        }

        return 0;
    }

    public function getTotalUnServedRequirementsCount($date, $bdms, $userId, $type)
    {
        if(!$this->_userIdWiseUnServedRequirementCount || !isset($this->_userIdWiseUnServedRequirementCount[$type])){
            $this->_userIdWiseUnServedRequirementCount[$type] =  Requirement::doesntHave('submissions')
                ->select('user_id', \DB::raw('count(id) as count'))
                ->whereIn('user_id', $bdms)
                ->whereBetween('created_at', $date)
                ->groupBy('user_id')
                ->pluck('count', 'user_id')
                ->toArray();
        }

        if(isset($this->_userIdWiseUnServedRequirementCount[$type][$userId])){
            return $this->_userIdWiseUnServedRequirementCount[$type][$userId];
        }

        return 0;
    }

    public function getTotalReceivedSubmissionCount($date, $bdms, $userId, $type)
    {
        if(!$this->_userIdWisetotalReceivedSubmissionCount || !isset($this->_userIdWisetotalReceivedSubmissionCount[$type])){
            $this->_userIdWisetotalReceivedSubmissionCount[$type] = Requirement::leftJoin('submissions', 'requirements.id', '=', 'submissions.requirement_id')
                ->whereBetween('submissions.created_at', $date)
                ->whereIn('requirements.user_id', $bdms)
                ->groupBy('requirements.user_id')
                ->selectRaw('requirements.user_id, COUNT(submissions.id) as count')
                ->pluck('count', 'user_id')
                ->toArray();
        }

        if(isset($this->_userIdWisetotalReceivedSubmissionCount[$type][$userId])){
            return $this->_userIdWisetotalReceivedSubmissionCount[$type][$userId];
        }

        return 0;
    }

    public function getTotalStatusCount($filedName, $status, $date, $bdms, $userId, $type)
    {
        if(!$this->_userIdWiseStatusCount || !isset($this->_userIdWiseStatusCount[$type]) || !isset($this->_userIdWiseStatusCount[$type][$status])){
            $collection =  Requirement::leftJoin('submissions', 'requirements.id', '=', 'submissions.requirement_id');
            if($status == Submission::STATUS_NOT_VIEWED){
                $collection->where('submissions.is_show', '0')
                    ->whereBetween('submissions.created_at', $date);
            } elseif ($status == Submission::STATUS_PENDING) {
                $collection->where('submissions.is_show', '1')
                    ->where("submissions.$filedName", $status)
                    ->whereBetween('submissions.created_at', $date);
            } else {
                $collection->where("submissions.$filedName", $status)
                    ->whereBetween('submissions.updated_at', $date);
            }
            $collection->whereBetween('submissions.updated_at', $date)
                ->whereIn('requirements.user_id', $bdms)
                ->groupBy('requirements.user_id')
                ->selectRaw('requirements.user_id, COUNT(submissions.id) as count');

            $this->_userIdWiseStatusCount[$type][$status] = $collection->pluck('count', 'user_id')->toArray();
        }

        if(isset($this->_userIdWiseStatusCount[$type][$status][$userId])){
            return $this->_userIdWiseStatusCount[$type][$status][$userId];
        }

        return 0;
    }

    public function getTotalClientStatusCount($status, $date, $bdms, $userId, $type)
    {
        if(!$this->_userIdWiseClientStatusCount || !isset($this->_userIdWiseClientStatusCount[$type]) || !isset($this->_userIdWiseClientStatusCount[$type][$status])){
            $this->_userIdWiseClientStatusCount[$type][$status] =  Requirement::leftJoin('submissions', 'requirements.id', '=', 'submissions.requirement_id')
                ->leftJoin('interviews', 'submissions.id', '=', 'interviews.submission_id')
                ->where('interviews.status', $status)
                ->whereBetween('interviews.updated_at', $date)
                ->whereIn('requirements.user_id', $bdms)
                ->groupBy('requirements.user_id')
                ->selectRaw('requirements.user_id, COUNT(interviews.id) as count')
                ->pluck('count', 'user_id')
                ->toArray();
        }

        if(isset($this->_userIdWiseClientStatusCount[$type][$status][$userId])){
            return $this->_userIdWiseClientStatusCount[$type][$status][$userId];
        }

        return 0;
    }
}