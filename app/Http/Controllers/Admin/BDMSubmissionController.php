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
    }

    public function index(Request $request)
    {
        $data['menu'] = "Manage Submission";
        if ($request->ajax()) {
            $reqFilterStatus = $request->filter_status;

            $filterStatus = [];
            $showUnviewed = 0;
            
            if(!empty($reqFilterStatus)){
                if(in_array('both',$reqFilterStatus)){
                    $filterStatus[] = 'accepted';
                    $filterStatus[] = 'rejected';
                }
    
                if(in_array('accepted',$reqFilterStatus)){
                    $filterStatus[] = 'accepted';
                }
    
                if(in_array('rejected',$reqFilterStatus)){
                    $filterStatus[] = 'rejected';
                }
    
                if(in_array('pending',$reqFilterStatus)){
                    $filterStatus[] = 'pending';
                }

                if(in_array('un_viewed',$reqFilterStatus)){
                    $showUnviewed = 1;
                }
            }

            $user = Auth::user();
            if($user->role == 'recruiter'){
                if(!empty($filterStatus)){
                    $data = Submission::where('user_id', $user->id)->whereIn('status',$filterStatus)->latest('updated_at')->get();
                    if($showUnviewed){
                        $data = Submission::where('user_id', $user->id)->whereIn('status',$filterStatus)->where('is_show','0')->latest('updated_at')->get();
                    }
                } else {
                    if($showUnviewed){
                        $data = Submission::where('user_id', $user->id)->where('is_show','0')->latest('updated_at')->get();
                    } else {
                        $data = Submission::where('user_id', $user->id)->latest('updated_at')->get();
                    }
                }                
            }else if($user->role == 'bdm'){
                $requirementIds = Requirement::where('user_id', $user->id)->pluck('id')->toArray();
                if(!empty($filterStatus)){
                    $data = Submission::whereIn('requirement_id', $requirementIds)->whereIn('status',$filterStatus)->latest('updated_at')->get();
                    if($showUnviewed){
                        $data = Submission::whereIn('requirement_id', $requirementIds)->whereIn('status',$filterStatus)->where('is_show','0')->latest('updated_at')->get();
                    }    
                } else {
                    if($showUnviewed){
                        $data = Submission::whereIn('requirement_id', $requirementIds)->where('is_show','0')->latest('updated_at')->get();
                    } else {
                        $data = Submission::whereIn('requirement_id', $requirementIds)->latest('updated_at')->get();
                    }
                }
            }else{
                if(!empty($filterStatus)){
                    $data = Submission::whereIn('status',$filterStatus)->get();
                    if($showUnviewed){
                        $data = Submission::whereIn('status',$filterStatus)->where('is_show','0')->get();
                    }    
                } else {
                    if($showUnviewed){
                        $data = Submission::where('is_show','0')->get();
                    } else {
                        $data = Submission::get();
                    }
                }
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
                    return ($candidateCount ? "<span class='badge bg-indigo position-absolute top-0 start-100 translate-middle'>$candidateCount</span>" : "").(($isCandidateHasLog) ? "<span class='badge badge-pill badge-primary ml-4 position-absolute top-0 start-100 translate-middle'>L</span>" : "").'<div  class="a-center pt-2 pl-2 pb-2 pr-2 '. $candidateCss.'" style="width: fit-content;"><span class="'.$candidateClass.' candidate" style="'.$candidateBorderCss.'" data-cid="'.$row->id.'">'. $candidateName. '-' .$row->candidate_id. '</span></div>';
                })
                ->addColumn('bdm_status', function($row){
                    if(in_array(Auth::user()->role,['admin','bdm'])){
                        $status = '<select name="status" class="form-control select2 submissionStatus" data-id="'.$row->id.'">';
                        $submissionStatus = Submission::$status;
                        foreach ($submissionStatus as $key => $val){
                            $selected = $row->status == $key ? 'selected' : '';
                            $status .= '<option value="'.$key.'" '.$selected.'>'.$val.'</option>';
                        }
                        $status .= '</select>';
                        
                    }else{
                        $status = isset(Submission::$status[$row->status]) ? Submission::$status[$row->status] : '';
                    }
                    $status .= '<span>'.$row->reason.'</span>';
                    $status .= getEntityLastUpdatedAtHtml(EntityHistory::ENTITY_TYPE_BDM_STATUS,$row->id);
                    return $status;
                })
                ->addColumn('pv_status', function($row){
                    if(in_array(Auth::user()->role,['admin','bdm'])){
                        $status = '';
                        if($row->status == Submission::STATUS_ACCEPT){
                            $isDisplay = 0;
                            if(!empty($row->pv_status)){
                                $isDisplay = 1;       
                           } else {
                                $status .= '<button class="btn btn-sm btn-default show-pv-status-'.$row->id.' mr-2" data-id="'.$row->id.'" onclick="showStatusOptions('.$row->id.')"><i class="fa fa-plus-square"></i></button>';
                           }
                           $status .= '<select style=" '.(!$isDisplay ? "display:none;" : "").'" name="pvstatus" class="form-control select2 submissionPvStatus pv-status-'.$row->id.'" data-id="'.$row->id.'">';
                            $submissionPvStatus = Submission::$pvStatus;
                            $status .= '<option value="">Select Status</option>';
                            foreach ($submissionPvStatus as $key => $val){
                                $selected = $row->pv_status == $key ? 'selected' : '';
                                $status .= '<option value="'.$key.'" '.$selected.'>'.$val.'</option>';
                            }
                            $status .= '</select>'; 
                        }
                    }else{
                        $status = isset(Submission::$pvStatus[$row->pv_status]) ? Submission::$pvStatus[$row->pv_status] : '';
                    }
                    $status .= '<span>'.$row->pv_reason.'</span>';
                    $status .= getEntityLastUpdatedAtHtml(EntityHistory::ENTITY_TYPE_PV_STATUS,$row->id);
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
                    return $interviewModel->getInterviewStatusBasedOnSubmissionIdAndJobId($row->id, $row->Requirement->job_id);
                })
                ->rawColumns(['job_id','job_title','job_keyword','duration','client_name','poc','pv','employer_name','recruter_name','candidate_name','action','bdm_status','pv_status','emp_poc','created_at'])
                ->make(true);
        }

        $submissionModel = new Submission();
        $submissionStatusOptions[$submissionModel::STATUS_ACCEPT] = 'Show Accepted only';
        $submissionStatusOptions[$submissionModel::STATUS_REJECTED] = 'Show Rejected only';
        $submissionStatusOptions[$submissionModel::STATUS_PENDING] = 'Show Pending only';
        $submissionStatusOptions['un_viewed'] = 'Show Unviewed Only';
        $submissionStatusOptions['both'] = 'Show Both';

        $data['filterOptions'] = $submissionStatusOptions;

        return view('admin.bdm_submission.index',$data);
    }

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
        $this->manageSubmissionLogs($input, $Submission);
        $Submission->update($input);
        $this->updateCandidateWithSameCandidateId($submission);
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
        $this->manageSubmissionLogs($request->all(), $submission);
        $submission->update($inputData);

        $this->updateCandidateWithSameCandidateId($submission);
        $data['status'] = 1;
        $data['url'] = route('bdm_submission.index');
        \Session::flash('success','Submission has been updated successfully!');
        return $data;
    }
}
