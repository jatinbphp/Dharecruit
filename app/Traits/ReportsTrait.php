<?php
namespace App\Traits;
use App\Models\Interview;
use App\Models\Requirement;
use App\Models\Submission;
use App\Models\PVCompany;
use App\Models\Admin;

trait ReportsTrait {
    use RequirementTrait;
    use SubmissionTrait;
    use PVCompanyTrait;
    use POCTrait;
    public function getAllBdmsData($request): array
    {
        $bdms = $request->bdm;

        if(empty($bdms) || !count($bdms)){
            $bdms = array_keys(Admin::getActiveBDM()->toArray());
        }

        $bdmUserData = [];

        foreach ($bdms as $bdm){
            $userData = [];
            $userData['today']      = $this->getDateWiseBdmData('today', $bdm, $bdms, $request);
            $userData['this_week']  = $this->getDateWiseBdmData('this_week', $bdm, $bdms, $request);
            $userData['last_week']  = $this->getDateWiseBdmData('last_week', $bdm, $bdms, $request);
            $userData['this_month'] = $this->getDateWiseBdmData('this_month', $bdm, $bdms, $request);
            $userData['last_month'] = $this->getDateWiseBdmData('last_month', $bdm, $bdms, $request);
            $userData['time_frame'] = $this->getDateWiseBdmData('time_frame', $bdm, $bdms, $request);

            $bdmUserData['user_data'][$bdm] = $userData;
        }
        $bdmUserData['heading']    = $this->getBdmUserHeadingData();
        $bdmUserData['class_data'] = $this->getKeyWiseClass();

        return $bdmUserData;
    }

    public function getAllRecruitersData($request): array
    {
        $recruiters = $request->recruiter;

        if(empty($recruiters) || !count($recruiters)){
            $recruiters = array_keys(Admin::getActiveRecruiter()->toArray());
        }

        $recruiterUserData = [];

        foreach ($recruiters as $recruiter){
            $userData = [];
            $userData['today']      = $this->getDateWiseRecruiterData('today', $recruiter, $recruiters, $request);
            $userData['this_week']  = $this->getDateWiseRecruiterData('this_week', $recruiter, $recruiters, $request);
            $userData['last_week']  = $this->getDateWiseRecruiterData('last_week', $recruiter, $recruiters, $request);
            $userData['this_month'] = $this->getDateWiseRecruiterData('this_month', $recruiter, $recruiters, $request);
            $userData['last_month'] = $this->getDateWiseRecruiterData('last_month', $recruiter, $recruiters, $request);
            $userData['time_frame'] = $this->getDateWiseRecruiterData('time_frame', $recruiter, $recruiters, $request);

            $recruiterUserData['user_data'][$recruiter] = $userData;
        }
        $recruiterUserData['heading']    = $this->getRecruiterUserHeadingData();
        $recruiterUserData['class_data'] = $this->getKeyWiseClass();

        return $recruiterUserData;
    }

    public function getBdmTimeFrameData($request): array
    {
        $fromDate   = $request->fromDate;
        $toDate     = $request->toDate;
        $bdms       = $request->bdm;

        if(!$fromDate || !$toDate){
            return [];
        }

        if(empty($bdms) || !count($bdms)){
            $bdms = array_keys(Admin::getActiveBDM()->toArray());
        }

        foreach ($bdms as $bdm){
            $bdmUserData['user_data'][$bdm] = $this->getDateWiseBdmData('time_frame', $bdm, $bdms, $request, 1);
        }

        $bdmUserData['heading']    = $this->getBdmTimeFrameHeadingData();;
        $bdmUserData['class_data'] = $this->getKeyWiseClass();

        return $bdmUserData;
    }

    public function getRecruiterTimeFrameData($request): array
    {
        $fromDate   = $request->fromDate;
        $toDate     = $request->toDate;
        $recruiters = $request->recruiter;

        if(!$fromDate || !$toDate){
            return [];
        }

        if(empty($recruiters) || !count($recruiters)){
            $recruiters = array_keys(Admin::getActiveRecruiter()->toArray());
        }

        foreach ($recruiters as $recruiter){
            $recruiterUser['user_data'][$recruiter] = $this->getDateWiseRecruiterData('time_frame', $recruiter, $recruiters, $request, 1);
        }

        $recruiterUser['heading']  = $this->getRecruiterTimeFrameHeadingData();
        $recruiterUser['class_data'] = $this->getKeyWiseClass();

        return $recruiterUser;
    }

    public function getPvFilterData($request): array
    {
        $pvCompanies = $request->p_v_company;

        if(!$pvCompanies || !count($pvCompanies)){
            $pvCompanies = array_keys(PVCompany::getActivePVCompanyies()->toArray());
        }

        $pvData['heading'] = $this->getPvHeadingData();
        foreach ($pvCompanies as $pvCompany){
            $pvCompanyKey = $this->getKey($pvCompany);
            $pvData['pv_company_data'][$pvCompanyKey] = $this->getCompanyWisePVCompanyData($pvCompany, $pvCompanies, $request);
            $pvData['poc_data'][$pvCompanyKey]        = $this->getCompanyWisePocData($pvCompany, $request);
        }
        $pvData['heading']    = $this->getPvHeadingData();
        $pvData['class_data'] = $this->getPVClass();

        return  $pvData;
    }

    public function getPOCFilterData($request)
    {
        $pvCompanies = $request->p_v_company;
        $pocNames = $request->poc_name;

        if(!$pvCompanies || !count($pvCompanies)){
            $pvCompanies = array_keys(PVCompany::getActivePVCompanyies()->toArray());
        }

        if(!$pocNames || !count($pocNames)){
            $pocNames = array_keys(PVCompany::getActivePOCNames()->toArray());
        }
//        if(!$pvCompanies || !count($pvCompanies) || !$pocNames || !count($pocNames)){
//            return  [];
//        }

        $pvData['heading'] = $this->getPOCHeadingData();
        foreach ($pvCompanies as $pvCompany){
            $pvCompanyKey = $this->getKey($pvCompany);
            $pvData['poc_data'][$pvCompanyKey] = $this->getPocWiseData($pvCompany, $pocNames, $request);
        }
        $pvData['class_data'] = $this->getPVClass();

        return  $pvData;
    }
}
