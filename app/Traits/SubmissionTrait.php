<?php
namespace App\Traits;

use App\Models\Interview;
use App\Models\Requirement;
use App\Models\Submission;

trait SubmissionTrait{

    use CommonTrait;

    protected $_userIdWiseAllotadRequirementCount = [];
    protected $_userIdWiseServedRequirementCount = [];
    protected $_userIdWiseUnServedRequirementCount = [];
    protected $_userIdWiseStatusCount = [];
    protected $_userIdWiseClientStatusCount = [];
    protected $_userIdWisetotalReceivedSubmissionCount = [];
    public function getRecruiterUserHeadingData(): array
    {
        return [
            'Recruiter',
            'Allotad',
            'Served',
            'Unserved',
            'Servable%',
            'Sub Sent',
            'Uniq Sub',
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

    public function getDateWiseRecruiterData($type, $userId, $recruiters, $request): array
    {
        $headingType = $this->formatString($type);
        $date = $this->getDate($type, $request);
        if(!$date || !count($date) || !isset($date['from']) || !isset($date['to'])){
            return  [];
        }

        $submissionModel = new Submission();
        $interviewModel  = new Interview();

        $totalRequirements  = $this->getTotalRecruiterRequirementCount($date, $userId, $recruiters, $type);
        $servedRequirements = $this->getTotalRecruiterServedRequirementCount($date, $userId, $recruiters, $type);
        $servablPer         = $this->getPercentage($servedRequirements, $totalRequirements);

        return [
            'heading_type'                   => $headingType,
            'allocated'                      => $totalRequirements,
            'served'                         => $servedRequirements,
            'unserved'                       => 0,
            'servable_per'                   => $servablPer,
            'submission_received'            => 0,
            'bdm_accept'                     => 0,
            'bdm_rejected'                   => 0,
            'bdm_unviewed'                   => 0,
            'bdm_pending'                    => 0,
            'vendor_no_responce'             => 0,
            'vendor_rejected_by_pv'          => 0,
            'vendor_rejected_by_client'      => 0,
            'vendor_submitted_to_end_client' => 0,
            'vendor_position_closed'         => 0,
            'client_rescheduled'             => 0,
            'client_selected_for_next_round' => 0,
            'client_waiting_feedback'        => 0,
            'client_confirmed_position'      => 0,
            'client_rejected'                => 0,
            'client_backout'                 => 0,
        ];
    }

    public function getTotalRecruiterRequirementCount($date, $recruiters, $userId, $type)
    {
        if(!$this->_userIdWiseAllotadRequirementCount || !isset($this->_userIdWiseAllotadRequirementCount[$type])){
            
        }

//        if(!$this->_userIdWiseAllotadRequirementCount || !isset($this->_userIdWiseAllotadRequirementCount[$type])){
//            $this->_userIdWiseAllotadRequirementCount[$type] = Requirement::leftJoin('submissions', 'requirements.id', '=', 'submissions.requirement_id')
//                ->whereBetween('submissions.created_at', $date)
//                ->whereIn('submissions.user_id', $recruiters)
//                ->groupBy('submissions.user_id')
//                ->selectRaw('submissions.user_id, COUNT(requirements.id) as count')
//                ->pluck('count', 'user_id')
//                ->toArray();
//        }
//
//        if(isset($this->_userIdWiseAllotadRequirementCount[$type][$userId])){
//            return $this->_userIdWiseAllotadRequirementCount[$type][$userId];
//        }

        return 0;
    }

    public function getTotalRecruiterServedRequirementCount($date, $recruiters, $userId, $type)
    {
        if(!$this->_userIdWiseServedRequirementCount || !isset($this->_userIdWiseServedRequirementCount[$type])){
            $this->_userIdWiseServedRequirementCount[$type] = Requirement::whereHas('submissions',
                function ($query) use ($recruiters, $date) {
                    $query->whereIn('user_id', $recruiters)
                        ->whereBetween('created_at', $date);
                })
                ->pluck('id')->toArray();
        }

        if(isset($this->_userIdWiseServedRequirementCount[$type][$userId])){
            return $this->_userIdWiseServedRequirementCount[$type][$userId];
        }

        return 0;
    }
}
