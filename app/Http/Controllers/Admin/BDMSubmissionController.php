<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use App\Models\Submission;
use App\Models\Requirement;
use App\Models\EntityHistory;
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

            $filterStatus = [];
            
            if(empty($request->filter_status)){
                $filterStatus[] = 'accepted';
            } else if($request->filter_status == 'both'){
                $filterStatus[] = 'accepted';
                $filterStatus[] = 'rejected';
            } else {
                $filterStatus[] = $request->filter_status;
            }

            $user = Auth::user();
            if($user->role == 'recruiter'){
                $data = Submission::where('user_id', $user->id)->whereIn('status',$filterStatus)->get();
            }else{
                $requirementIds = Requirement::where('user_id', $user->id)->pluck('id')->toArray();
                $data = Submission::whereIn('requirement_id', $requirementIds)->whereIn('status',$filterStatus)->get();
            }

            return Datatables::of($data)
                ->addColumn('job_id', function($row){
                    return $row->Requirement->job_id;
                })
                ->addColumn('job_title', function($row){
                    return '<span class="job-title" data-id="'.$row->requirement_id.'">'.$row->Requirement->job_title.'</span>';
                })
                ->addColumn('job_keyword', function($row){
                    return $row->Requirement->job_keyword;
                })
                ->addColumn('duration', function($row){
                    return $row->Requirement->duration;
                })
                ->addColumn('client_name', function($row){
                    return '<i class="fa fa-eye client-icon client-icon-'.$row->id.'" onclick="showData('.$row->id.',\'client-\')" aria-hidden="true"></i><span class="client client-'.$row->id.'" style="display:none">'.(($row->Requirement->display_client) ? $row->Requirement->client_name : '').'</span>';
                })
                ->addColumn('recruter_name', function($row){
                    return $row->recruiters->name;
                })
                ->addColumn('candidate_name', function($row){
                    return $this->getCandidateHtml([$row], $row, $page='my_submission');
                })
                ->addColumn('action', function($row){
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
                    $status .= getEntityLastUpdatedAtHtml(EntityHistory::ENTITY_TYPE_BDM_STATUS,$row->id);
                    return $status;
                })
                ->addColumn('status', function($row){
                    if(in_array(Auth::user()->role,['admin','bdm'])){
                        $status = '<select name="pvstatus" class="form-control select2 submissionPvStatus" data-id="'.$row->id.'">';
                        $submissionPvStatus = Submission::$pvStatus;
                        $status .= '<option value="">Select Status</option>';
                        foreach ($submissionPvStatus as $key => $val){
                            $selected = $row->pv_status == $key ? 'selected' : '';
                            $status .= '<option value="'.$key.'" '.$selected.'>'.$val.'</option>';
                        }
                        $status .= '</select>';
                    }else{
                        $status = isset(Submission::$pvStatus[$row->pv_status]) ? Submission::$pvStatus[$row->pv_status] : '';
                    }
                    
                    $status .= getEntityLastUpdatedAtHtml(EntityHistory::ENTITY_TYPE_PV_STATUS,$row->id);
                    return $status;
                })
                ->addColumn('created_at', function($row){
                    return date('m/d/Y', strtotime($row->created_at));
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
                    return '<i class="fa fa-eye poc_name-icon poc-name-icon-'.$row->id.'" onclick="showData('.$row->id.',\'poc-name-\')" aria-hidden="true"></i><span class="poc_name poc-name-'.$row->id.'" style="display:none">'.$row->Requirement->poc_name.'</span>';
                })
                ->addColumn('b_rate', function($row){
                    return $row->Requirement->my_rate;
                })
                ->addColumn('r_rate', function($row){
                    return $row->recruiter_rate;
                })
                ->addColumn('employer_name', function($row){
                    return '<i class="fa fa-eye employer_name-icon employer-name-icon-'.$row->id.'" onclick="showData('.$row->id.',\'employer-name-\')" aria-hidden="true"></i><span class="employer_name employer-name-'.$row->id.'" style="display:none">'.$row->employer_name.'</span>';
                })
                ->addColumn('employer_phone', function($row){
                    return '<i class="fa fa-eye emp_poc-icon emp_poc-icon-'.$row->id.'" onclick="showData('.$row->id.',\'emp_poc-\')" aria-hidden="true"></i><span class="emp_poc emp_poc-'.$row->id.'" style="display:none">'.$row->employee_phone.'</span>';
                })
                ->rawColumns(['job_id','job_title','job_keyword','duration','client_name','poc','pv','employer_name','recruter_name','candidate_name','action','status','employer_phone','created_at'])
                ->make(true);
        }

        $submissionModel = new Submission();
        $submissionStatusOptions[$submissionModel::STATUS_ACCEPT] = 'Show Accepted only';
        $submissionStatusOptions[$submissionModel::STATUS_REJECTED] = 'Show Rejected only';
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
        //
    }

    public function update(Request $request, $id)
    {
        //
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
}
