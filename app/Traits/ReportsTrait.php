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
    use EmployerTrait;
    use EmployeeTrait;
    public function getAllBdmsData($request): array
    {
        $bdms = $request->bdm;

        if(empty($bdms) || !count($bdms)){
            $bdms = array_keys(Admin::getActiveBDM());
            if($request->sub_type == 'lead_sub_received'){
                if(isLeadUser() && isManager()){
                    $bdms = array_merge(getTeamMembers(), getManagerAllUsers());
                }elseif(isLeadUser()){
                    $bdms = getTeamMembers();
                }elseif(isManager()){
                    $bdms = getManagerAllUsers();
                }
            } elseif (getLoggedInUserRole() == 'bdm'){
                $bdms = [getLoggedInUserId()];
            }
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
            $recruiters = array_keys(Admin::getActiveRecruiter());
            if($request->sub_type == 'lead_sub_sent'){
                if(isLeadUser() && isManager()){
                    $recruiters = array_merge(getTeamMembers(), getManagerAllUsers());
                }elseif(isLeadUser()){
                    $recruiters = getTeamMembers();
                }elseif(isManager()){
                    $recruiters = getManagerAllUsers();
                }
            } elseif (getLoggedInUserRole() == 'recruiter'){
                $recruiters = [getLoggedInUserId()];
            }
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
            $bdms = array_keys(Admin::getActiveBDM());
            if($request->sub_type == 'lead_sub_received'){
                if(isLeadUser() && isManager()){
                    $bdms = array_merge(getTeamMembers(), getManagerAllUsers());
                }elseif(isLeadUser()){
                    $bdms = getTeamMembers();
                }elseif(isManager()){
                    $bdms = getManagerAllUsers();
                }
            } elseif (getLoggedInUserRole() == 'bdm'){
                $bdms = [getLoggedInUserId()];
            }
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
            $recruiters = array_keys(Admin::getActiveRecruiter());
            if($request->sub_type == 'lead_sub_sent'){
                if(isLeadUser() && isManager()){
                    $recruiters = array_merge(getTeamMembers(), getManagerAllUsers());
                }elseif(isLeadUser()){
                    $recruiters = getTeamMembers();
                }elseif(isManager()){
                    $recruiters = getManagerAllUsers();
                }
            } elseif (getLoggedInUserRole() == 'recruiter'){
                $recruiters = [getLoggedInUserId()];
            }
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

        foreach ($pvCompanies as $pvCompany){
            $pvCompanyKey = $this->getKey($pvCompany);
            $pvData['pv_company_data'][$pvCompanyKey] = $this->getCompanyWisePVCompanyData($pvCompany, $pvCompanies, $request);
            $pvData['is_new_pv_data'][$pvCompanyKey]  = $this->isNewAsPerConfiguration('pv_company_name', $pvCompany);
            $allData = $this->getCompanyWisePocData($pvCompany, $request);
            $pvData['poc_data'][$pvCompanyKey]        = isset($allData['poc_data']) ? $allData['poc_data'] : [];
            $pvData['is_new_poc_data'][$pvCompanyKey]  = isset($allData['is_new_poc_data']) ? $allData['is_new_poc_data'] : [];
        }
        $pvData['heading']    = $this->getPvHeadingData();
        $pvData['class_data'] = $this->getTextClass();
        $pvData['empty_pv_rows'] = $this->getEmptyPVRows();
        $pvData['empty_poc_rows'] = $this->getEmptyPOCRows();

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

        $pocData['heading'] = $this->getPOCHeadingData();
        foreach ($pvCompanies as $pvCompany){
            $pvCompanyKey = $this->getKey($pvCompany);
            $allData = $this->getPocWiseData($pvCompany, $pvCompanies, $pocNames, $request);
            $pocData['poc_data'][$pvCompanyKey] = isset($allData['data']) ? $allData['data'] : [];
            $pocData['poc_org_req_count'][$pvCompanyKey] = isset($allData['poc_org_req_count']) ? $allData['poc_org_req_count'] : [];
            $pocData['is_new_pv_company'][$pvCompanyKey] = $this->isNewAsPerConfiguration('pv_company_name', $pvCompany);
            $pocData['is_new_poc'][$pvCompanyKey] = isset($allData['is_new_poc']) ? $allData['is_new_poc'] : [];
        }
        $pocData['class_data']     = $this->getTextClass();
        $pocData['empty_poc_rows'] = $this->getEmptyPOCRows();
        $pocData['hide_columns']   = $this->getPocHideColumns();
        $pocData['pv_company_org_req_count']   = $this->getPVCompanyWiseOrgReqCount();

        return  $pocData;
    }

    public function getEmployerFilterData($request): array
    {
        $employers = $request->employer;

        if(!$employers || !count($employers)){
            $employers = array_keys(Admin::getActiveEmployers()->toArray());
        }

        $employeeData = [];
        foreach ($employers as $employer){
            $employerKey = $this->getKey($employer);
            $employeeData['employer_company_data'][$employerKey] = $this->getEmployerWiseData($employer, $employers, $request);
            $employeeData['employee_data'][$employerKey]        = $this->getEmployerWiseEmployeeData($employer, $request);
        }
        $employeeData['heading']    = $this->getEmployerHeadingData();
        $employeeData['class_data'] = $this->getTextClass();
        $employeeData['empty_employer_rows'] = $this->getEmptyEmployerRows();
        $employeeData['empty_poc_rows'] = $this->getEmptyPOCRows();

        return  $employeeData;
    }

    public function getEmployeeFilterData($request): array
    {
        $employerNames = $request->employer_name;
        $employeeNames = $request->employee_name;

        if(!$employerNames || !count($employerNames)){
            $employerNames = array_keys(Admin::getActiveEmployers()->toArray());
        }

        if(!$employeeNames || !count($employeeNames)){
            $employeeNames = array_keys(Admin::getActiveEmployees()->toArray());
        }
//        if(!$employerNames || !count($employerNames) || !$employeeNames || !count($employeeNames)){
//            return  [];
//        }

        $employeData['heading'] = $this->getEmployeeHeadingData();
        foreach ($employerNames as $employeName){
            $employeNameKey = $this->getKey($employeName);
            $allData = $this->getEmployeeWiseData($employeName, $employerNames, $employeeNames, $request);
            $employeData['emp_poc_data'][$employeNameKey] = isset($allData['data']) ? $allData['data'] : [];
            $employeData['employee_uni_sub_count'][$employeNameKey] = isset($allData['employee_uni_sub_count']) ? $allData['employee_uni_sub_count'] : [];
        }
        $employeData['class_data']     = $this->getTextClass();
        $employeData['empty_poc_rows'] = $this->getEmptyPOCRows();
        $employeData['hide_columns']   = $this->getEmployeeHideColumns();
        $employeData['employer_uni_sub_count']   = $this->getEmployerWiseUniSubmissionCount();

        return  $employeData;
    }
}
