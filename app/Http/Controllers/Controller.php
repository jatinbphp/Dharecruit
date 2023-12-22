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

    public function getListHtml($data, $page='requirement',$request) {
        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('job_title', function($row){
                return getJobTitleHtml($row);
            })
            ->addColumn('user_id', function($row){
                return $row->BDM->name;
            })
            ->addColumn('category', function($row){
                return $row->Category->name;
            })
            ->addColumn('recruiter', function($row) use (&$request){
                return getRecruiterHtml($row, $request);
            })
            ->addColumn('status', function($row){
                return getStatusHtml($row);
            })
            ->addColumn('candidate', function ($row) use (&$page, &$request){
                return getCandidateHtml($row, $page, $request);
            })
            ->addColumn('action', function ($row) use (&$page){
                return getActionHtml($row, $page);
            })
            ->addColumn('client', function($row) {
                return getClientHtml($row);
            })
            ->addColumn('job_keyword', function($row) {
                return getJobKeywordHtml($row);
            })
            ->addColumn('job_id', function($row) {
                return getJobIdHtml($row);
            })
            ->addColumn('pv', function($row) {
                return getPvHtml($row);
            })
            ->addColumn('poc', function($row) {
                return getPocHtml($row);
            })
            ->addColumn('total_orig_req', function($row) {
                return getTotalOrigReq($row);
            })
            ->addColumn('total_orig_req_in_days', function($row) {
                return getTotalOrigReqInDays($row);
            })
            ->setRowClass(function ($row) {
                return (($row->parent_requirement_id != 0 && $row->parent_requirement_id == $row->id) ? 'parent-row' : (($row->parent_requirement_id != 0) ? 'child-row' : ''));
                ;
            })
            ->rawColumns(['user_id','category','recruiter','status','candidate','action','client','job_title','job_keyword','job_id','pv','poc','total_orig_req','total_orig_req_in_days'])
            ->make(true);
    }

    public function getUser(){
        return Auth::user();
    }

    public function Filter($request, $page=''){
        $whereInfo = [];
        $user = Auth::user();
        $requirementIds = [];

        $expStatus = [Requirement::STATUS_EXP_HOLD , Requirement::STATUS_EXP_NEED];
        if($user['role'] == 'bdm' && isset($request->authId) && $request->authId > 0){
            $query = Requirement::where('user_id',$request->authId)->select();
        }elseif($user['role'] == 'recruiter' && isset($request->authId) && $request->authId > 0){
            $query = Requirement::whereRaw("find_in_set($request->authId,recruiter)")->select();
        }else{
            $query = Requirement::select();
        }

        // As Per Client Requirement Not Show Expired Req on all page for bdm and req but they can search it.
        if(in_array(Auth::user()->role, ['bdm', 'recruiter']) && $page == 'all'){
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
            return $query->orderBy('parent_requirement_id', 'DESC')->orderBy('id', 'desc')->get();
        }

        return $query->orderBy('id', 'desc')->get();
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
                    $requiremrntIdsHavingSubmission = $submission->pluck('requirement_id')->toArray();
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

    public function getCandidateHtml($submissions, $row, $page = 'requirement') {
        $user = Auth::user();
        $candidate = '';
        $linkData = '';
        $submissionModel = new Submission();
        $interviewModel  = new Interview();

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
            if($user->id == $userId || $user->role == 'admin'){
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
            $latestJobIdOfMatchPvCompany = $this->getLatestJobIdOfMatchPvCompany($submission->email);
            $isCandidateHasLog  = $this->isCandidateHasLog($submission);
            $isEmployerNameChanged = $this->isEmployerNameChanged($submission->candidate_id);

            if($user->role == 'admin'){
                $linkData = '';
                if($this->isLinkSubmission($submission->employee_email)){
                    $linkData .= '<div class="border text-center ml-5 text-light link-data" style="background-color:rgb(172, 91, 173); width: 40px; display:none">Link</div>';
                }
            }

            if($user->id == $userId && $user->role == 'recruiter'){
                $candidate .= "<div class='$otherCandidate'>".(($candidateCount) ? "<span class='badge bg-indigo position-absolute top-0 start-100 translate-middle'>$candidateCount</span>" : "").(($isCandidateHasLog) ? "<span class='badge badge-pill badge-primary ml-4 position-absolute top-0 start-100 translate-middle'>L</span>" : "").(($isEmployerNameChanged) ? "<span class='badge bg-red ml-5'>2 Emp</span>" : "").'<div class="'.$divClass.'" style="'.$divCss.'"><span class="candidate '.$textColor.' candidate-'.$submission->id.'" id="candidate-'.$submission->id.'" style="'.$css.'" data-cid="'.$submission->id.'">'.($isSamePvCandidate ? "<i class='fa fa-info'></i>  ": "").$candidateFirstName.'-'.$submission->candidate_id.'</span></div><span style="color:#AC5BAD; font-weight:bold; display:none" class="submission-date">'.$candidateLastDate.'</span></div><br>';
            } else {
                if(($user->id == $userId && $user->role == 'bdm') || $user->role == 'admin'){
                    $class = 'candidate';
                } else {
                    $class = '';
                }
                $candidate .= "<div class='$otherCandidate'>".(($candidateCount) ? "<span class='badge bg-indigo position-absolute top-0 start-100 translate-middle'>$candidateCount</span>" : "").(($isCandidateHasLog) ? "<span class='badge badge-pill badge-primary ml-4 position-absolute top-0 start-100 translate-middle'>L</span>" : "").$linkData.(($isEmployerNameChanged) ? "<span class='badge bg-red ml-5'>2 Emp</span>" : "").'<div class="'.$divClass.'" style="'.$divCss.'"><span class="'.$class.' '.$textColor.' candidate-'.$submission->id.'" id="candidate-'.$submission->id.'" style="'.$css.'" data-cid="'.$submission->id.'">'.($isSamePvCandidate ? "<i class='fa fa-info'></i> " :"").$candidateFirstName.'-'.$submission->candidate_id.'</span></div><span style="color:#AC5BAD; font-weight:bold; display:none" class="submission-date">'.$candidateLastDate.'</span></div><br>';
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

        $requirementIdsWithCurrentEmail = Submission::where('email', $submissionEmail)->pluck('requirement_id')->toArray();

        if($submissionId){
            $requirementIdsWithCurrentEmail = Submission::where('email', $submissionEmail)->where('id','!=',$submissionId)->pluck('requirement_id')->toArray();
        }

        if(!$requirementIdsWithCurrentEmail || !count($requirementIdsWithCurrentEmail)){
            return 0;
        }

        $currentRequirement = Requirement::where('id', $requirementId)->first();
        if(!$currentRequirement){
            return 0;
        }
        $currentRequirementPvCompany = $currentRequirement->pv_company_name;

        $samePvCompanyCandidate = Requirement::whereIn('id',$requirementIdsWithCurrentEmail)->where('pv_company_name',$currentRequirementPvCompany)->first();

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

        $allLogData = DataLog::where('section_id', $submission->id)->where('section', DataLog::SECTION_SUBMISSION)->orderBy('created_at', 'DESC')->get();

        if(empty($allLogData) || !count($allLogData)){
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
            $oldLogData = $this->getLogDataByName($submission, $value);

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

    public function ExpireRequirement(){
        $settingRow =  Setting::where('name', 'no_of_hours_for_expire')->first();

        if(empty($settingRow) || !$settingRow->value){
            return $this;
        }

        $expHours = $settingRow->value;
        $requirementObj = new Requirement();

        $requirementData = Requirement::
            whereNotIn('status', [$requirementObj::STATUS_EXP_HOLD, $requirementObj::STATUS_EXP_NEED])
            ->whereRaw('TIMESTAMPDIFF(HOUR, created_at, NOW()) >= ?', [$expHours])
            ->get();

        if(empty($requirementData)){
            return $this;
        }

        $logData = [];

        foreach ($requirementData as $requirement) {
            $data = [];

            $data['status'] = ($requirement->status == 'unhold') ? $requirementObj::STATUS_EXP_NEED : $requirementObj::STATUS_EXP_HOLD;

            $requirement->update($data);

            $data['requirement_id'] = $requirement->id;
            $data['created_at'] = $requirement->created_at;
            $logData[$requirement->id] = $data;
        }

        if($logData){
            $inputData['section_id'] = 0;
            $inputData['section']    = 'requirement';
            $inputData['data']       = json_encode($logData);

            DataLog::create($inputData);
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
        $employee = Admin::where('email', $empEmail)->where('role','employee')->first();

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

        $latestCandudateData = Submission::where('candidate_id', $candidateId)->orderBy('id', 'desc')->first();

        if(empty($latestCandudateData) || !$latestCandudateData->id || !$latestCandudateData->employer_name){
            return 0;
        }

        $latestEmployerName = $latestCandudateData->employer_name;

        $startDate = \Carbon\Carbon::now()->subDays(90);

        $recentSubmissions = Submission::where('candidate_id', $candidateId)->where('created_at', '>=', $startDate)->latest()->get();

        if(empty($recentSubmissions)){
            return 0;
        }

        $isEmployeeUpdate = 0;

        foreach ($recentSubmissions as $submission) {
            $currentemployeeName = $submission->employer_name;
            if($latestEmployerName != $currentemployeeName){
                $isEmployeeUpdate = 1;
                break;
            }
        }

        return $isEmployeeUpdate;
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

        $requirementRow = Requirement::where($columnName, $value)
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

        if(!empty($settingRow) || !$settingRow->value){
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
            $requirementCountWiseUserData[] = Admin::getUserNameBasedOnId($userId)."($count)";
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
}
