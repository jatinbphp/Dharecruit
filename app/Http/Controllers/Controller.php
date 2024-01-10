<?php

namespace App\Http\Controllers;

use App\Models\AssignToRecruiter;
use App\Models\Requirement;
use App\Models\Submission;
use App\Models\Interview;
use App\Models\EntityHistory;
use App\Models\Setting;
use App\Models\DataLog;
use App\Models\PVCompany;
use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Orders;
use App\Models\OrderItems;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use DataTables;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $_user = null;
    protected $_currentUserId = null;
    protected $_currentUserRole = null;
    protected $_userIdWiseName = [];
    protected $_candidateIdWiseIsEmployerChanged = [];
    public function fileMove($photo, $path){
        $root = storage_path('app/public/uploads/'.$path);
        $filename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
        $name = $filename."_".date('His',time()).".".$photo->getClientOriginalExtension();
        if (!file_exists($root)) {
            mkdir($root, 0777, true);
        }
        $photo->move($root,$name);
        return 'uploads/'.$path."/".$name;
    }

    public function getCurrentUserId()
    {
        if(!$this->_currentUserId){
            $this->_currentUserId = $this->getUser()->id;
        }
        return $this->_currentUserId;
    }

    public function getCurrentUserRole()
    {
        if(!$this->_currentUserRole){
            $this->_currentUserRole = $this->getUser()->role;
        }

        return $this->_currentUserRole;
    }
    public function getListHtml($query, $request, $page='requirement') {
        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('job_id', function($row) {
                return $this->getJobIdHtml($row);
            })
            ->addColumn('job_title', function($row){
                return $this->getJobTitleHtml($row);
            })
            ->addColumn('recruiter', function($row) use (&$request){
              return $this->getRecruiterHtml($row, $request);
            })
            ->addColumn('status', function($row){
                return $this->getStatusHtml($row);
            })
            ->addColumn('candidate', function ($row) use (&$page, &$request){
                return $this->getCandidateListData($row, $page, $request);
            })
            ->addColumn('action', function ($row) use (&$page){
                return $this->getActionHtml($row, $page);
            })
            ->addColumn('job_keyword', function($row) {
               return $this->getJobKeywordHtml($row);
            })
            ->addColumn('pv', function($row) {
                return $this->getPvHtml($row);
            })
            ->addColumn('poc', function($row) {
                return $this->getPocHtml($row);
            })
            ->addColumn('total_orig_req', function($row) {
                return $this->getTotalOrigReq($row);
            })
            ->addColumn('total_orig_req_in_days', function($row) {
                return $this->getTotalOrigReqInDays($row);
            })
            ->setRowClass(function ($row) {
                return (($row->parent_requirement_id != 0 && $row->parent_requirement_id == $row->id) ? 'parent-row' : (($row->parent_requirement_id != 0) ? 'child-row' : ''));
            })
            ->rawColumns(['recruiter','status','candidate','job_title','job_keyword','job_id','pv','poc','total_orig_req','total_orig_req_in_days','action'])
            ->make(true);
    }

    public function getJobIdHtml($row){
        $jid = '';
        if(Auth::user()->role == 'admin'){
            if($this->isLinkRequirement($row->poc_email)){
                $jid .= '<div class="border text-center text-light link-data" style="background-color:rgb(172, 91, 173); width: 40px; display:none">Link</div>';
            }
        }
        if(Auth::user()->role == 'admin' || (Auth::user()->role=='bdm' && Auth::user()->id == $row->user_id)){
            if($row->parent_requirement_id != $row->id && $row->parent_requirement_id != 0){
                return $jid .'<span data-order="'.$row->job_id.'" class="border-width-5 border-color-info job-title pt-1 pl-1 pl-1 pr-1" data-id="'.$row->id.'">'.$row->job_id.'</span>';
            } elseif($row->parent_requirement_id == $row->id){
                return $jid.'<span data-order="'.$row->job_id.'" class="border-width-5 border-color-warning job-title pt-1 pl-1 pl-1 pr-1" data-id="'.$row->id.'">'.$row->job_id.'</span>';
            } else {
                return $jid.'<span class=" job-title" data-id="'.$row->id.'" data-order="'.$row->job_id.'">'.$row->job_id.'</span>';
            }
        } else {
            return '<span class=" job-title" data-id="'.$row->id.'" data-order="'.$row->job_id.'">'.$row->job_id.'</span>';
        }
    }
    public function getJobTitleHtml($row): string
    {
        $loggedinUser = $this->getCurrentUserId();
        $isShowRecruiters = explode(',', $row->is_show_recruiter);
        $isShowRecruitersAfterUpdate = explode(',', $row->is_show_recruiter_after_update);
        $textStyle = '';
        if($this->getCurrentUserRole() == 'recruiter'){
            if(!in_array($loggedinUser, $isShowRecruiters)){
                $textStyle = 'pt-1 pl-2 pb-1 pr-2 border border-primary text-primary';
            } else if($row->is_update_requirement == 1){
                if(!in_array($loggedinUser, $isShowRecruitersAfterUpdate) && in_array($loggedinUser, $isShowRecruiters)){
                    $textStyle = 'pt-1 pl-2 pb-1 pr-2 border border-warning text-warning';
                }
            }
        }
        $userWiseCount = '';
        if($this->getCurrentUserRole() == 'admin'){
            $userWiseCount = $this->getUserWiseRequirementsCountAsPerPoc($row->poc_name, 1);
        }
        return '<div data-order="'.$row->job_title.'" class="'.$textStyle.' job-title job-title-'.$row->id.'" data-id="'.$row->id.'"><span class="font-weight-bold">'.$row->job_title.'</span></div>'.(($userWiseCount) ? "<div class='container pl-0'><div class='bg-white p-1 border d-inline-block'>".$userWiseCount."</div></div>" : "");
    }

    public function getRecruiterHtml($row, $request): string
    {
        $recruiterIds = !empty($row->recruiter) ? explode(',',$row->recruiter) : [];

        if(!count($recruiterIds)){
            return '';
        }
        $recName = '';

        $user = $this->getUser();
        $loggedInUserId = $this->getCurrentUserId();

        if($this->getCurrentUserRole() == 'recruiter'){
            if(in_array($loggedInUserId, $recruiterIds)){
                $recruiterIds = array_flip($recruiterIds);
                unset($recruiterIds[$loggedInUserId]);
                $recruiterIds = array_filter(array_flip($recruiterIds));
                sort($recruiterIds);
                array_unshift($recruiterIds, $loggedInUserId);
            }
        } else {
            $recruiterIds = array_filter($recruiterIds);
            sort($recruiterIds);
        }

        $filterRecIds = [];
        if(!empty($request->recruiter)){
            if(empty($request->served)){
                $filterRecIds[] = [$request->recruiter];
            }
        }

        if(!empty($request->candidate_name)){
            $candidateNameIds = Submission::where('name', 'like', '%'.$request->candidate_name.'%')->where('requirement_id', $row->id)->pluck('user_id')->toArray();
            $filterRecIds[] = $candidateNameIds;
        }

        if(!empty($request->candidate_id)){
            $candidateIdIds = Submission::where('candidate_id', $request->candidate_id)->where('requirement_id', $row->id)->pluck('user_id')->toArray();
            $filterRecIds[] = $candidateIdIds;
        }

        if(!empty($request->filter_employer_name)){
            $employerIds = Submission::where('employer_name', $request->filter_employer_name)->where('requirement_id', $row->id)->pluck('user_id')->toArray();
            $filterRecIds[] = $employerIds;
        }

        if(!empty($request->filter_employee_name)){
            $employeeNameIds = Submission::where('employee_name', $request->filter_employee_name)->where('requirement_id', $row->id)->pluck('user_id')->toArray();
            $filterRecIds[] = $employeeNameIds;
        }

        if(!empty($request->filter_employee_phone_number)){
            $employeePhoneIds = Submission::where('employee_phone', $request->filter_employee_phone_number)->where('requirement_id', $row->id)->pluck('user_id')->toArray();
            $filterRecIds[] = $employeePhoneIds;
        }

        if(!empty($request->fifilterlter_employee_email)){
            $employeeEmailIds = Submission::where('employee_email', $request->filter_employee_email)->where('requirement_id', $row->id)->pluck('user_id')->toArray();
            $filterRecIds[] = $employeeEmailIds;
        }

        if($filterRecIds && count($filterRecIds)){
            $commonRequirementIds = call_user_func_array('array_intersect', $filterRecIds);
            if($commonRequirementIds && count($commonRequirementIds)){
                $recruiterIds = array_unique($commonRequirementIds);
            } else {
                $recruiterIds = [];
            }
        }

        foreach ($recruiterIds as $recruiterId){
            $recruterUser = Admin::where('id',$recruiterId)->first();
            if(empty($recruterUser)){
                continue;
            }
            $bgColor = '';
            if($user->id == $recruterUser->id){
                $bgColor = '#BED8E2';
            }
            $submission = Submission::where('user_id',$recruiterId)->where('requirement_id',$row->id)->count();
            $recName .= '<div class="border border-dark floar-left p-1 mt-2" style="
                border-radius: 5px; width: auto; background-color:'.$bgColor.'"><span>'. $submission.' '.$recruterUser['name']. '</span></div>';
        }
        return $recName;
    }

    public function getStatusHtml($row){
        $statusBtn = '';
        $status = $row->status;
        if($this->getCurrentUserRole() == 'admin' || $this->getCurrentUserId() == $row->user_id){
            if ($status == "hold") {
                $statusBtn .= '<div class="btn-group-horizontal" id="assign_remove_"'.$row->id.'">
                                    <button class="btn btn-danger unassign ladda-button" data-style="slide-left" id="remove" url="'.route('requirement.unassign').'" ruid="'.$row->id.'" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Hold</span> </button>
                                </div>';
                $statusBtn .= '<div class="btn-group-horizontal" id="assign_add_"'.$row->id.'"  style="display: none"  >
                                    <button class="btn btn-success assign ladda-button" data-style="slide-left" id="assign" uid="'.$row->id.'" url="'.route('requirement.assign').'" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Need</span></button>
                                </div>';
            }
            if ($status == "unhold") {
                $statusBtn .= '<div class="btn-group-horizontal" id="assign_add_"'.$row->id.'">
                                    <button class="btn btn-success assign ladda-button" id="assign" data-style="slide-left" uid="'.$row->id.'" url="'.route('requirement.assign').'" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Need</span></button>
                                </div>';
                $statusBtn .= '<div class="btn-group-horizontal" id="assign_remove_"'.$row->id.'" style="display: none" >
                                    <button class="btn  btn-danger unassign ladda-button" id="remove" ruid="'.$row->id.'" data-style="slide-left" url="'.route('requirement.unassign').'" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Hold</span></button>
                                </div>';
            }
        }else{
            if ($status == "hold") {
                $statusBtn .= '<div class="btn-group-horizontal">
                                    <button class="btn btn-danger noChange ladda-button" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Hold</span></button>
                                </div>';
            }
            if ($status == "unhold") {
                $statusBtn .= '<div class="btn-group-horizontal">
                                    <button class="btn btn-success noChange ladda-button" data-style="slide-left" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Need</span></button>
                                </div>';
            }
        }
        $requirementObj = new Requirement();
        if(in_array($status,[$requirementObj::STATUS_EXP_HOLD, $requirementObj::STATUS_EXP_NEED])){
            $statusBtn .= '<div class="btn-group-horizontal">
                                <button class="btn btn btn-secondary noChange ladda-button" data-style="slide-left" type="button" style="height:28px; padding:0 12px"><span class="ladda-label"><b>'.(isset(Requirement::$exprieStatus[$status]) ? (Requirement::$exprieStatus[$status]) : '').'</b></span></button>
                           </div>';
        }
        return $statusBtn;
    }

    public function getCandidateListData($row, $page='requirement', $request){
        $userRole = $this->getCurrentUserRole();
        $userId   = $this->getCurrentUserId();
        if(in_array($userRole, ['recruiter', 'bdm', 'admin'])){
            $loggedInRecruterSubmission    = Submission::query();
            $notLoggedInRecruterSubmission = Submission::query();

            if(!empty($request->filter_employer_name)){
                $loggedInRecruterSubmission->where('employer_name', $request->filter_employer_name);
            }

            if(!empty($request->filter_employee_name)){
                $loggedInRecruterSubmission->where('employee_name', $request->filter_employee_name);
            }

            if(!empty($request->filter_employee_phone_number)){
                $loggedInRecruterSubmission->where('employee_phone', $request->filter_employee_phone_number);
            }

            if(!empty($request->filter_employee_email)){
                $loggedInRecruterSubmission->where('employee_email', $request->filter_employee_email);
            }

            if(!empty($request->candidate_name)){
                $loggedInRecruterSubmission->where('name', 'like', '%'.$request->candidate_name.'%');
            }

            if(!empty($request->candidate_id)){
                $loggedInRecruterSubmission->where('candidate_id', $request->candidate_id);
            }

            if(!empty($request->bdm_feedback)){
                $bdmFeedBack = $request->bdm_feedback;
                $isOrWhere = 0;
                $isWhere = 0;
                $isStatus = 0;
                if(in_array('no_updates', $bdmFeedBack)){
                    $bdmFeedBack = array_flip($bdmFeedBack);
                    unset($bdmFeedBack['no_updates']);
                    $bdmFeedBack = array_flip($bdmFeedBack);
                    $isWhere = 1;
                }

                if(in_array('no_viewed', $bdmFeedBack)){
                    $bdmFeedBack = array_flip($bdmFeedBack);
                    unset($bdmFeedBack['no_viewed']);
                    $bdmFeedBack = array_flip($bdmFeedBack);

                    if($bdmFeedBack && count($bdmFeedBack)){
                        $isStatus = 1;
                    } else {
                        $isOrWhere = 1;
                    }
                }

                $loggedInRecruterSubmission->where(function ($loggedInRecruterSubmission) use ($isWhere, $isStatus, $isOrWhere, $bdmFeedBack) {
                    if($isWhere == 1){
                        $loggedInRecruterSubmission->whereNull('pv_status');
                        $loggedInRecruterSubmission->Where('is_show', 1);
                        $loggedInRecruterSubmission->Where('status', 'pending');
                        if($isStatus == 0 && $bdmFeedBack && count($bdmFeedBack)){
                            $loggedInRecruterSubmission->orWhereIn('status', $bdmFeedBack);
                        }
                    }
                    if($isOrWhere == 1){
                        $loggedInRecruterSubmission->orWhere('is_show', 0);
                    }
                    if($isStatus == 1){
                        $loggedInRecruterSubmission->orwhere(function ($loggedInRecruterSubmission) use ($isWhere, $isStatus, $isOrWhere, $bdmFeedBack) {
                            $loggedInRecruterSubmission->whereIn('status', $bdmFeedBack);
                            $loggedInRecruterSubmission->orWhere('is_show', 0);
                        });
                    }
                });

                if(!in_array('no_updates', $request->bdm_feedback) && !in_array('no_viewed', $request->bdm_feedback)){
                    $loggedInRecruterSubmission->whereIn('status', $request->bdm_feedback);
                }
            }

            if(!empty($request->pv_feedback)){
                $loggedInRecruterSubmission->whereIn('pv_status', $request->pv_feedback);
            }

            if(!empty($request->client_feedback)){
                $submissionId = Interview::whereIn('status', $request->client_feedback)->pluck('submission_id')->toArray();
                if($submissionId){
                    $loggedInRecruterSubmission->whereIn('id', $submissionId);
                } else {
                    $loggedInRecruterSubmission->whereIn('id', []);
                }
            }

            if($userRole == 'recruiter'){
                $loggedInRecruiters = $loggedInRecruterSubmission->where('user_id', $userId)->where('requirement_id',$row->id)->orderby('user_id','DESC')->get();
            } else {
                if(!empty($request->recruiter)){
                    $loggedInRecruiters = $loggedInRecruterSubmission->where('user_id', $request->recruiter)->where('requirement_id',$row->id)->orderby('user_id','DESC')->get();
                } else {
                    $loggedInRecruiters = $loggedInRecruterSubmission->where('requirement_id',$row->id)->orderby('user_id','DESC')->get();

                }
            }

            if(empty($request->filter_employer_name) && empty($request->filter_employee_name) && empty($request->filter_employee_phone_number) && empty($request->filter_employee_email) && empty($request->bdm_feedback) && empty($request->pv_feedback) && empty($request->client_feedback) && empty($request->candidate_name) && empty($request->candidate_id) && empty($request->recruiter)){
                $notLogeedInRecruiters = $notLoggedInRecruterSubmission->where('user_id', '!=',$userId)->where('requirement_id',$row->id)->orderby('user_id','ASC')->get();
                $allSubmission = $loggedInRecruiters->merge($notLogeedInRecruiters);
            } else {
                $allSubmission = $loggedInRecruiters;
            }

        } else {
            $allSubmission = Submission::where('requirement_id',$row->id)->orderby('user_id','ASC')->get();
            if($userRole == 'bdm' || $userRole == 'admin'){
                if(!empty($request->recruiter)){
                    $allSubmission = Submission::where('requirement_id',$row->id)->where('user_id', $request->recruiter)->orderby('user_id','ASC')->get();
                }
            }
        }

        $candidate = '';
        if($allSubmission && count($allSubmission) > 0){
            $candidate .= $this->getCandidateHtml($allSubmission, $row, $page);
        } else {
            if(!empty($row->recruiter)){
                $candidate .= '<div style="width:50px; background-color: yellow;">&nbsp;</div>';
            }
        }
        return $candidate;
    }

    public function getActionHtml($row, $page='requirement'){
        $exprieStatus = Requirement::$exprieStatus;
        $loggedInUserId = $this->getCurrentUserId();
        $userRole       = $this->getCurrentUserRole();
        $btn = '';
        if($page == 'submission'){
            if($row->submissionCounter < 3){
                $rId = !empty($row->recruiter) ? explode(',',$row->recruiter) : [];
                if(!empty($rId) && in_array($loggedInUserId, $rId) && !array_key_exists($row->status, $exprieStatus)){
                    //$btn = '<div class="btn-group btn-group-sm mr-2"><a href="'.url('admin/submission/'.$row->id).'"><button class="btn btn-sm btn-default tip" data-toggle="tooltip" title="View Submission" data-trigger="hover" type="submit" ><i class="fa fa-eye"></i></button></a></div>';
                    $btn = '<div class="btn-group btn-group-sm mr-2"><button class="btn btn-sm btn-default tip view-submission" data-toggle="tooltip" title="View Submission" data-trigger="hover" type="submit" data-id="'.$row->id.'" ><i class="fa fa-eye"></i></button></div>';
                    $btn .= '<div class="btn-group btn-group-sm"><a href="'.url('admin/submission/new/'.$row->id).'"><button class="btn btn-sm btn-default tip" data-toggle="tooltip" title="Add New Submission" data-trigger="hover" type="submit" ><i class="fa fa-upload"></i></button></a></div>';
                }else{
                    $btn = '';
                    if($row->status != "hold" && !array_key_exists($row->status,$exprieStatus)){
                        $btn = '<span data-toggle="tooltip" title="Assign Requirement" data-trigger="hover">
                                    <button class="btn btn-sm btn-default assignRequirement mr-2" data-id="'.$row->id.'" type="button"><i class="fa fa-plus-square"></i></button>
                                </span>';
                    }
                }
            }else{
                $btn = '';
            }
            $btn .= '<div class="border border-dark floar-left p-1 mt-2" style="
                border-radius: 5px; width: auto"><span>'.getTimeInReadableFormate($row->created_at).'</span></div>';
        } else {
            if(($userRole == 'admin' && !array_key_exists($row->status, $exprieStatus)) || ($userRole == 'bdm' && $loggedInUserId == $row->user_id && !array_key_exists($row->status, $exprieStatus))){
                $btn .= '<div class="btn-group btn-group-sm mr-2"><a href="'.url('admin/requirement/'.$row->id.'/edit').'"><button class="btn btn-sm btn-default tip" data-toggle="tooltip" title="Edit Requirement" data-trigger="hover" type="submit" ><i class="fa fa-edit"></i></button></a></div>';
            }
            if($userRole == 'admin' && !array_key_exists($row->status, $exprieStatus)){
                $btn .= '<span data-toggle="tooltip" title="Delete Requirement" data-trigger="hover">
                            <button class="btn btn-sm btn-default deleteRequirement mr-2" data-id="'.$row->id.'" type="button"><i class="fa fa-trash"></i></button>
                        </span>';
            }
            //$btn .= '<div class="btn-group btn-group-sm"><a href="'.url('admin/requirement/'.$row->id).'"><button class="btn btn-sm btn-default tip" data-toggle="tooltip" title="View Submission" data-trigger="hover" type="submit" ><i class="fa fa-eye"></i></button></a></div>';
            if(($userRole == 'admin') || ($loggedInUserId == $row->user_id)){
                $btn .= '<div class="btn-group btn-group-sm"><button class="btn btn-sm btn-default tip view-submission" data-toggle="tooltip" title="View Submission" data-trigger="hover" type="submit" data-id="'.$row->id.'"><i class="fa fa-eye"></i></button></div>';
            }
            //$btn .= '<div class="btn-group btn-group-sm ml-2"><a href="'.Route('requirement.repost',[$row->id]).'"><button class="btn btn-sm btn-default tip" data-toggle="tooltip" title="Repost Requirement" data-trigger="hover" type="submit"><i class="fa fa-retweet"></i></button></a></div>';
            if(($userRole == 'admin') || ($userRole == 'bdm' && $loggedInUserId == $row->user_id && $page != 'all_requirement')){
                $btn .= '<div class="btn-group btn-group-sm ml-2"><a href="'.url('admin/requirement/repostReqirement').'/'.$row->id.'"><button class="btn btn-sm btn-default tip" data-toggle="tooltip" title="Repost Requirement" data-trigger="hover" type="submit"><i class="fa fa-retweet"></i></button></a></div>';
            }
            $btn .= '<div class="border border-dark floar-left p-1 mt-2" style="
                border-radius: 5px; width: auto"><span>'.getTimeInReadableFormate($row->created_at).'</span></div>';
        }
        return $btn;
    }

    public function getJobKeywordHtml($row){
        $jobKeyword = strip_tags($row->job_keyword);
        $userWiseCount = '';
        if($this->getCurrentUserRole() == 'admin'){
            $userWiseCount = $this->getUserWiseRequirementsCountAsPerPoc($row->poc_name);
        }
        if(strlen($jobKeyword) > 60){
            $shortString = substr($jobKeyword, 0, 60);
            return '<p>' . $shortString . '<span class=" job-title" data-id="'.$row->id.'"><span class="font-weight-bold"> More +</span></p>'.(($userWiseCount) ? "<div class='container pl-0'><div class='bg-white p-1 border d-inline-block'>".$userWiseCount."</div></div>" : "");
        }
        return '<p>'.strip_tags($row->job_keyword).'</p>'.(($userWiseCount) ? "<div class='container pl-0'><div class='bg-white p-1 border d-inline-block'>".$userWiseCount."</div></div>" : "");
    }

    public function getPvHtml($row){
        if($this->getCurrentUserRole() != 'admin'){
            return $row->pv_company_name;
        }

        $totalPvCount  = $this->getAllPvCompanyCount($row->pv_company_name);
        $isNewPoc      = $this->isNewAsPerConfiguration('pv_company_name', $row->pv_company_name);

        $pocHtml = '<span class="font-weight-bold '.(($isNewPoc) ? "text-primary" : "").'">'.$row->pv_company_name;
        $pocHtml .= '<br><br><span class="border pt-1 pl-1 pr-1 pb-1 '.(($isNewPoc) ? "border-primary" : "border-secondary").'">'.$totalPvCount.'</span></span>';
        return $pocHtml;
    }

    public function getPocHtml($row){
        if($this->getCurrentUserRole() != 'admin'){
            return $row->poc_name;
        }
        $controllerObj = new Controller();
        $isNewPoc      = $controllerObj->isNewAsPerConfiguration('poc_name', $row->poc_name);
        return '<p class="font-weight-bold '.(($isNewPoc) ? "text-primary" : "").'">'.$row->poc_name.'</p>';
    }

    public function getTotalOrigReq($row){
        if($this->getCurrentUserRole() != 'admin'){
            return '';
        }
        $controllerObj = new Controller();

        return $controllerObj->getTotalOrigReqBasedOnPocData($row->poc_name, 1);
    }

    public function getTotalOrigReqInDays($row){
        if($this->getCurrentUserRole() != 'admin'){
            return '';
        }
        $controllerObj = new Controller();
        $totalPvCount = $controllerObj->getAllPvCompanyCount($row->poc_name);
        $isNewPoc     = $controllerObj->isNewAsPerConfiguration('poc_name', $row->poc_name);

        return $controllerObj->getTotalOrigReqBasedOnPocData($row->poc_name);
    }

    public function getUser(){
        if(!$this->_user){
            $this->_user = Auth::user();
        }
        return $this->_user;
    }

    public function Filter($request, $page=''){
        $user = Auth::user();
        $requirementIds = [];

        $query = Requirement::with(
                [
                    'BDM' => function ($query) {
                        $query->select('name as bdm_name','id');
                    },
                    'Category' => function ($query) {
                        $query->select('name as category_name','id');
                    },
                ]
            )->select('id', 'created_at','user_id','job_id', 'job_title', 'location', 'work_type', 'duration', 'visa', 'client', 'my_rate', 'category', 'moi', 'job_keyword', 'pv_company_name', 'poc_name', 'client_name', 'display_client', 'status', 'recruiter', 'is_show_recruiter', 'is_show_recruiter_after_update','is_update_requirement','parent_requirement_id');
        $expStatus = [Requirement::STATUS_EXP_HOLD , Requirement::STATUS_EXP_NEED];
        if($user['role'] == 'bdm' && isset($request->authId) && $request->authId > 0){
            $query = $query->where('user_id',$request->authId);
        }elseif($user['role'] == 'recruiter' && isset($request->authId) && $request->authId > 0){
            $query = $query->whereRaw("find_in_set($request->authId,recruiter)");
        }

        // As Per Client Requirement Not Show Expired Req on all page for bdm and req but they can search it.
        if(in_array(Auth::user()->role, ['bdm', 'recruiter']) && $page == 'all' && empty($request->fromDate) && empty($request->toDate)){
            if(!in_array($request->status, ['exp_hold','exp_need'])){
                $query->whereNotIn('status',$expStatus);
            }
        }

        if(!empty($request->fromDate)){
            $fromDate = date('Y-m-d', strtotime($request->fromDate));
            $query->where('created_at', '>=' ,$fromDate." 00:00:00");
        }

        if(!empty($request->toDate)){
            $toDate = date('Y-m-d', strtotime($request->toDate));
            $query->where('created_at', '<=' ,$toDate." 23:59:59");
        }

        if(!empty($request->job_title)){
            $query->where('job_title', 'like', '%'.$request->job_title.'%');
        }

        if(!empty($request->bdm)){
            $query->where('user_id', $request->bdm);
        }

        if(!empty($request->job_id)){
            $query->where('job_id', $request->job_id);
        }

        if(!empty($request->client)){
            $query->where('client_name', 'like', '%'.$request->client.'%');
        }

        if(!empty($request->job_location)){
            $query->where('location', 'like', '%'.$request->job_location.'%');
        }

        if(!empty($request->moi)){
            $query->where('moi', 'like', '%,'.$request->moi.',%');
        }

        if(!empty($request->work_type)){
            $query->where('work_type', $request->work_type);
        }

        if(!empty($request->category)){
            $query->where('category', $request->category);
        }

        if(!empty($request->visa)){
            $query->where('visa', 'like', '%,'.$request->visa.',%');
        }

        if(!empty($request->recruiter)){
            $query->where('recruiter', 'like', '%,'.$request->recruiter.',%');
            // $recruiterReqId = $this->getRequirementIdsBasedOnFilterData('recruiter', $request->recruiter, $request);
            // $requirementIds[] = $recruiterReqId;
        }

        if(!empty($request->served)){
            $data = $this->getRequirementIdBasedOnServedOptions(strtolower($request->served),$query, $request);
            if(isset($data['type']) && $data['type'] == 'where_in'){
                if(isset($data['requirement_id'])){
                    $requirementIds[] = $data['requirement_id'];
                }
            }
        }

        if(!empty($request->status)){
            $query->where('status', $request->status);
        }

        if(!empty($request->pv_email)){
            $query->where('poc_email', $request->pv_email);
        }

        if(!empty($request->pv_company)){
            $query->where('pv_company_name', $request->pv_company);
        }

        if(!empty($request->pv_name)){
            $query->where('poc_name', $request->pv_name);
        }

        if(!empty($request->pv_phone)){
            $query->where('poc_phone_number', $request->pv_phone);
        }

        if(!empty($request->requirement_type)){
            if($request->requirement_type == 'repost'){
                $query->where('id' ,'!=', \DB::raw('parent_requirement_id'))->where('parent_requirement_id', '!=', '0');
            } elseif($request->requirement_type == 'original') {
                $query->where(function ($query) {
                    $query->where('id' ,'=', \DB::raw('parent_requirement_id'));
                    $query->orwhere('parent_requirement_id', '=', '0');
                });
            }
        }

        if(!empty($request->filter_employer_name)){
            $employerNameReqId = $this->getRequirementIdsBasedOnFilterData('employer_name',strtolower($request->filter_employer_name), $request);
            $requirementIds[] = $employerNameReqId;
        }

        if(!empty($request->filter_employee_name)){
            $employeeNameReqId = $this->getRequirementIdsBasedOnFilterData('employee_name',strtolower($request->filter_employee_name), $request);
            $requirementIds[] = $employeeNameReqId;
        }

        if(!empty($request->filter_employee_phone_number)){
            $employeePhoneReqId = $this->getRequirementIdsBasedOnFilterData('employee_phone',strtolower($request->filter_employee_phone_number), $request);
            $requirementIds[] = $employeePhoneReqId;
        }

        if(!empty($request->filter_employee_email)){
            $employeeEmailReqId = $this->getRequirementIdsBasedOnFilterData('employee_email',strtolower($request->filter_employee_email), $request);
            $requirementIds[] = $employeeEmailReqId;
        }

        if(!empty($request->bdm_feedback)){
            $bdmStatusReqId = $this->getRequirementIdsBasedOnFilterData('status', $request->bdm_feedback, $request);
            $requirementIds[] = $bdmStatusReqId;
        }

        if(!empty($request->pv_feedback)){
            $pvStatusReqId = $this->getRequirementIdsBasedOnFilterData('pv_status', $request->pv_feedback, $request);
            $requirementIds[] = $pvStatusReqId;
        }

        if(!empty($request->client_feedback)){
            $clientFeedbackReqId = $this->getRequirementIdsBasedOnFilterData('client_feedback',$request->client_feedback, $request);
            $requirementIds[] = $clientFeedbackReqId;
        }

        if(!empty($request->candidate_name)){
            $clientNameReqId = $this->getRequirementIdsBasedOnFilterData('name', $request->candidate_name, $request, 'like');
            $requirementIds[] = $clientNameReqId;
        }

        if(!empty($request->candidate_id)){
            $clienteIdReqId = $this->getRequirementIdsBasedOnFilterData('candidate_id', $request->candidate_id, $request);
            $requirementIds[] = $clienteIdReqId;
        }

        if($requirementIds && count($requirementIds)){
            $commonRequirementIds = call_user_func_array('array_intersect', $requirementIds);
            if($commonRequirementIds && count($commonRequirementIds)){
                $query->whereIn('id', $commonRequirementIds);
            } else {
                $query->where('id', 0);
            }
        }

        if(!empty($request->show_merge) && $request->show_merge == 1){
            return $query->orderBy('parent_requirement_id', 'DESC')->orderBy('id', 'desc');
        }

        return $query->orderBy('id', 'desc');
    }

    public function getRequirementIdBasedOnServedOptions($served, $query, $request){
        $submissionModel = new Submission();

        if($served == $submissionModel::STATUS_SERVED_BY_ME){
            $query->where('recruiter', 'like', '%,'.Auth::user()->id.',%');
            $userId = Auth::user()->id;
            $requirementIds =  Requirement::whereHas('submissions', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })->pluck('id')->toArray();

            $data['type'] = 'where_in';
            $data['requirement_id'] = array_unique($requirementIds);

            return $data;
        }
        if($served == $submissionModel::STATUS_ALLOCATED_BY_ME) {
            $query->where('recruiter', 'like', '%,'.Auth::user()->id.',%');

            $data['type'] = 'not_consider';

            return $data;
        }
        if($served == $submissionModel::STATUS_ALLOCATED_BY_ME_BUT_NOT_SERVED_BY_ME){
            $query->where('recruiter', 'like', '%,'.Auth::user()->id.',%');
            $userId = Auth::user()->id;
            $requirementForCurrentUsersIds =  Requirement::whereHas('submissions', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })->pluck('id')->toArray();
            $requirementIds =  Requirement::whereHas('submissions', function ($query) use ($userId) {
                $query->where('user_id', '!=', $userId);
            })->pluck('id')->toArray();
            $requirementIdsNotHavingSubmissions = Requirement::doesntHave('submissions')->pluck('id')->toArray();

            $data['type'] = 'where_in';
            $data['requirement_id'] = array_unique(array_merge(array_diff($requirementIds, $requirementForCurrentUsersIds), $requirementIdsNotHavingSubmissions));

            return $data;
        }
        if($served == $submissionModel::STATUS_ALLOCATED_BY_ME_BUT_NOT_SERVED_BY_ANYONE){
            $query->where('recruiter', 'like', '%,'.Auth::user()->id.',%');

            $data['type'] = 'where_in';
            $data['requirement_id'] = array_unique(Requirement::doesntHave('submissions')->pluck('id')->toArray());

            return $data;
        }


        if($served == $submissionModel::STATUS_SERVED){
            $data = [];
            if(Auth::user()->role == 'admin'){
                $recruiter = !empty($request->recruiter) ? $request->recruiter : 0;
                if($recruiter){
                    $query->where('recruiter', 'like', '%,'.$recruiter.',%');
                    $requirementIds =  Requirement::whereHas('submissions', function ($query) use ($recruiter) {
                        $query->where('user_id', $recruiter);
                    })->pluck('id')->toArray();

                    $data['type'] = 'where_in';
                    $data['requirement_id'] = array_unique($requirementIds);
                }
            } else {
                $query->whereNotNull('recruiter');
                $requirementIds =   Requirement::has('submissions')->pluck('id')->toArray();

                $data['type'] = 'where_in';
                $data['requirement_id'] = array_unique($requirementIds);
            }

            return $data;
        }

        if($served == $submissionModel::STATUS_UNSERVED) {
            $data = [];
            if(Auth::user()->role == 'admin'){
                $recruiter = !empty($request->recruiter) ? $request->recruiter : 0;
                if($recruiter){
                    $requirementIds = Requirement::doesntHave('submissions')->pluck('id')->toArray();
                    $data['type'] = 'where_in';
                    $data['requirement_id'] = array_unique($requirementIds);
                }
            } else {
                $requirementIds =   Requirement::doesntHave('submissions')->pluck('id')->toArray();

                $data['type'] = 'where_in';
                $data['requirement_id'] = array_unique($requirementIds);
            }
            return $data;
        }

        if($served == $submissionModel::STATUS_ALLOCATED) {
            $data = [];
            if(Auth::user()->role == 'admin'){
                $recruiter = !empty($request->recruiter) ? $request->recruiter : 0;
                if($recruiter){
                    // $query->where('recruiter', 'like', '%,'.$recruiter.',%');
                    $data['type'] = 'not_consider';
                }
                return $data;
            } else {
                $query->whereNotNull('recruiter');

                $data['type'] = 'not_consider';
            }
            return $data;
        }

        if($served == $submissionModel::STATUS_NOT_ALLOCATED) {
            $data = [];
            if(Auth::user()->role == 'admin'){
                $recruiter = !empty($request->recruiter) ? $request->recruiter : 0;
                if($recruiter){
                    $conditionToRemove = ['type' => 'Basic', 'column' => 'recruiter', 'operator' => 'like', 'value' => '%,'.$recruiter.',%'];

                    $query = tap($query, function ($query) use ($conditionToRemove) {
                        $queryBuilder = $query->getQuery();
                        $wheres = $queryBuilder->wheres;

                        foreach ($wheres as $key => $where) {
                            if ($where === $conditionToRemove) {
                                unset($wheres[$key]);
                                break;
                            }
                        }
                        $queryBuilder->wheres = array_values($wheres);
                    });

                    $query->where('recruiter', 'not like', '%,'.$recruiter.',%');
                    $data['type'] = 'not_consider';
                }
            } else {
                $query->whereNull('recruiter');

                $data['type'] = 'not_consider';
            }
            return $data;
        }

        if($served == $submissionModel::STATUS_ALLOCATED_BUT_NOT_SERVED) {
            $data = [];
            if(Auth::user()->role == 'admin') {
                $recruiter = !empty($request->recruiter) ? $request->recruiter : 0;
                if($recruiter){
                    $query->where('recruiter', 'like', '%,'.$recruiter.',%');
                    $requirementForCurrentUsersIds =  Requirement::whereHas('submissions', function ($query) use ($recruiter) {
                        $query->where('user_id', $recruiter);
                    })->pluck('id')->toArray();
                    $requirementIds =  Requirement::whereHas('submissions', function ($query) use ($recruiter) {
                        $query->where('user_id', '!=', $recruiter);
                    })->pluck('id')->toArray();
                    $requirementIdsNotHavingSubmissions = Requirement::doesntHave('submissions')->pluck('id')->toArray();

                    $data['type'] = 'where_in';
                    $data['requirement_id'] = array_unique(array_merge(array_diff($requirementIds, $requirementForCurrentUsersIds), $requirementIdsNotHavingSubmissions));
                }
            } else {
                $query->whereNotNull('recruiter');
                $requirementIds =   Requirement::doesntHave('submissions')->pluck('id')->toArray();

                $data['type'] = 'where_in';
                $data['requirement_id'] = array_unique($requirementIds);
            }
            return $data;
        }

        return [];
    }

    public function getRequirementIdsBasedOnFilterData($columnName, $value, $request, $operator = '') {
        if(!$columnName || !$value){
            return $this;
        }

        $requiremrntIdsHavingSubmission = [];

        // if($columnName == 'recruiter'){
        //     return Submission::where('user_id', $value)->pluck('requirement_id')->toArray();
        // }

        if(in_array(strtolower($columnName), ['status', 'pv_status'])){
            $submissions = Submission::query();
            if(strtolower($columnName) == 'status'){
                $bdmFeedBack = $value;
                $isOrWhere = 0;
                $isWhere = 0;
                $isStatus = 0;

                if(in_array('no_updates', $bdmFeedBack)){
                    $bdmFeedBack = array_flip($bdmFeedBack);
                    unset($bdmFeedBack['no_updates']);
                    $bdmFeedBack = array_flip($bdmFeedBack);
                    $isWhere = 1;
                }

                if(in_array('no_viewed', $bdmFeedBack)){
                    $bdmFeedBack = array_flip($bdmFeedBack);
                    unset($bdmFeedBack['no_viewed']);
                    $bdmFeedBack = array_flip($bdmFeedBack);

                    if($bdmFeedBack && count($bdmFeedBack)){
                        $isStatus = 1;
                    } else {
                        $isOrWhere = 1;
                    }
                }

                $submissions->where(function ($submissions) use ($isWhere, $isStatus, $isOrWhere, $bdmFeedBack) {
                    if($isWhere == 1){
                        $submissions->whereNull('pv_status');
                        $submissions->Where('is_show', 1);
                        $submissions->Where('status', 'pending');
                        if($isStatus == 0 && $bdmFeedBack && count($bdmFeedBack)){
                            $submissions->orWhereIn('status', $bdmFeedBack);
                        }
                    }
                    if($isOrWhere == 1){
                        $submissions->orWhere('is_show', 0);
                    }
                    if($isStatus == 1){
                        $submissions->orwhere(function ($submissions) use ($isWhere, $isStatus, $isOrWhere, $bdmFeedBack) {
                            $submissions->whereIn('status', $bdmFeedBack);
                            $submissions->orWhere('is_show', 0);
                        });
                    }
                });

                if(!in_array('no_updates', $value) && !in_array('no_viewed', $value)){
                    $submissions->whereIn('status', $value);
                }
            } else {
                $submissions->whereIn($columnName, $value);
            }

            if(Auth::user()->role == 'recruiter'){
                if(isset($request->authId) && $request->authId > 0){
                    $requiremrntIdsHavingSubmission = $submissions->where('user_id', $request->authId)->pluck('requirement_id')->toArray();
                } else {
                    $requiremrntIdsHavingSubmission = $submissions->pluck('requirement_id')->toArray();
                }
            } else if(Auth::user()->role == 'bdm'){
                $requirementIds = Requirement::where('user_id', Auth::user()->id)->pluck('id')->toArray();

                if($requirementIds && count($requirementIds)){
                    $submissions->whereIn('requirement_id', $requirementIds);
                }else{
                    $submissions->where('requirement_id', 0);
                }

                $requiremrntIdsHavingSubmission = $submissions->pluck('requirement_id')->toArray();
            } else {
                $requiremrntIdsHavingSubmission = $submissions->pluck('requirement_id')->toArray();
            }

            $requiremrntIdsHavingSubmission = array_unique($requiremrntIdsHavingSubmission);
        } else if(strtolower($columnName) == 'client_feedback'){
            $submissionId = Interview::whereIn('status', $value)->pluck('submission_id')->toArray();
            if(!$submissionId && !count($submissionId)){
                $requiremrntIdsHavingSubmission = [];
            } else {
                $submissions = Submission::whereIn('id', $submissionId);
                if(Auth::user()->role == 'recruiter'){
                    if(isset($request->authId) && $request->authId > 0){
                        $requiremrntIdsHavingSubmission = $submissions->where('user_id', $request->authId)->pluck('requirement_id')->toArray();
                    } else {
                        $requiremrntIdsHavingSubmission = $submissions->pluck('requirement_id')->toArray();
                    }
                } else if(Auth::user()->role == 'bdm') {
                    $requirementIds = Requirement::where('user_id', Auth::user()->id)->pluck('id')->toArray();

                    if($requirementIds && count($requirementIds)){
                        $submissions->whereIn('requirement_id', $requirementIds);
                    }else{
                        $submissions->where('requirement_id', 0);
                    }

                    $requiremrntIdsHavingSubmission = $submissions->pluck('requirement_id')->toArray();
                } else {
                    $requiremrntIdsHavingSubmission = $submissions->pluck('requirement_id')->toArray();
                }
            }
        } else {
            if(isset($request->authId) && $request->authId > 0){
                if($operator == 'like'){
                    $requiremrntIdsHavingSubmission = Submission::where($columnName, 'like', '%'.$value.'%')->where('user_id', $request->authId)->pluck('requirement_id')->toArray();
                } else {
                    $requiremrntIdsHavingSubmission = Submission::where($columnName, $value)->where('user_id', $request->authId)->pluck('requirement_id')->toArray();
                }
            } else {
                if($operator == 'like'){
                    $requiremrntIdsHavingSubmission = Submission::where($columnName, 'like', '%'.$value.'%')->pluck('requirement_id')->toArray();
                } else {
                    $requiremrntIdsHavingSubmission = Submission::where($columnName, $value)->pluck('requirement_id')->toArray();
                }
            }
        }

        return $requiremrntIdsHavingSubmission;
    }

    public function submissionFilter($request,$id){
        $whereInfo = [];

        $user = $this->getUser();

        if($user['role'] == 'admin'){
            $query = Submission::where('requirement_id',$id)->select();
        }else{
            $query = Submission::where('user_id',$user['id'])->where('requirement_id',$id)->select();
        }

        if(!empty($request->candidateId)){
            $whereInfo[] = ['id', $request->candidateId];
        }

        if(!empty($request->candidateEmail)){
            $whereInfo[] = ['email', 'like', '%'.$request->candidateEmail.'%'];
        }

        return $query->where($whereInfo);
    }

    public function getCandidateHtml($submissions, $row, $page = 'requirement'): string
    {
        $loggedInUserId = $this->getCurrentUserId();
        $userRole = $this->getCurrentUserRole();
        $candidate = '';
        $linkData = '';
        $submissionModel = new Submission();
        $interviewModel  = new Interview();
        $requirementCreatedAt = $row->created_at;

        foreach ($submissions as $submission){
            $textColor = '';
            $css = '';
            $divClass = 'a-center pt-2 pl-2 pb-2 pr-2 mt-2 ';
            $divCss = '';
            $userId = $row->user_id;
            if($page == 'submission') {
                $userId = $submission->user_id;
            }

            if($page == 'my_submission'){
                $userId = $submission->requirement->user_id;
            }

            $isSamePvCandidate = $this->isSamePvCandidate($submission->email, $submission->requirement_id, $submission->id);
            $otherCandidate = 'other-candidate';
            if($loggedInUserId == $userId || $userRole == 'admin'){
                $otherCandidate = '';
                if($submission->is_show == 0){
                    $textColor = 'text-primary';
                    $divClass .= 'border border-primary';
                } else{
                    $interviewStatus = $this->getInterviewStatus($submission->id, $row->job_id);
                    if($interviewStatus){
                        $divCss = "width: fit-content;";
                        if($interviewStatus == $interviewModel::STATUS_SCHEDULED){
                            $divClass .= 'border border-warning rounded-pill';
                            $textColor = 'text-dark';
                        } else if($interviewStatus == $interviewModel::STATUS_RE_SCHEDULED){
                            $divClass .= 'border-warning-10 rounded-pill';
                            $textColor = 'text-dark';
                        } else if(in_array($interviewStatus, [$interviewModel::STATUS_SELECTED_FOR_NEXT_ROUND,$interviewModel::STATUS_WAITING_FEEDBACK])){
                            $divClass .= 'bg-warning rounded-pill';
                            $textColor = 'text-dark';
                        } else if($interviewStatus == $interviewModel::STATUS_CONFIRMED_POSITION){
                            $divClass .= 'bg-success';
                            $textColor = 'text-dark';
                        } else if($interviewStatus == $interviewModel::STATUS_REJECTED){
                            $divClass .= 'bg-danger';
                            $textColor = 'text-white';
                        } else if($interviewStatus == $interviewModel::STATUS_BACKOUT){
                            $divClass .= 'bg-dark';
                            $textColor = 'text-white';
                        }
                    } else if(!empty($submission->pv_status) && $submission->pv_status){
                        if(in_array($submission->pv_status, [$submissionModel::STATUS_NO_RESPONSE_FROM_PV, $submissionModel::STATUS_POSITION_CLOSED])){
                            $css = "border-bottom: solid;";
                            $textColor = 'text-secondary';
                        } else if($submission->pv_status == $submissionModel::STATUS_SUBMITTED_TO_END_CLIENT){
                            $css = "border-bottom: solid;";
                            $textColor = 'text-success test';
                        }else if($submission->pv_status == $submissionModel::STATUS_REJECTED_BY_END_CLIENT){
                            $css = "border-bottom: 6px double;";
                            $textColor = 'text-danger';
                        }else if($submission->pv_status == $submissionModel::STATUS_REJECTED_BY_PV){
                            $textColor = 'text-danger';
                            $css = "border-bottom: solid;";
                        }
                    } else {
                        if($submission->status == $submissionModel::STATUS_REJECTED){
                            $textColor = 'text-danger';
                        } elseif($submission->status == $submissionModel::STATUS_ACCEPT){
                            $textColor = 'text-success';
                        } elseif($submission->is_show == 1) {
                            $textColor = 'text-primary';
                        }
                    }
                }
            }
            $nameArray          = explode(" ",$submission->name);
            $candidateFirstName = isset($nameArray[0]) ? $nameArray[0] : '';
            $candidateLastDate  = ($this->getCandidateLastStatusUpdatedAt($submission)) ? date('m/d h:i A', strtotime($this->getCandidateLastStatusUpdatedAt($submission))) : '';
            $candidateCount     = $this->getCandidateCountByEmail($submission->email);
//            $latestJobIdOfMatchPvCompany = $this->getLatestJobIdOfMatchPvCompany($submission->email);
            $isCandidateHasLog  = $this->isCandidateHasLog($submission);
            $isEmployerNameChanged = $this->isEmployerNameChanged($submission->candidate_id);
            $timeSpan = $this->getSubmissionTimeSpan($requirementCreatedAt, $submission->created_at);

            if($userRole == 'admin'){
                $linkData = '';
                if($this->isLinkSubmission($submission->employee_email)){
                    $linkData .= '<div class="border text-center ml-5 text-light link-data" style="background-color:rgb(172, 91, 173); width: 40px; display:none">Link</div>';
                }
            }

            if($loggedInUserId == $userId && $userRole == 'recruiter'){
                $candidate .=
                    "<div class='$otherCandidate'>"
                        .(($candidateCount) ? "<span class='badge bg-indigo position-absolute top-0 start-100 translate-middle'>$candidateCount</span>" : "")
                        .(($isCandidateHasLog) ? "<span class='badge badge-pill badge-primary ml-4 position-absolute top-0 start-100 translate-middle'>L</span>" : "")
                        .(($isEmployerNameChanged) ? "<span class='badge bg-red ml-5'>2 Emp</span>" : "")
                        .'<div class="'.$divClass.'" style="'.$divCss.'">
                            <span class="candidate '.$textColor.' candidate-'.$submission->id.'" id="candidate-'.$submission->id.'" style="'.$css.'" data-cid="'.$submission->id.'">'
                            .($isSamePvCandidate ? "<i class='fa fa-info'></i>  ": "").$candidateFirstName.'-'.$submission->candidate_id.'</span>
                        </div>
                        <div class="p-1 ml-2 mt-1 border border-dark" style="width: fit-content;">
                            <span class="text-secondary font-weight-bold">'.$timeSpan.'</span>
                        </div>
                        <span style="color:#AC5BAD; font-weight:bold; display:none" class="submission-date ml-2">'.$candidateLastDate.'</span>
                    </div><br>';
            } else {
                if(($loggedInUserId == $userId && $userRole == 'bdm') || $userRole == 'admin'){
                    $class = 'candidate';
                } else {
                    $class = '';
                }
                $candidate .=
                    "<div class='$otherCandidate'>"
                        .(($candidateCount) ? "<span class='badge bg-indigo position-absolute top-0 start-100 translate-middle'>$candidateCount</span>" : "")
                        .(($isCandidateHasLog) ? "<span class='badge badge-pill badge-primary ml-4 position-absolute top-0 start-100 translate-middle'>L</span>" : "")
                        .$linkData.(($isEmployerNameChanged) ? "<span class='badge bg-red ml-5'>2 Emp</span>" : "")
                        .'<div class="'.$divClass.'" style="'.$divCss.'">
                            <span class="'.$class.' '.$textColor.' candidate-'.$submission->id.'" id="candidate-'.$submission->id.'" style="'.$css.'" data-cid="'.$submission->id.'">'
                            .($isSamePvCandidate ? "<i class='fa fa-info'></i> " :"").$candidateFirstName.'-'.$submission->candidate_id.'</span>
                        </div>
                        <div class="p-1 ml-2 mt-1 border border-dark" style="width: fit-content;">
                            <span class="text-secondary font-weight-bold">'.$timeSpan.'</span>
                        </div>
                        <span style="color:#AC5BAD; font-weight:bold; display:none" class="submission-date ml-2">'.$candidateLastDate.'</span>
                    </div><br>';
            }
        }
        return $candidate;
    }

    public function getCandidateCss($submission,$checkUser = false) {
        $userId = $submission->requirement->user_id;
        $user = Auth::user();
        $submissionModel = new Submission();
        $interviewModel  = new Interview();

        if($user->id == $userId || $user->role == 'admin' || $checkUser){
            if($submission->is_show == 0){
                return 'border border-primary';
            } else {
                $interviewStatus = $this->getInterviewStatus($submission->id, $submission->Requirement->job_id);
                if($interviewStatus){
                    //$divCss .= "width: fit-content;";
                    if($interviewStatus == $interviewModel::STATUS_SCHEDULED){
                        return 'border border-warning rounded-pill';
                    } else if($interviewStatus == $interviewModel::STATUS_RE_SCHEDULED){
                        return 'border-warning-10 rounded-pill';
                    } else if(in_array($interviewStatus, [$interviewModel::STATUS_SELECTED_FOR_NEXT_ROUND, $interviewModel::STATUS_WAITING_FEEDBACK])){
                        return 'bg-warning rounded-pill';
                    } else if($interviewStatus == $interviewModel::STATUS_CONFIRMED_POSITION){
                        return 'bg-success';
                    } else if($interviewStatus == $interviewModel::STATUS_REJECTED){
                        return 'bg-danger';
                    } else if($interviewStatus == $interviewModel::STATUS_BACKOUT){
                        return 'bg-dark';
                    }
                } else if(in_array($submission->pv_status, [$submissionModel::STATUS_NO_RESPONSE_FROM_PV, $submissionModel::STATUS_POSITION_CLOSED])){
                    return "solid;";
                } else if($submission->pv_status == $submissionModel::STATUS_SUBMITTED_TO_END_CLIENT){
                    return "solid;";
                }else if($submission->pv_status == $submissionModel::STATUS_REJECTED_BY_END_CLIENT){
                    return "6px double;";
                }else if($submission->pv_status == $submissionModel::STATUS_REJECTED_BY_PV){
                    return "solid;";
                }
            }
        }
        return '';
    }

    public function getCandidateBorderCss($submission){
        $interviewStatus = $this->getInterviewStatus($submission->id, $submission->Requirement->job_id);
        if(!$interviewStatus && $submission->is_show == 1 && !empty($submission->pv_status) && $submission->pv_status){
            $submissionModel = new Submission();
            if(in_array($submission->pv_status, [$submissionModel::STATUS_NO_RESPONSE_FROM_PV, $submissionModel::STATUS_POSITION_CLOSED])){
                return "border-bottom: solid;";
            } else if($submission->pv_status == $submissionModel::STATUS_SUBMITTED_TO_END_CLIENT){
                return "border-bottom: solid;";
            }else if($submission->pv_status == $submissionModel::STATUS_REJECTED_BY_END_CLIENT){
                return "border-bottom: 6px double;";
            }else if($submission->pv_status == $submissionModel::STATUS_REJECTED_BY_PV){
                return "border-bottom: solid;";
            }
        }
        return '';
    }

    public function getCandidateClass($submission,$checkUser = false) {
        $userId = $submission->requirement->user_id;
        $user = Auth::user();
        $submissionModel = new Submission();
        $interviewModel  = new Interview();

        if($user->id == $userId || $user->role == 'admin' || $checkUser){
            if($submission->is_show == 0){
                return 'text-primary';
            } else {
                $interviewStatus = $this->getInterviewStatus($submission->id, $submission->Requirement->job_id);
                if($interviewStatus){
                    if(in_array($interviewStatus, [$interviewModel::STATUS_SCHEDULED, $interviewModel::STATUS_RE_SCHEDULED, $interviewModel::STATUS_SELECTED_FOR_NEXT_ROUND,                    $interviewModel::STATUS_WAITING_FEEDBACK,                    $interviewModel::STATUS_CONFIRMED_POSITION])){
                        return 'text-dark';
                    } else if(in_array($interviewStatus, [$interviewModel::STATUS_REJECTED, $interviewModel::STATUS_BACKOUT])){
                        return 'text-white';
                    }
                } else if(!empty($submission->pv_status) && $submission->pv_status){
                    if(in_array($submission->pv_status, [$submissionModel::STATUS_NO_RESPONSE_FROM_PV, $submissionModel::STATUS_POSITION_CLOSED])){
                        return 'text-secondary';
                    } else if($submission->pv_status == $submissionModel::STATUS_SUBMITTED_TO_END_CLIENT){
                        return 'text-success test';
                    }else if($submission->pv_status == $submissionModel::STATUS_REJECTED_BY_END_CLIENT){
                        return 'text-danger';
                    }else if($submission->pv_status == $submissionModel::STATUS_REJECTED_BY_PV){
                        return 'text-danger seconda';
                    }
                } else {
                    if($submission->status == $submissionModel::STATUS_REJECTED){
                        return 'text-danger';
                    } elseif($submission->status == $submissionModel::STATUS_ACCEPT){
                        return 'text-success';
                    } elseif($submission->is_show == 1) {
                        return 'text-primary';
                    }
                }
            }
        }
        return '';
    }

    public function getInterviewStatus($submissionId, $jobId) {
        if(!$jobId || !$submissionId){
            return '';
        }

        $statusData = Interview::where('submission_id', $submissionId)->where('job_id', $jobId)->first(['status']);
        if(!$statusData || !$statusData->status){
            return '';
        }

        return $statusData->status;
    }

    public function getCandidateLastStatusUpdatedAt($submission){
        $submissionId = $submission->id;

        if(!$submissionId){
            return '';
        }

        $statuslastUpdatedAt =  EntityHistory::whereIn('entity_type',[EntityHistory::ENTITY_TYPE_PV_STATUS,EntityHistory::ENTITY_TYPE_BDM_STATUS])->where('submission_id',$submissionId)->orderBy('id','DESC')->first(['created_at']);
        if(empty($statuslastUpdatedAt) || !$statuslastUpdatedAt->created_at){
            return '';
        }
        return $statuslastUpdatedAt->created_at;
    }

    public function getSettingData() {
        return Setting::pluck('value','name')->toArray();
    }

    public function isSamePvCandidate($submissionEmail, $requirementId, $submissionId = 0){
        if(!$submissionEmail || !$requirementId){
            return 0;
        }

        if($submissionId){
            $requirementIdsWithCurrentEmail = Submission::select('requirement_id')->where('email', $submissionEmail)->where('id','!=',$submissionId)->pluck('requirement_id')->toArray();
        } else {
            $requirementIdsWithCurrentEmail = Submission::select('requirement_id')->where('email', $submissionEmail)->pluck('requirement_id')->toArray();
        }

        if(!$requirementIdsWithCurrentEmail || !count($requirementIdsWithCurrentEmail)){
            return 0;
        }

        $currentRequirement = Requirement::select('pv_company_name')->where('id', $requirementId)->first();
        if(!$currentRequirement){
            return 0;
        }
        $currentRequirementPvCompany = $currentRequirement->pv_company_name;

        $samePvCompanyCandidate = Requirement::select('id')->whereIn('id',$requirementIdsWithCurrentEmail)->where('pv_company_name',$currentRequirementPvCompany)->first();

        if(empty($samePvCompanyCandidate)){
            return 0;
        }

        return 1;
    }

    public function getCandidateCountByEmail($email){
        if(!$email){
            return 0;
        }
        $candidateCount = Submission::where('email', $email)->groupBy('email')->count();

        return $candidateCount > 1 ? $candidateCount : 0;
    }

    public function manageSubmissionLogs($newData, $oldData, $jobId) {
        if(!$newData || !$oldData){
            return $this;
        }

        $differentValues = [];
        $isAllSameData = 0;

        foreach ($newData as $key => $value) {
            if(in_array($key,['created_at','updated_at'])){
                continue;
            }
            if(is_array($value)){
                $value = ','. implode(',',$value).',';
            }
            if($value == 'on'){
                $value = '1';
            }elseif($value == 'off'){
                $value = '0';
            }
            if (isset($oldData[$key]) && strtolower($oldData[$key]) != strtolower($value)) {
                $isAllSameData = 1;
                $differentValues[$key] = $value;

            } else {
                $differentValues[$key] = '';
            }
        }

        if($isAllSameData){
            $this->saveDataLog($oldData->id, DataLog::SECTION_SUBMISSION, $differentValues,$oldData,$jobId);
        }

        return $this;
    }

    public function addNewDataInLog($submission){
        if(!$submission){
            return $this;
        }

        $cloneSubmission = $submission->replicate();

        $this->saveDataLog($submission->id, DataLog::SECTION_SUBMISSION, $submission->toArray(),$submission,$cloneSubmission->Requirement->job_id);
        return $this;
    }

    public function saveDataLog($sectionId,$section,$data,$submission,$jobId){
        $cloneSubmission = $submission->replicate();
        if(!$sectionId || !$section || !$data){
            return $this;
        }

        $inputData['section_id']   = $sectionId;
        $inputData['section']      = $section;
        $inputData['candidate_id'] = $cloneSubmission->candidate_id;
        $inputData['job_id']       = $jobId;
        $inputData['user_id']      = Auth::user()->id;
        $inputData['data']         = json_encode($data);

        DataLog::create($inputData);
        return $this;
    }

    public function getLatestJobIdOfMatchPvCompany($email){
        if(!$email){
            return '';
        }

        $submission = Submission::where('email', $email)->orderBy('created_at','DESC')->first();

        if(!empty($submission) && $submission->Requirement->job_id){
            return $submission->Requirement->job_id;
        }
        return '';
    }

    public function getLogDataByName($submission, $key){
        if(!$submission || !$key || !in_array($key, Submission::$manageLogFileds)){
            return '';
        }

        $allLogData = DataLog::select('data','user_id','job_id')->where('section_id', $submission->id)->where('section', DataLog::SECTION_SUBMISSION)->orderBy('created_at', 'DESC')->get();

        if(empty($allLogData)){
            return '';
        }

        $logData =
            '<br><table class="table table-striped log-data" style="display:none">
                <thead>

                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Latest First <br>Value</th>
                        <th scope="col">JID</th>
                        <th scope="col">Modified By</th>
                        <th scope="col">Date</th>
                    </tr>
                </thead>
                <tbody>';
        $count = 0;
        $isNoLogData = 1;
        foreach($allLogData as $data){
            $allData = json_decode($data->data, 1);
            if(isset($allData[$key]) && $allData[$key]){
                $count++;
                $isNoLogData = 0;
                $logData .=
                    '<tr>
                        <th scope="row">'.$count.'</th>
                        <td>'.$allData[$key].'</td>
                        <td>'.$data->job_id.'</td>
                        <td>'.$data->userDetail->role.' : '.$data->userDetail->name.'</td>
                        <td>'.date('m-d-y',strtotime($data->created_at)).'</td>
                    </tr>';
            }
        }

        if($isNoLogData){
            return '';
        }

        if($count == 1){
            return '';
        }

        $logData .=
                '</tbody>
            </table>';

        return $logData;
    }

    public function isLogData($submission, $key, $manageLogFileds){
        if(!$submission || !$key || !in_array($key, $manageLogFileds)){
            return 0;
        }

        $allLogData = DataLog::select('data')->where('section_id', $submission->id)->where('section', DataLog::SECTION_SUBMISSION)->orderBy('created_at', 'DESC')->get();

        if(empty($allLogData)){
            return 0;
        }

        $count = 0;
        $isNoLogData = 1;
        foreach($allLogData as $data){
            $allData = json_decode($data->data, 1);
            if(isset($allData[$key]) && $allData[$key]){
                $count++;
                $isNoLogData = 0;
            }
        }

        if($isNoLogData){
            return 0;
        }

        if($count == 1){
            return 0;
        }

        return 1;
    }

    public function isCandidateHasLog($submission) {
        if(empty($submission)){
            return 0;
        }

        $manageLogFileds = Submission::$manageLogFileds;

        if(empty($manageLogFileds) || !count($manageLogFileds)){
            return 0;
        }

        $isHasLog = 0;
        foreach ($manageLogFileds as $value) {
            $oldLogData = $this->isLogData($submission, $value, $manageLogFileds);

            if($oldLogData){
                $isHasLog = 1;
                break;
            }
        }

        if($isHasLog){
            return 1;
        }

        return 0;
    }

    public function updateCandidateWithSameCandidateId($submission, $jobId){
        if(empty($submission)){
            return $this;
        }
        $submissionData = $submission->toArray();
        // unset($submissionData['id']);
        // unset($submissionData['requirement_id']);
        // unset($submissionData['user_id']);
        // unset($submissionData['email']);
        // unset($submissionData['common_skills']);
        // unset($submissionData['skills_match']);
        // unset($submissionData['reason']);
        // unset($submissionData['status']);
        // unset($submissionData['pv_status']);
        // unset($submissionData['pv_reason']);
        // unset($submissionData['is_show']);
        // unset($submissionData['deleted_at']);
        // unset($submissionData['created_at']);
        // unset($submissionData['updated_at']);
        $candidateId = $submission->candidate_id;
        if($candidateId){
            $oldSubmissionRows = Submission::where('candidate_id',$candidateId)->where('id', '!=', $submission->id)->get();
            if(!empty($oldSubmissionRows) && $oldSubmissionRows->count()){
                foreach($oldSubmissionRows as $oldSubmissionRow){
                    // Submission::where('id', $oldSubmissionRow->id)->update($submissionData);
                    // $newSubmission = Submission::where('id', $oldSubmissionRow->id)->first();
                    $this->manageSubmissionLogs($submissionData,$oldSubmissionRow, $jobId);
                }
            }
        }
        return $this;
    }

    public function getTooltipHtml($text, $character = 0) {
        if(!$text){
            return '';
        }

        $settingRow =  Setting::where('name', 'tooltip_after_no_of_words')->first();
        $configurationNumberForTooltip = 0;

        if(!empty($settingRow) || !$settingRow->value){
            $configurationNumberForTooltip = $settingRow->value;
        }

        if(!$configurationNumberForTooltip && !$character){
            return $text;
        }

        if($character){
            if(strlen($text) > $character){
                $shortString = substr($text, 0, $character);
                return '<p>' . $shortString . '<span class="custom-tooltip" data-toggle="tooltip" data-placement="bottom" title="'.$text.'">  <i class="fa fa-info-circle"></i></span>';
            }
        }

        if($configurationNumberForTooltip){
            if(strlen($text) > $configurationNumberForTooltip){
                $shortString = substr($text, 0, $configurationNumberForTooltip);
                return '<p>' . $shortString . '<span class="custom-tooltip" data-toggle="tooltip" data-placement="bottom" title="'.$text.'">  <i class="fa fa-info-circle"></i></span>';
            }
        }

        return '<span>'.$text.'</span>';
    }

    public function getEmployeeLinkData($data, $employeeEmail){
        if(!$employeeEmail){
            return [];
        }
        $linkEmployeeEmail = '';
        $linkEmployeePhoneNumber = '';
        $linkPocLocation = '';
        $linkPvCompanyLocation = '';

        $ulStartData = '<ul class="list-group mt-3">';
        $ulEndData = '</ul>';

        $empCompanyData = Admin::where('email',$employeeEmail)->where('role', 'employee')->first();

        if($empCompanyData && $empCompanyData->linked_data){
            $linkedData = json_decode($empCompanyData->linked_data, 1);
            foreach ($linkedData as $key => $linkValue) {
                foreach($linkValue as $values){
                    if($key == 'linking_email'){
                        $linkEmployeeEmail .= ' <li class="list-group-item p-1"><span class="text-primary">'.$values['value'].'</span> ( '.Admin::getUserNameBasedOnId($values['user_id']).' : '.date('m-d-y', strtotime($values['dateTime'])).' ) </li>';
                    }
                    if($key == 'linking_phone'){
                        $linkEmployeePhoneNumber .= ' <li class="list-group-item p-1"><span class="text-primary">'.$values['value'].'</span> ( '.Admin::getUserNameBasedOnId($values['user_id']).' : '.date('m-d-y', strtotime($values['dateTime'])).' ) </li>';
                    }
                }
            }
        }

        $data['linkEmployeeEmail'] = "<div id='linkEmployeeEmail'> $ulStartData  $linkEmployeeEmail  $ulEndData </div>";
        $data['linkEmployeePhoneNumber'] = "<div id='linkEmployeePhoneNumber'> $ulStartData  $linkEmployeePhoneNumber  $ulEndData </div>";

        return $data;
    }

    public function isLinkRequirement($pocEmail)
    {
        return 1;
        if(!$pocEmail){
            return 0;
        }
        $isFound = 0;
        $pvComapny = PVCompany::where('email', $pocEmail)->first();

        if($pvComapny && $pvComapny->linked_data){
            $linkedData = json_decode($pvComapny->linked_data, 1);
            foreach ($linkedData as $key => $linkValue) {
                if($key == 'linking_email'){
                    $isFound = 1;
                    break;
                }
            }
        }
        return $isFound;
    }

    public function isLinkSubmission($empEmail)
    {
        if(!$empEmail){
            return 0;
        }
        $isFound = 0;
        $employee = Admin::select('linked_data')->where('email', $empEmail)->where('role','employee')->first();

        if($employee && $employee->linked_data){
            $linkedData = json_decode($employee->linked_data, 1);
            foreach ($linkedData as $key => $linkValue) {
                if($key == 'linking_email'){
                    $isFound = 1;
                    break;
                }
            }
        }
        return $isFound;
    }

    public function getPvCompanyName()
    {
        $pvCompany = PvCompany::whereNotNull('name');
        if(Auth::user()->role != 'admin'){
            $pvCompany->where('user_id', Auth::user()->id);
        }
        return $pvCompany->groupBy('name')->pluck('name')->toArray();
    }

    public function isEmployerNameChanged($candidateId)
    {
        if(!$candidateId){
            return 0;
        }

        if(isset($this->_candidateIdWiseIsEmployerChanged[$candidateId])){
            return $this->_candidateIdWiseIsEmployerChanged[$candidateId];
        }

        $startDate = \Carbon\Carbon::now()->subDays(90);
        $totalCount =  Submission::select(\DB::raw('COUNT(DISTINCT employer_name) as employer_count'))
            ->where('created_at', '>=', $startDate)
            ->where('candidate_id', $candidateId)
            ->groupBy('candidate_id')
            ->first();

        if($totalCount->employer_count > 1){
            $this->_candidateIdWiseIsEmployerChanged[$candidateId] = 1;
            return 1;
        }

        $this->_candidateIdWiseIsEmployerChanged[$candidateId] = 0;

        return 0;
    }

    public function getAllPvCompanyCount($pvComapnyName)
    {
        if(!$pvComapnyName){
            return 0;
        }

        return Requirement::where('pv_company_name', $pvComapnyName)
            ->where(function ($query) {
                $query->where('id' ,\DB::raw('parent_requirement_id'));
                $query->orwhere('parent_requirement_id', '=', '0');
            })
            ->count();
    }

    public function isNewAsPerConfiguration($columnName, $value)
    {
        if(!$columnName || !$value){
            return 0;
        }

        $newPocCountConfiguration = 0;

        $settingRow =  Setting::where('name', 'heighlight_new_poc_data_days')->first();

        if(!empty($settingRow) || !$settingRow->value){
            $newPocCountConfiguration = $settingRow->value;
        }

        if(!$newPocCountConfiguration){
            return false;
        }

        $requirementRow = Requirement::select('created_at')->where($columnName, $value)
            ->where(function ($query) {
                $query->where('id' ,\DB::raw('parent_requirement_id'));
                $query->orwhere('parent_requirement_id', '=', '0');
            })->first();

        if(empty($requirementRow) || !$requirementRow->created_at){
            return false;
        }

        $createdAtDateAsPerConfiguration = \Carbon\Carbon::now()->subDays($newPocCountConfiguration);

        if($requirementRow->created_at >= $createdAtDateAsPerConfiguration){
            return true;
        }

        return false;
    }

    public function getTotalOrigReqBasedOnPocData($pocName, $isTotal = 0)
    {
        if(!$pocName){
            return 0;
        }

        if($isTotal){
            return Requirement::where('poc_name', $pocName)
            ->where(function ($query) {
                $query->where('id' ,\DB::raw('parent_requirement_id'));
                $query->orwhere('parent_requirement_id', '=', '0');
            })->count();
        }

        $newPocCountConfiguration = 0;

        $settingRow =  Setting::where('name', 'show_poc_count_days')->first();

        if(!empty($settingRow) && $settingRow->value){
            $newPocCountConfiguration = $settingRow->value;
        }

        if(!$newPocCountConfiguration){
            return 0;
        }

        $createdAtDateAsPerConfiguration = \Carbon\Carbon::now()->subDays($newPocCountConfiguration);

        return Requirement::where('poc_name', $pocName)
            ->where('created_at', '>=', $createdAtDateAsPerConfiguration)
            ->where(function ($query) {
                $query->where('id' ,\DB::raw('parent_requirement_id'));
                $query->orwhere('parent_requirement_id', '=', '0');
            })->count();
    }

    public function getUserWiseRequirementsCountAsPerPoc($pocName, $isTotal = 0)
    {
        if(!$pocName){
            return '';
        }

        if($isTotal){
            $userIdWiseRequirementCount = Requirement::where('poc_name', $pocName)
            ->groupBy('user_id')
            ->selectRaw('user_id, COUNT(*) as count')
            ->where(function ($query) {
                $query->where('id' ,\DB::raw('parent_requirement_id'));
                $query->orwhere('parent_requirement_id', '=', '0');
            })
            ->pluck('count', 'user_id')
            ->toArray();
        } else {
            $newPocCountConfiguration = 0;

            $settingRow =  Setting::where('name', 'show_poc_count_days')->first();

            if(!empty($settingRow) || !$settingRow->value){
                $newPocCountConfiguration = $settingRow->value;
            }

            if(!$newPocCountConfiguration){
                return 0;
            }

            $createdAtDateAsPerConfiguration = \Carbon\Carbon::now()->subDays($newPocCountConfiguration);

            $userIdWiseRequirementCount = Requirement::where('poc_name', $pocName)
                ->groupBy('user_id')
                ->selectRaw('user_id, COUNT(*) as count')
                ->where('created_at', '>=', $createdAtDateAsPerConfiguration)
                ->where(function ($query) {
                    $query->where('id' ,\DB::raw('parent_requirement_id'));
                    $query->orwhere('parent_requirement_id', '=', '0');
                })
                ->pluck('count', 'user_id')
                ->toArray();
        }

        if(empty($userIdWiseRequirementCount)){
            return '';
        }

        arsort($userIdWiseRequirementCount);

        $requirementCountWiseUserData = [];

        foreach ($userIdWiseRequirementCount as $userId => $count) {
            $requirementCountWiseUserData[] = $this->getUserIdWiseName($userId)."($count)";
        }

        return implode(", ", $requirementCountWiseUserData);
    }

    public function getAllEmpDataCount($columnName, $value)
    {
        if(!$columnName || !$value){
            return 0;
        }

        return Submission::where($columnName, $value)->count();
    }

    public function assignRecruiterToRequirement($requirementId, $recruiters)
    {
        if(!$requirementId){
            return $this;
        }

        if(!$recruiters || !count($recruiters)){
            AssignToRecruiter::where('requirement_id', $requirementId)->delete();
            return  $this;
        }

        foreach ($recruiters as $recruiterId){
            $assignRecruiterRow = AssignToRecruiter::Where('requirement_id', $requirementId)
                ->where('recruiter_id', $recruiterId)->first();
            if(empty($assignRecruiterRow)){
                AssignToRecruiter::create(
                    [
                        'requirement_id' => $requirementId,
                        'recruiter_id' => $recruiterId,
                    ]
                );
            }
        }

        $allAssignedRecruiters = AssignToRecruiter::Where('requirement_id', $requirementId)
            ->pluck('recruiter_id')
            ->toArray();

        if($allAssignedRecruiters && count($allAssignedRecruiters)){
            $recruitersDiffrence = array_diff($allAssignedRecruiters, $recruiters);
            if($recruitersDiffrence && count($recruitersDiffrence)){
                foreach ($recruitersDiffrence as $recruiterId){
                    $rowToRemove = AssignToRecruiter::Where('requirement_id', $requirementId)
                        ->where('recruiter_id', $recruiterId)->first();

                    if ($rowToRemove) {
                        $rowToRemove->delete();
                    }
                }
            }
        }
        return  $this;
    }

    public function getSubmissionTimeSpan($requirementCreateDate, $submissionCreateDate): string
    {
        if(!$requirementCreateDate || !$submissionCreateDate){
            return '';
        }

        $timeSpan = '';
        $submissionCreatedDate  = Carbon::parse($submissionCreateDate);
        $diffInHours   = $requirementCreateDate->diffInHours($submissionCreatedDate);
        $diffInMinutes = $requirementCreateDate->diffInMinutes($submissionCreatedDate) % 60;

        if ($diffInHours >= 24) {
            $diffInDays = floor($diffInHours / 24);
            $diffInHours = $diffInHours % 24;

            $timeSpan = "$diffInDays days, $diffInHours hr : $diffInMinutes mins";
        } else {
            if($diffInHours > 1){
                $timeSpan = "$diffInHours hr:$diffInMinutes mins";
            }else{
                $timeSpan = "$diffInMinutes mins";
            }
        }
        return $timeSpan;
    }

    public function getUserIdWiseName($userId)
    {
        if(!$userId){
            return '';
        }

        if(!$this->_userIdWiseName){
            $this->_userIdWiseName = Admin::getUserIdWiseName();
        }

        return isset($this->_userIdWiseName[$userId]) ? $this->_userIdWiseName[$userId] : '';

    }
}
