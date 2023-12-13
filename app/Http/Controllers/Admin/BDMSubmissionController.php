<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use App\Models\Submission;
use App\Models\Requirement;
use App\Models\EntityHistory;
use App\Models\Interview;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BDMSubmissionCOntroller extends Controller
{   
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('accessright:manage_bdm_submission');
        $this->ExpireRequirement();
    }

    public function index(Request $request)
    {
        $data['menu'] = "Manage Submission";
        if ($request->ajax()) {
            // $reqFilterStatus = $request->filter_status;

            // $filterStatus = [];
            // $showUnviewed = 0;
            
            // if(!empty($reqFilterStatus)){
            //     if(in_array('both',$reqFilterStatus)){
            //         $filterStatus[] = 'accepted';
            //         $filterStatus[] = 'rejected';
            //     }
    
            //     if(in_array('accepted',$reqFilterStatus)){
            //         $filterStatus[] = 'accepted';
            //     }
    
            //     if(in_array('rejected',$reqFilterStatus)){
            //         $filterStatus[] = 'rejected';
            //     }
    
            //     if(in_array('pending',$reqFilterStatus)){
            //         $filterStatus[] = 'pending';
            //     }

            //     if(in_array('un_viewed',$reqFilterStatus)){
            //         $showUnviewed = 1;
            //     }
            // }

            $user = Auth::user();
            $requirementIds = [];

            $submissions = Submission::Query();

            if(!empty($request->fromDate)){
                $fromDate = date('Y-m-d', strtotime($request->fromDate));
                $submissions->where('created_at', '>=' ,$fromDate." 00:00:00");
            }
    
            if(!empty($request->toDate)){
                $toDate = date('Y-m-d', strtotime($request->toDate));
                $submissions->where('created_at', '<=' ,$toDate." 23:59:59");
            }

            if(!empty($request->filter_employer_name)){
                $submissions->where('employer_name', 'like', '%'.$request->filter_employer_name.'%');
            }
    
            if(!empty($request->filter_employee_name)){
                $submissions->where('employee_name', 'like', '%'.$request->filter_employee_name.'%');
            }
    
            if(!empty($request->filter_employee_phone_number)){
                $submissions->where('employee_phone', $request->filter_employee_phone_number);
            }
    
            if(!empty($request->filter_employee_email)){
                $submissions->where('employee_email', $request->filter_employee_email);
            }

            if(!empty($request->candidate_name)){
                $submissions->where('name', 'like' , '%'.$request->candidate_name.'%');
            }
    
            if(!empty($request->candidate_id)){
                $submissions->where('candidate_id', $request->candidate_id);
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

                if(!in_array('no_updates', $request->bdm_feedback) && !in_array('no_viewed', $request->bdm_feedback)){
                    $submissions->whereIn('status', $request->bdm_feedback);
                }
            }
    
            if(!empty($request->pv_feedback)){
                $submissions->whereIn('pv_status', $request->pv_feedback);
            }

            if(!empty($request->recruiter)){
                $submissions->where('user_id',$request->recruiter);
            }

            if(!empty($request->pv_email)){
                $pvEmailReqIds = $this->getRequirementIdBasedOnData('poc_email', $request->pv_email);
                $requirementIds[] = $pvEmailReqIds;
            }
    
            if(!empty($request->pv_company)){
                $pvCompanyReqIds = $this->getRequirementIdBasedOnData('pv_company_name', $request->pv_company, 'like');
                $requirementIds[] = $pvCompanyReqIds;
            }
    
            if(!empty($request->pv_name)){
                $pvNameReqIds = $this->getRequirementIdBasedOnData('poc_name', $request->pv_name, 'like');
                $requirementIds[] = $pvNameReqIds;
            }
    
            if(!empty($request->pv_phone)){
                $pvPhoneReqIds = $this->getRequirementIdBasedOnData('poc_phone_number', $request->pv_phone);
                $requirementIds[] = $pvPhoneReqIds;
            }

            if(!empty($request->job_title)){
                $jobTitleReqIds = $this->getRequirementIdBasedOnData('job_title', $request->job_title, 'like');
                $requirementIds[] = $jobTitleReqIds;
            }
    
            if(!empty($request->bdm)){
                $bdmReqIds = $this->getRequirementIdBasedOnData('user_id', $request->bdm);
                $requirementIds[] = $bdmReqIds;
            }
    
            if(!empty($request->job_id)){
                $jobIdReqIds = $this->getRequirementIdBasedOnData('job_id', $request->job_id);
                $requirementIds[] = $jobIdReqIds;
            }
    
            if(!empty($request->client)){
                $clientReqIds = $this->getRequirementIdBasedOnData('client_name', $request->client, 'like');
                $requirementIds[] = $clientReqIds;
            }
    
            if(!empty($request->job_location)){
                $jobLocationReqIds = $this->getRequirementIdBasedOnData('location', $request->job_location, 'like');
                $requirementIds[] = $jobLocationReqIds;
            }

            if($requirementIds && count($requirementIds)){
                $commonRequirementIds = call_user_func_array('array_intersect', $requirementIds);
                
                if(Auth::user()->role == 'recruiter'){
                    if($commonRequirementIds && count($commonRequirementIds)){
                        $submissions->whereIn('requirement_id', $commonRequirementIds);
                    } else {
                        $submissions->where('requirement_id', 0);
                    }
                }
            }

            if($user->role == 'recruiter'){
                $data = $submissions->where('user_id', $user->id)->orderBy('id', 'desc')->get();
                // if(!empty($filterStatus)){
                //     $data = Submission::where('user_id', $user->id)->whereIn('status',$filterStatus)->orderBy('id', 'desc')->get();
                //     if($showUnviewed){
                //         $data = Submission::where('user_id', $user->id)->whereIn('status',$filterStatus)->where('is_show','0')->orderBy('id', 'desc')->get();
                //     }
                // } else {
                //     if($showUnviewed){
                //         $data = Submission::where('user_id', $user->id)->where('is_show','0')->orderBy('id', 'desc')->get();
                //     } else {
                //         $data = Submission::where('user_id', $user->id)->orderBy('id', 'desc')->get();
                //     }
                // }                
            }else if($user->role == 'bdm'){
                $loggedinBdmrequirementIds = Requirement::where('user_id', $user->id)->pluck('id')->toArray();
                if($requirementIds && count($requirementIds)){
                    if(isset($commonRequirementIds) && $commonRequirementIds && count($commonRequirementIds)){
                        $allRequirementIds = array_intersect($loggedinBdmrequirementIds, $commonRequirementIds);
                        $data = $submissions->whereIn('requirement_id', $allRequirementIds)->orderBy('id', 'desc')->get();
                    } else {
                        $data = $submissions->where('requirement_id', 0)->orderBy('id', 'desc')->get();
                    }
                } else {
                    $data = $submissions->whereIn('requirement_id', $loggedinBdmrequirementIds)->orderBy('id', 'desc')->get();
                }
                // if(!empty($filterStatus)){
                //     $data = Submission::whereIn('requirement_id', $requirementIds)->whereIn('status',$filterStatus)->orderBy('id', 'desc')->get();
                //     if($showUnviewed){
                //         $data = Submission::whereIn('requirement_id', $requirementIds)->whereIn('status',$filterStatus)->where('is_show','0')->orderBy('id', 'desc')->get();
                //     }    
                // } else {
                //     if($showUnviewed){
                //         $data = Submission::whereIn('requirement_id', $requirementIds)->where('is_show','0')->orderBy('id', 'desc')->get();
                //     } else {
                //         $data = Submission::whereIn('requirement_id', $requirementIds)->orderBy('id', 'desc')->get();
                //     }
                // }
            }else{
                if($requirementIds && count($requirementIds)){
                    if(isset($commonRequirementIds) && $commonRequirementIds && count($commonRequirementIds)){
                        $data = $submissions->whereIn('requirement_id', $commonRequirementIds)->orderBy('id', 'desc')->get();
                    } else {
                        $data = $submissions->where('requirement_id', 0)->orderBy('id', 'desc')->get();
                    }
                } else {
                    $data = $submissions->orderBy('id', 'desc')->get();
                }
                // if(!empty($filterStatus)){
                //     $data = Submission::orderBy('id', 'desc')->get();
                //     if($showUnviewed){
                //         $data = Submission::whereIn('status',$filterStatus)->where('is_show','0')->orderBy('id', 'desc')->get();
                //     }    
                // } else {
                //     if($showUnviewed){
                //         $data = Submission::where('is_show','0')->orderBy('id', 'desc')->get();
                //     } else {
                //         $data = Submission::orderBy('id', 'desc')->get();
                //     }
                // }
            }

            return Datatables::of($data)
                ->addColumn('job_id', function($row){
                    return '<span class=" job-title" data-id="'.$row->requirement_id.'">'.$row->Requirement->job_id.'</span>';
                })
                ->addColumn('job_title', function($row){
                    return '<span class="job-title" data-id="'.$row->requirement_id.'">'.$row->Requirement->job_title.'</span>';
                })
                // ->addColumn('job_keyword', function($row){
                //     return $row->Requirement->job_keyword;
                // })
                // ->addColumn('duration', function($row){
                //     return $row->Requirement->duration;
                // })
                ->addColumn('client_name', function($row){
                    return '<i class="fa fa-eye client-icon client-icon-'.$row->id.'" onclick="showData('.$row->id.',\'client-\')" aria-hidden="true"></i><span class="client client-'.$row->id.'" style="display:none">'.(($row->Requirement->display_client) ? $row->Requirement->client_name : '').'</span>';
                })
                ->addColumn('recruter_name', function($row){
                    return $row->recruiters->name;
                })
                ->addColumn('bdm', function($row){
                    return $row->Requirement->BDM->name;
                })
                ->addColumn('candidate_name', function($row){
                    $candidateClass = $this->getCandidateClass($row,true);
                    $candidateCss   = $this->getCandidateCss($row,true);
                    $candidateBorderCss = $this->getCandidateBorderCss($row);
                    $candidateNames = explode(' ',$row->name);
                    $candidateName = isset($candidateNames[0]) ? $candidateNames[0] : '';
                    $candidateCount = $this->getCandidateCountByEmail($row->email);
                    $isCandidateHasLog  = $this->isCandidateHasLog($row);
                    return ($candidateCount ? "<span class='badge bg-indigo position-absolute top-0 start-100 translate-middle'>$candidateCount</span>" : "").(($isCandidateHasLog) ? "<span class='badge badge-pill badge-primary ml-4 position-absolute top-0 start-100 translate-middle'>L</span>" : "").'<div  class="a-center pt-2 pl-2 pb-2 pr-2 '. $candidateCss.'" style="width: fit-content;"><span class="'.$candidateClass.' candidate candidate-'.$row->id.'" style="'.$candidateBorderCss.'" data-cid="'.$row->id.'">'. $candidateName. '-' .$row->candidate_id. '</span></div>';
                })
                ->addColumn('bdm_status', function($row){
                    $statusLastUpdatedAt = ($row->bdm_status_updated_at) ? strtotime($row->bdm_status_updated_at) : 0;
                    // if(in_array(Auth::user()->role,['admin'])){
                    //     $status = '<select data-order="'.$statusLastUpdatedAt.'" name="status" class="form-control select2 submissionStatus" data-id="'.$row->id.'">';
                    //     $submissionStatus = Submission::$status;
                    //     foreach ($submissionStatus as $key => $val){
                    //         $selected = $row->status == $key ? 'selected' : '';
                    //         $status .= '<option value="'.$key.'" '.$selected.'>'.$val.'</option>';
                    //     }
                    //     $status .= '</select>';
                        
                    // }else{
                    $status = isset(Submission::$status[$row->status]) ? "<p data-order='$statusLastUpdatedAt'>".Submission::$status[$row->status]."</p>" : '';
                    // }
                    if($row->status){
                        if($row->status == Submission::STATUS_REJECTED){
                            $status .= "<span class='feedback' style='display:none'>".$this->getTooltipHtml($row->reason,30)."</span>";
                        }
                        $status .= getEntityLastUpdatedAtHtml(EntityHistory::ENTITY_TYPE_BDM_STATUS,$row->id);
                    }
                    return $status;
                })
                ->addColumn('pv_status', function($row){
                    $status = '';
                    $statusLastUpdatedAt = ($row->pv_status_updated_at) ? strtotime($row->pv_status_updated_at) : 0;
                    if(in_array(Auth::user()->role,['admin','bdm'])){
                        if($row->status == Submission::STATUS_ACCEPT){
                            $isDisplay = 0;
                            if(!empty($row->pv_status)){
                                $isDisplay = 1;       
                           } else {
                                $status .= '<button class="btn btn-sm btn-default show-pv-status-'.$row->id.' mr-2" data-id="'.$row->id.'" onclick="showStatusOptions('.$row->id.')"><i class="fa fa-plus-square"></i></button>';
                           }
                           $status .= '<select data-order="'.$statusLastUpdatedAt.'" style=" '.(!$isDisplay ? "display:none;" : "").'" name="pvstatus" class="form-control select2 submissionPvStatus pv-status-'.$row->id.'" data-id="'.$row->id.'">';
                            $submissionPvStatus = Submission::$pvStatus;
                            $status .= '<option value="">Select Status</option>';
                            foreach ($submissionPvStatus as $key => $val){
                                $selected = $row->pv_status == $key ? 'selected' : '';
                                $status .= '<option value="'.$key.'" '.$selected.'>'.$val.'</option>';
                            }
                            $status .= '</select>'; 
                        }
                    }else{
                        if($row->status == Submission::STATUS_ACCEPT){
                            $status .= isset(Submission::$pvStatus[$row->pv_status]) ? "<p data-order='$statusLastUpdatedAt'>".Submission::$pvStatus[$row->pv_status]."</p>" : '';
                        }
                    }
                    if($row->pv_status && $row->status == Submission::STATUS_ACCEPT){
                        $status .= "<span class='feedback' style='display:none'>".$this->getTooltipHtml($row->pv_reason,30)."</span>";
                        $status .= getEntityLastUpdatedAtHtml(EntityHistory::ENTITY_TYPE_PV_STATUS,$row->id);
                    }
                    return $status;
                })
                ->addColumn('created_at', function($row){
                    return date('m/d/y', strtotime($row->created_at));
                })
                ->addColumn('location', function($row){
                    return $row->Requirement->location;
                })
                ->addColumn('candidate_location', function($row){
                    return $row->location;
                })
                ->addColumn('pv', function($row){
                    return '<i class="fa fa-eye pv_name-icon pv-name-icon-'.$row->id.'" onclick="showData('.$row->id.',\'pv-name-\')" aria-hidden="true"></i><span class="pv_name pv-name-'.$row->id.'" style="display:none">'.$row->Requirement->pv_company_name.'</span>';
                })
                ->addColumn('poc', function($row){
                    $pocNameArray = explode(' ', $row->Requirement->poc_name);
                    $pocFirstName = isset($pocNameArray[0]) ? $pocNameArray[0] : '';
                    return '<i class="fa fa-eye poc_name-icon poc-name-icon-'.$row->id.'" onclick="showData('.$row->id.',\'poc-name-\')" aria-hidden="true"></i><span class="poc_name poc-name-'.$row->id.'" style="display:none">'.$pocFirstName.'</span>';
                })
                ->addColumn('b_rate', function($row){
                    return $row->Requirement->my_rate;
                })
                ->addColumn('r_rate', function($row){
                    return $row->recruiter_rate;
                })
                ->addColumn('employer_name', function($row){
                    return '<i class="fa fa-eye show_employer_name-icon employer-name-icon-'.$row->id.'" onclick="showData('.$row->id.',\'employer-name-\')" aria-hidden="true"></i><span class="show_employer_name employer-name-'.$row->id.'" style="display:none">'.$row->employer_name.'</span>';
                })
                ->addColumn('emp_poc', function($row){
                    $empPocNameArray = explode(' ', $row->employee_name);
                    $empPocFirstName = isset($empPocNameArray[0]) ? $empPocNameArray[0] : '';
                    return '<i class="fa fa-eye emp_poc-icon emp_poc-icon-'.$row->id.'" onclick="showData('.$row->id.',\'emp_poc-\')" aria-hidden="true"></i><span class="emp_poc emp_poc-'.$row->id.'" style="display:none">'.$empPocFirstName.'</span>';
                })
                // ->addColumn('action', function($row){
                //     return '<div class="btn-group btn-group-sm mr-2"><a href="'.url('admin/bdm_submission/'.$row->id.'/edit').'"><button class="btn btn-sm btn-default tip" data-toggle="tooltip" title="Edit Interview" data-trigger="hover" type="submit" ><i class="fa fa-edit"></i></button></a></div>';
                // })
                ->addColumn('client_status', function($row){
                    $interviewModel = new Interview();
                    $statusLastUpdatedAt = ($row->interview_status_updated_at) ? strtotime($row->interview_status_updated_at) : 0;
                    $interviewFeedback = $interviewModel->getInterviewFeedbackBasedOnSubmissionIdAndJobId($row->id, $row->Requirement->job_id);
                    $status = "<p data-order='$statusLastUpdatedAt'>".$interviewModel->getInterviewStatusBasedOnSubmissionIdAndJobId($row->id, $row->Requirement->job_id)."</p>";
                    $status .= "<span class='feedback' style='display:none'>".$this->getTooltipHtml($interviewFeedback,30)."</span>";
                    return $status;
                })
                ->rawColumns(['job_id','job_title','job_keyword','duration','client_name','poc','pv','employer_name','recruter_name','candidate_name','action','bdm_status','pv_status','emp_poc','created_at','client_status'])
                ->make(true);
        }

        $submissionModel = new Submission();
        $submissionStatusOptions[$submissionModel::STATUS_ACCEPT] = 'Show Accepted only';
        $submissionStatusOptions[$submissionModel::STATUS_REJECTED] = 'Show Rejected only';
        $submissionStatusOptions[$submissionModel::STATUS_PENDING] = 'Show Pending only';
        $submissionStatusOptions['un_viewed'] = 'Show Unviewed Only';
        $submissionStatusOptions['both'] = 'Show Both';

        $data['filterOptions'] = $submissionStatusOptions;
        $data['filterFile'] = 'common_filter';
        $data['pvCompanyName'] = $this->getPvCompanyName();

        return view('admin.bdm_submission.index',$data);
    }

    public function getRequirementIdBasedOnData($column, $data, $operator = 'equal')
    {
        if(!$column || !$data){
            return [];
        }

        if($operator == 'equal'){
            return Requirement::where($column, $data)->pluck('id')->toArray();
        } else if($operator == 'like'){
            return Requirement::where($column, 'Like', '%'.$data.'%')->pluck('id')->toArray();
        }

        return [];
    }

    // public function getRequirementIdBasedOnServedOptions($served, $query){
    //     if(!$served){
    //         return $this;
    //     }

    //     $requiremrntIdsHavingSubmission = Submission::pluck('requirement_id')->toArray();
    //     $requiremrntIdsHavingSubmission = array_unique($requiremrntIdsHavingSubmission);

    //     if($served == 'served'){
    //         $query->whereNotNull('recruiter');
    //         $query->whereIn('id', $requiremrntIdsHavingSubmission);
    //     } else if($served == 'un_served') {
    //         $query->whereNotNull('recruiter');
    //         $query->whereNotIn('id', $requiremrntIdsHavingSubmission);
    //     } else if($served == 'allocated') {
    //         $query->whereNotNull('recruiter');
    //     } else if($served == 'not_allocated'){
    //         $query->whereNull('recruiter');
    //     } else if($served == 'allocated_but_not_served'){
    //         $query->whereNotNull('recruiter');
    //         $query->whereNotIn('id', $requiremrntIdsHavingSubmission);
    //     }

    //     return $this;
    // }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $data['menu'] = "Manage Submission";
        $data['submission'] = Submission::where('id',$id)->first();
        $data['sub_menu'] = "Submission";
        $data['isFromBDM'] = 1;
        $data['settings'] = $this->getSettingData();

        return view('admin.submission.edit',$data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            // 'name' => 'required',
            // 'email' => 'required',
            'location' => 'required',
            'phone' => 'required|numeric',
            'employer_detail' => 'required',
            'work_authorization' => 'required',
            'last_4_ssn' => 'required',
            'recruiter_rate' => 'required',
            'education_details' => 'required',
            'resume_experience' => 'required',
            'linkedin_id' => 'required',
            'relocation' => 'required',
            //'vendor_rate' => 'required',
            'employer_name' => 'required',
            'employee_name' => 'required',
            'employee_email' => 'required|email',
            'employee_phone' => 'required|numeric|digits:10',
        ]);

        $input = $request->all();
        unset($input['submission_id']);
        unset($input['name']);
        unset($input['email']);
        $Submission = Submission::where('id',$id)->first();
        $cloneSubmission = $Submission->replicate();
        $this->manageSubmissionLogs($input, $Submission, $cloneSubmission->Requirement->job_id);
        $Submission->update($input);
        $this->updateCandidateWithSameCandidateId($Submission,$cloneSubmission->Requirement->job_id);
        \Session::flash('success','Submission  has been updated successfully!');
        return redirect(route('bdm_submission.index'));
    }

    public function destroy($id)
    {
        //
    }

    public function changePvStatus(Request $request, $id)
    {
       $submission = Submission::where('id',$id)->first();

        if(!empty($submission)){
            $input['pv_status'] = $request['pv_status'];
            $input['pv_reason'] = '';
            $input['pv_status_updated_at'] = \Carbon\Carbon::now();
            $submission->update($input);

            $inputData['submission_id']  = $submission->id;
            $inputData['requirement_id'] = $submission->requirement_id;
            $inputData['entity_type']    = EntityHistory::ENTITY_TYPE_PV_STATUS;
            $inputData['entity_value']   = $submission->status;
    
            EntityHistory::create($inputData);

            $data['status'] = 1;
            $data['css']    = $this->getCandidateCss($submission);
            $data['class']  = $this->getCandidateClass($submission);
            $data['entity_type'] = EntityHistory::ENTITY_TYPE_PV_STATUS;
            $data['updated_date_html'] = getEntityLastUpdatedAtHtml(EntityHistory::ENTITY_TYPE_PV_STATUS,$submission->id);
        }else{
           $data['status'] = 0;
        }

        return $data;
    }

    public function pvRejectReasonUpdate(Request $request)
    {
        $submission = Submission::where('id',$request->submissionId)->first();

        if(!empty($submission)){
            $input['pv_reason'] = $request['pv_reason'];
            $input['pv_status'] = $request['pv_status'];
            $submission->update($input);
        }

        return redirect()->route('bdm_submission.index')->with('filter', $request['filter']);
    }

    public function getUpdateSubmissionData(Request $request){
        $data['status'] = 0;
        $submissionData = Submission::where('id', $request->id)->first();
        
        if(empty($submissionData)){
            return $data;
        }

        $data['status'] = 1;
        $data['submissionData'] = $submissionData;

        return $data;
    }

    public function updateSubmissionData(Request $request){
        $data['status'] = 0;
        if(empty($request->submission_id)){
            return $data;
        }
        $submission = Submission::where('id',$request->submission_id)->first();
        if(empty($submission)){
            return $data;
        }
        $inputData = $request->all();
        unset($inputData['submission_id']);
        unset($inputData['name']);
        unset($inputData['email']);
        $cloneSubmission = $submission->replicate();
        $this->manageSubmissionLogs($inputData, $submission, $cloneSubmission->Requirement->job_id);
        $submission->update($inputData);

        $this->updateCandidateWithSameCandidateId($submission,$cloneSubmission->Requirement->job_id);
        $data['status'] = 1;
        $data['url'] = route('bdm_submission.index');
        \Session::flash('success','Submission has been updated successfully!');
        return $data;
    }
}
