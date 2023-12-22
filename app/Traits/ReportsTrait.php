<?php
namespace App\Traits;
use App\Models\Interview;
use App\Models\Requirement;
use App\Models\Submission;

trait ReportsTrait {
    use RequirementTrait;
    use SubmissionTrait;
    public function getAllBdmsData($request): array
    {
        $bdms = $request->bdm;

        if(empty($bdms) || !count($bdms)){
            return [];
        }

        $bdmUserData = [];

        foreach ($bdms as $bdm){
            $userData = [];
            $userData['heading']    = $this->getBdmUserHeadingData();
            $userData['today']      = $this->getDateWiseBdmData('today', $bdm, $bdms, $request);
            $userData['this_week']  = $this->getDateWiseBdmData('this_week', $bdm, $bdms, $request);
            $userData['last_week']  = $this->getDateWiseBdmData('last_week', $bdm, $bdms, $request);
            $userData['this_month'] = $this->getDateWiseBdmData('this_month', $bdm, $bdms, $request);
            $userData['last_month'] = $this->getDateWiseBdmData('last_month', $bdm, $bdms, $request);
            $userData['time_frame'] = $this->getDateWiseBdmData('time_frame', $bdm, $bdms, $request);

            $bdmUserData['user_data'][$bdm] = $userData;
        }
        $bdmUserData['class_data'] = $this->getKeyWiseClass();

        return $bdmUserData;
    }

    public function getAllRecruitersData($request): array
    {
        $recruiters = $request->recruiter;

        if(empty($recruiters) || !count($recruiters)){
            return [];
        }

        $recruiterUserData = [];

        foreach ($recruiters as $recruiter){
            $userData = [];
            $userData['heading']    = $this->getRecruiterUserHeadingData();
            $userData['today']      = $this->getDateWiseRecruiterData('today', $recruiters, $recruiter, $request);
            $userData['this_week']  = $this->getDateWiseRecruiterData('this_week', $recruiters, $recruiter, $request);
            $userData['last_week']  = $this->getDateWiseRecruiterData('last_week', $recruiters, $recruiter, $request);
            $userData['this_month'] = $this->getDateWiseRecruiterData('this_month', $recruiters, $recruiter, $request);
            $userData['last_month'] = $this->getDateWiseRecruiterData('last_month', $recruiters, $recruiter, $request);
            $userData['time_frame'] = $this->getDateWiseRecruiterData('time_frame', $recruiters, $recruiter, $request);

            $recruiterUserData['user_data'][$recruiter] = $userData;
        }
        $recruiterUserData['class_data'] = $this->getKeyWiseClass();

        return $recruiterUserData;
    }


}
