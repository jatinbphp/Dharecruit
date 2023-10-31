<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use App\Models\Interview;
use App\Models\Submission;
use App\Models\Requirement;
use App\Models\EntityHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class InterviewController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('accessright:manage_interview');
    }

    public function index(Request $request)
    {
        $data['menu'] = "Manage Interview";

        if ($request->ajax()) {
            $loggedinUser = Auth::user()->id;
            $data = Interview::where('user_id', $loggedinUser)->get();
            
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function($row){
                    $status = '<select name="interviewStatus" class="form-control select2 interviewStatus" data-id="'.$row->id.'">';
                    $interviewStatus = Interview::$interviewStatusOptions;
                    foreach ($interviewStatus as $key => $val){
                        $selected = $row->status == $key ? 'selected' : '';
                        $status .= '<option value="'.$key.'" '.$selected.'>'.$val.'</option>';
                    }
                    $status .= '</select>';
                    $statusLastUpdatedAtHtml = getEntityLastUpdatedAtHtml(EntityHistory::ENTITY_TYPE_INTERVIEW_STATUS,$row->submission_id);
                    if($statusLastUpdatedAtHtml){
                        $status .= $statusLastUpdatedAtHtml;
                    }else{
                        $status .= '<div id="interviewStatusUpdatedAt-'.$row->id.'"></div>';
                    }
                    return $status;
                })
                ->addColumn('action', function($row){
                    return '<div class="btn-group btn-group-sm mr-2"><a href="'.url('admin/interview/'.$row->id.'/edit').'"><button class="btn btn-sm btn-default tip" data-toggle="tooltip" title="Edit Interview" data-trigger="hover" type="submit" ><i class="fa fa-edit"></i></button></a></div>';
                })
                ->addColumn('candidate_phone_number', function($row){
                    return '<i class="fa fa-eye candidate_phone-icon candidate-phone-icon-'.$row->id.'" onclick="showData('.$row->id.',\'candidate-phone-\')" aria-hidden="true"></i><span class="candidate_phone candidate-phone-'.$row->id.'" style="display:none">'.$row->candidate_phone_number.'</span>';
                })
                ->addColumn('candidate_email', function($row){
                    return '<i class="fa fa-eye candidate_email-icon candidate-email-icon-'.$row->id.'" onclick="showData('.$row->id.',\'candidate-email-\')" aria-hidden="true"></i><span class="candidate_email candidate-email-'.$row->id.'" style="display:none">'.$row->candidate_email.'</span>';
                })
                ->addColumn('candidate_name', function($row){
                    return $row->Submission->name;
                })
                ->addColumn('created_at', function($row){
                    return date('m/d/Y', strtotime($row->created_at));
                })
                ->addColumn('client_location', function($row){
                    return $row->Submission->Requirement->location;
                })
                ->addColumn('candidate_location', function($row){
                    return $row->Submission->location;
                })
                ->addColumn('recruiter', function($row){
                    return $row->Submission->Recruiters->name;
                })
                ->addColumn('employer_name', function($row){
                    return '<i class="fa fa-eye employer_name-icon employer-name-icon-'.$row->id.'" onclick="showData('.$row->id.',\'employer-name-\')" aria-hidden="true"></i><span class="employer_name employer-name-'.$row->id.'" style="display:none">'.$row->Submission->employer_name.'</span>';
                })
                ->addColumn('emp_poc', function($row){
                    return '<i class="fa fa-eye emp_poc-icon emp-poc-icon-'.$row->id.'" onclick="showData('.$row->id.',\'emp-poc-\')" aria-hidden="true"></i><span class="emp_poc emp-poc-'.$row->id.'" style="display:none">'.$row->Submission->employee_phone.'</span>';
                })
                ->addColumn('poc_name', function($row){
                    return '<i class="fa fa-eye poc_name-icon poc-name-icon-'.$row->id.'" onclick="showData('.$row->id.',\'poc-name-\')" aria-hidden="true"></i><span class="poc_name poc-name-'.$row->id.'" style="display:none">'.$row->Submission->Requirement->poc_name.'</span>';
                })
                ->addColumn('pv_name', function($row){
                    return '<i class="fa fa-eye pv_name-icon pv-name-icon-'.$row->id.'" onclick="showData('.$row->id.',\'pv-name-\')" aria-hidden="true"></i><span class="pv_name pv-name-'.$row->id.'" style="display:none">'.$row->Submission->Requirement->pv_company_name.'</span>';
                })
                ->addColumn('hiring_manager', function($row){
                    return '<i class="fa fa-eye hiring_manager-icon hiring-manager-icon-'.$row->id.'" onclick="showData('.$row->id.',\'hiring-manager-\')" aria-hidden="true"></i><span class="hiring_manager hiring-manager-'.$row->id.'" style="display:none">'.$row->hiring_manager.'</span>';
                })
                ->addColumn('client', function($row){
                    return '<i class="fa fa-eye client-icon client-icon-'.$row->id.'" onclick="showData('.$row->id.',\'client-\')" aria-hidden="true"></i><span class="client client-'.$row->id.'" style="display:none">'.$row->client.'</span>';
                })
                ->addColumn('interview_time', function($row){
                    return '<br><span style="font-weight:bold">'.date('m/d l', strtotime($row->interview_date)).'</span><br>
                    <span>'.date('H:i:s', strtotime($row->interview_time)) .' '. $row->time_zone.'</span>';
                })
                ->rawColumns(['status','action','candidate_phone_number','emp_poc','candidate_email','employer_name','poc_name','pv_name','hiring_manager','client','interview_time'])
                ->make(true);
        }

        return view('admin.interview.index', $data);
    }

    public function create()
    {
        $data['menu'] = "Manage Interview";
        $data['interviewStatus'] = Interview::$interviewStatusOptions;
        return view("admin.interview.create",$data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'interview_date' => 'required|date',
            'interview_time' => 'required|date_format:H:i',
            'candidate_phone_number' => 'required|numeric|digits:10',
            'candidate_email' => 'required|email',
            'time_zone' => 'required',
            'status' => 'required',
        ]);

        $input = $request->all();
        $input['user_id'] = Auth::user()->id;
        $interview = Interview::create($input);

        $inputData['submission_id']  = $interview->submission_id;
        $inputData['requirement_id'] = $interview->Submission->Requirement->id;
        $inputData['entity_type']    = EntityHistory::ENTITY_TYPE_INTERVIEW_STATUS;
        $inputData['entity_value']   = $interview->status;

        EntityHistory::create($inputData);

        \Session::flash('success', 'Interview has been inserted successfully!');
        return redirect()->route('interview.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $data['menu'] = "My Interview";
        $data['interview'] = Interview::where('id',$id)->first();
        $data['interviewStatus'] = Interview::$interviewStatusOptions;
        return view('admin.interview.edit',$data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'interview_date' => 'required|date',
            'interview_time' => 'required|date_format:H:i',
            'candidate_phone_number' => 'required|numeric|digits:10',
            'candidate_email' => 'required|email',
            'time_zone' => 'required',
            'status' => 'required',
        ]);

        $input = $request->all();
        $requirement = Interview::where('id',$id)->first();
        $requirement->update($request->all());

        \Session::flash('success','Interview has been updated successfully!');
        return redirect()->route('interview.index');
    }

    public function destroy($id)
    {
        //
    }

    public function changeInterviewStatus(Request $request, $id){
        $interview = Interview::where('id',$id)->first();
        if(empty($interview)){
            $data['status'] = 0;
            return $data;
        }
        $input['status'] = $request['status'];
        $interview->update($input);

        $inputData['submission_id']  = $interview->submission_id;
        $inputData['requirement_id'] = $interview->Submission->Requirement->id;
        $inputData['entity_type']    = EntityHistory::ENTITY_TYPE_INTERVIEW_STATUS;
        $inputData['entity_value']   = $interview->status;

        EntityHistory::create($inputData);

        $data['status'] = 1;
        $data['updated_date_html'] = getEntityLastUpdatedAtHtml(EntityHistory::ENTITY_TYPE_PV_STATUS,$interview->submission_id);
        return $data;
    }

    function getCandidatesName(Request $request) {
        if(empty($request->job_id)){
            return 0;
        }
        $requirementId = Requirement::where('job_id',$request->job_id)->pluck('id')->first();
        $candidateData = Submission::where('requirement_id',$requirementId)->where('status',Submission::STATUS_ACCEPT)->whereNotNull('name')->select('name','id')->orderBy('id', 'DESC')->get()->unique('name');

        $data['status']        = 0;
        $data['cnadidateName'] = '';

        if(!count($candidateData)){
            return $data;
        }

        $data['status'] = 1;
        $option = '<option value="">Please Select Candidate Name</option>';
        foreach ($candidateData as $candidate){
            $option .= '<option value="'.$candidate['id'].'" data-id="'.$candidate['id'].'">'.$candidate['name'].'</option>';
        }
        $data['cnadidateName'] .= ' <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label col-md-12" for="candidateSelection">Employee Name</label>
                                        <select class="form-control select2 col-md-12" id="candidateSelection" style="width: 100%" onChange="loadCandidateData(event)">
                                            '.$option.'
                                        </select>
                                    </div>
                                </div>';
        
        return $data;
    }

    public function getCandidateData(Request $request){
        $data['status'] = 0;
        
        if(!$request->candidate_id){
            return $data;
        }

        $submissionData = Submission::where('id',$request->candidate_id)->first();

        if(empty($submissionData)){
            return $data;
        }

        $candidateData['client']                 = $submissionData->Requirement->client_name;
        $candidateData['candidate_phone_number'] = $submissionData->phone;
        $candidateData['candidate_email']        = $submissionData->email;
        $candidateData['recruiter_name']         = $submissionData->Recruiters->name;

        $data['status']        = 1;
        $data['candidateData'] = $candidateData;

        return $data;
    }
}