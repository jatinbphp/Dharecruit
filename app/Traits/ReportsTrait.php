<?php
namespace App\Traits;
use App\Models\Interview;
use App\Models\Requirement;
use App\Models\Submission;

trait ReportsTrait {
    use RequirementTrait;
    use SubmissionTrait;
    use PVCompanyTrait;
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
            $userData['today']      = $this->getDateWiseRecruiterData('today', $recruiter, $recruiters, $request);
            $userData['this_week']  = $this->getDateWiseRecruiterData('this_week', $recruiter, $recruiters, $request);
            $userData['last_week']  = $this->getDateWiseRecruiterData('last_week', $recruiter, $recruiters, $request);
            $userData['this_month'] = $this->getDateWiseRecruiterData('this_month', $recruiter, $recruiters, $request);
            $userData['last_month'] = $this->getDateWiseRecruiterData('last_month', $recruiter, $recruiters, $request);
            $userData['time_frame'] = $this->getDateWiseRecruiterData('time_frame', $recruiter, $recruiters, $request);

            $recruiterUserData['user_data'][$recruiter] = $userData;
        }
        $recruiterUserData['class_data'] = $this->getKeyWiseClass();

        return $recruiterUserData;
    }

    public function getBdmTimeFrameData($request): array
    {
        $fromDate   = $request->fromDate;
        $toDate     = $request->toDate;
        $bdms       = $request->bdm;

        if(!$fromDate || !$toDate || empty($bdms) || !count($bdms)){
            return [];
        }

        $bdmUserData['user_data']['heading']    = $this->getBdmTimeFrameHeadingData();

        foreach ($bdms as $bdm){
            $bdmUserData['user_data'][$bdm] = $this->getDateWiseBdmData('time_frame', $bdm, $bdms, $request, 1);
        }
        $bdmUserData['class_data'] = $this->getKeyWiseClass();

        return $bdmUserData;
    }

    public function getRecruiterTimeFrameData($request): array
    {
        $fromDate   = $request->fromDate;
        $toDate     = $request->toDate;
        $recruiters = $request->recruiter;

        if(!$fromDate || !$toDate || empty($recruiters) || !count($recruiters)){
            return [];
        }

        $recruiterUser['user_data']['heading']  = $this->getRecruiterTimeFrameHeadingData();

        foreach ($recruiters as $recruiter){
            $recruiterUser['user_data'][$recruiter] = $this->getDateWiseRecruiterData('time_frame', $recruiter, $recruiters, $request, 1);
        }
        $recruiterUser['class_data'] = $this->getKeyWiseClass();

        return $recruiterUser;
    }

    public function getPvFilterData($request): array
    {
        if(!$request){
            return [];
        }

        $pvCompanyIds = $request->p_v_company;

        if(!$pvCompanyIds || !count($pvCompanyIds)){
            return  [];
        }

        $pvData['user_data']['heading'] = $this->getPvHeadingData();
        foreach ($pvCompanyIds as $companyId){
            $pvData['user_data'][$companyId] = $this->getCompanyWisePVCompanyData($companyId, $pvCompanyIds, $request);
        }
        $pvData['class_data'] = $this->getPVCompanyClass();

        return  $pvData;
    }
}
