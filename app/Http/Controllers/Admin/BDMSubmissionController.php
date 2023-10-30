<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use App\Models\Submission;
use App\Models\Requirement;
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
            $loggedinUser = Auth::user()->id;
            $requirementIds = Requirement::where('user_id', $loggedinUser)->pluck('id')->toArray();
            
            $filterStatus = [];
            
            if(empty($request->filter_status)){
                $filterStatus[] = 'accepted';
            } else if($request->filter_status == 'both'){
                $filterStatus[] = 'accepted';
                $filterStatus[] = 'rejected';
            } else {
                $filterStatus[] = $request->filter_status;
            }

            $data = Submission::whereIn('requirement_id', $requirementIds)->whereIn('status',$filterStatus)->get();

            return Datatables::of($data)
                ->addIndexColumn()
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
                    return  $row->Requirement->display_client ? $row->Requirement->client_name : '';
                })
                ->addColumn('recruter_name', function($row){
                    return $row->recruiters->name;
                })
                ->addColumn('candidate_name', function($row){
                    return $this->getCandidateHtml([$row], $row, $page='my_submission');
                })
                ->addColumn('action', function($row){
                    $status = '<select name="status" class="form-control select2 submissionStatus" data-id="'.$row->id.'">';
                    $submissionStatus = Submission::$status;
                    foreach ($submissionStatus as $key => $val){
                        $selected = $row->status == $key ? 'selected' : '';
                        $status .= '<option value="'.$key.'" '.$selected.'>'.$val.'</option>';
                    }
                    $status .= '</select>';
                    return $status;
                })
                ->addColumn('status', function($row){
                    $status = '<select name="pvstatus" class="form-control select2 submissionPvStatus" data-id="'.$row->id.'">';
                    $submissionPvStatus = Submission::$pvStatus;
                    $status .= '<option value="">Select Status</option>';
                    foreach ($submissionPvStatus as $key => $val){
                        $selected = $row->pv_status == $key ? 'selected' : '';
                        $status .= '<option value="'.$key.'" '.$selected.'>'.$val.'</option>';
                    }
                    $status .= '</select>';
                    return $status;
                })
                ->addColumn('created_at', function($row){
                    return date('m-d-Y', strtotime($row->created_at));
                })
                ->rawColumns(['job_id','job_title','job_keyword','duration','client_name','recruter_name','candidate_name','action','status','created_at'])
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
            $data['status'] = 1;
            $data['css']    = $this->getCandidateCss($submission);
            $data['class']  = $this->getCandidateClass($submission);
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
