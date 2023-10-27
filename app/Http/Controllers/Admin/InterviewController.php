<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use App\Models\Interview;
use App\Models\Submission;
use App\Models\Requirement;
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
                    return $status;
                })
                ->addColumn('action', function($row){
                    return '<div class="btn-group btn-group-sm mr-2"><a href="'.url('admin/interview/'.$row->id.'/edit').'"><button class="btn btn-sm btn-default tip" data-toggle="tooltip" title="Edit Interview" data-trigger="hover" type="submit" ><i class="fa fa-edit"></i></button></a></div>';
                })
                ->rawColumns(['status','action'])
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
        Interview::create($input);

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
            return 0;
        }else{
            $input['status'] = $request['status'];
            $interview->update($input);
            return 1;
        }
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
