<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Requirement;
use App\Models\Submission;
use App\Models\Setting;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('accessright:manage_submission');
        $this->ExpireRequirement();
    }

    public function index(Request $request){
        $data['menu'] = "Requirements";
        $data['search'] = $request['search'];

        if ($request->ajax()) {
            $data = $this->Filter($request,'all');
            return $this->getListHtml($data, 'submission', $request);
        }
        $data['type'] = 1;
        $data['filterFile'] = 'requirement_filter';
        return view('admin.submission.index', $data);
    }

    public function myRequirement(Request $request){
        
        $data['menu'] = "My Requirements";
        $data['search'] = $request['search'];

        if ($request->ajax()) {
            $request['authId'] = Auth::user()->id;
            $data = $this->Filter($request);
            return $this->getListHtml($data, 'submission', $request);
        }
        $data['type'] = 2;
        $data['filterFile'] = 'common_filter';
        return view('admin.submission.index', $data);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $fileds = [
            'name' => 'required',
            'email' => 'required',
            'location' => 'required',
            'phone' => 'required|numeric',
            'employer_detail' => 'required',
            'work_authorization' => 'required',
            'recruiter_rate' => 'required',
            'last_4_ssn' => 'required',
            'education_details' => 'required',
            'resume_experience' => 'required',
            'linkedin_id' => 'required',
            'relocation' => 'required',
            //'vendor_rate' => 'required',
            'employer_name' => 'required',
            'employee_name' => 'required',
            'employee_email' => 'required|email',
            'employee_phone' => 'required|numeric|digits:10'
        ];

        if(empty($request['resume']) && empty($request['existResume'])){
            $fileds['resume'] = 'required|mimes:doc,docx,pdf';
        }

        $this->validate($request, $fileds);
        $requirement = Requirement::where('id',$request['requirement_id'])->first();
        $existSubmission = Submission::where('requirement_id',$request['requirement_id'])->where('email',$request['email'])->first();
        if(!empty($existSubmission)){
            $msg = $existSubmission['name'].':'.$existSubmission['id'].' has been already submitted to the '.$requirement['job_title'];
            \Session::flash('danger', $msg);
            return redirect()->back();
        }

        $input = $request->all();
        $input['user_id'] = Auth::user()->id;

        if(!empty($request['resume'])){
            if($file = $request->file('resume')){
                $input['documents'] = $this->fileMove($file,'user_documents');
            }
        } else if(!empty($request['existResume'])){
            $input['documents'] = $request['existResume'];
        }else{
            $this->validate($request, [
                'resume' => 'required|mimes:doc,docx,pdf',
            ]);
        }

        $submission = Submission::create($input);

        if($submission && !empty($request->email)){
            $existingSubmission = Submission::where('email',$request->email)->first();
            if(!empty($existingSubmission) && $existingSubmission->id){
                $submission->candidate_id = $existingSubmission->id;
                $submission->save();
            }else{
                $submission->candidate_id = $submission->id;
                $submission->save();
            }
            $this->addNewDataInLog($submission);
            $cloneSubmission = $submission->replicate();
            $this->updateCandidateWithSameCandidateId($submission,$cloneSubmission->Requirement->job_id);
        }

        if($requirement['submissionCounter'] < 3){
            $in['submissionCounter'] = $requirement['submissionCounter'] + 1;
            $requirement->update($in);
        }

        if($requirement['submissionCounter'] == 3){
            $in['status'] = 'hold';
            $requirement->update($in);
        }

        if($submission){
            $this->addEmployeeDetails($submission);
        }

        \Session::flash('success', 'New submission has been inserted successfully!');
        return redirect()->route('submission.index');
    }

    public function show(Request $request, $id)
    {
        $data['menu'] = "Requirements";
        $data['sub_menu'] = "Submission";
        $data['all_submission'] = Submission::where('requirement_id',$id)->get();

        if ($request->ajax()) {
            $data = $this->submissionFilter($request,$id);
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('user_id', function($row){
                    return $row->Recruiters->name;
                })
                ->addColumn('documents', function($row){
                    return '<a href="'.asset('storage/'.$row->documents).'" target="_blank"><img src="'.url('assets/dist/img/resume.png').'" height="50"></a>';
                })
                ->addColumn('status', function($row){
                    return ucfirst($row->status);
                })
                ->addColumn('action', function($row){
                    $btn = '<div class="btn-group btn-group-sm view" data-cid="'.$row->id.'"><button class="btn btn-sm btn-default tip" data-toggle="tooltip" title="View Submission" data-trigger="hover"><i class="fa fa-eye"></i></button></a></div>';
                    return $btn;
                })
                ->rawColumns(['user_id','documents','status','action'])
                ->make(true);
        }

        $data['requirement'] = Requirement::where('id',$id)->first();
        return view('admin.submission.list',$data);
    }

    public function submissionAdd($id){
        $data['menu'] = "Requirements";
        $data['sub_menu'] = "Submission";
        $data['requirement'] = Requirement::where('id',$id)->first();
        $data['settings'] = $this->getSettingData();
        if(!empty($data['requirement'])){
            $user = Auth::user();
            $recruiter = explode(',',$data['requirement']['recruiter']);
            if($user['role'] == 'recruiter' && !empty($recruiter)){
                if(!in_array($user['id'],$recruiter)){
                    \Session::flash('danger', 'Requirement is not assigned to you');
                    return redirect(route('submission.index'));
                }
            }
        }
        $requirementCount = Requirement::where('id',$id)->first();
        if($requirementCount['submissionCounter'] == 3){
            \Session::flash('danger', 'Submission is hold!');
            return redirect(route('submission.index'));
        }
        return view('admin.submission.create',$data);
    }

    public function edit($id)
    {
        $data['menu'] = "Requirements";
        $data['submission'] = Submission::where('id',$id)->first();
        $data['sub_menu'] = "Submission";
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
        unset($input['name']);
        unset($input['email']);
        $Submission = Submission::where('id',$id)->first();
        $cloneSubmission = $Submission->replicate();
        $this->manageSubmissionLogs($input, $Submission, $cloneSubmission->Requirement->job_id);
        $Submission->update($input);
        $this->updateCandidateWithSameCandidateId($Submission,$cloneSubmission->Requirement->job_id);

        \Session::flash('success','Submission  has been updated successfully!');
        return redirect(route('submission.show',['submission'=>$Submission['requirement_id']]));
        // return redirect()->route('submission.index');
    }

    public function destroy($id)
    {
        $submission = Submission::findOrFail($id);
        if(!empty($submission)){
            Storage::delete($submission['documents']);
            $submission->delete();
            return 1;
        }else{
            return 0;
        }
    }

    public function assignSubmission($id){
        $requirement = Requirement::where('id',$id)->first();
        if(!empty($requirement)){
            $input['recruiter'] = !empty($requirement['recruiter']) ? $requirement['recruiter'].Auth::user()->id.',' : ','.Auth::user()->id.',';
            $requirement->update($input);
            return 1;
        }else{
            return 0;
        }
    }

    public function getAlreadyAddedUserDetail(Request $request){
        $status = 0;
        $responceData['status'] = $status;
        
        if(!$request || (!$request->get('email') && !$request->get('id'))){
            return $responceData;
        }

        $submissionId = $request->get('id');
        $submissionEmail = $request->get('email');
        
        $data = [];

        if (!empty($submissionEmail)){
            $data = Submission::where('email',$submissionEmail)->latest()->first();
        } else if(!empty($submissionId)){
            $data = Submission::where('candidate_id',$submissionId)->latest()->first();
        }

        if($data) {
            $status = 1;
        }

        $responceData['status'] = $status;
        $responceData['responceData'] = $data;

        return $responceData;
    }

    function getEmpName(Request $request) {
        if(Auth::user()->role == 'admin'){
            $allReqs = Admin::where('name',$request['employer_name'])->whereNotNull('name')->where('role','employee')->groupBy('employee_name')->select('employee_name','id')->get();
        }else{
            $allReqs = Admin::where('name',$request['employer_name'])->where('added_by',Auth::user()->id)->whereNotNull('name')->where('role','employee')->groupBy('employee_name')->select('employee_name','id')->get();
        }
        $data['status'] = 0;
        $data['empName'] = '';
        if(count($allReqs) > 0){
            $data['status'] = 1;
            $option = '<option value="">Please Select Employee Name</option>';
            foreach ($allReqs as $list){
                $option .= '<option value="'.$list['employee_name'].'" data-id="'.$list['id'].'">'.$list['employee_name'].'</option>';
            }
            $option .= '<option value="0">Add New Employee</option>';
            $data['empName'] .= ' <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label col-md-12" for="empSelection">Employee Name</label>
                                            <select class="form-control select2 col-md-12" id="empSelection" style="width: 100%" onChange="checkData(event)">
                                                '.$option.'
                                            </select>
                                        </div>
                                    </div>';
        }
        return $data;
    }

    function getEmpDetail(Request $request){
        $requs = Admin::orderBy('id', 'DESC')->where('employee_name',$request['employee_name'])->where('name',$request['employer_name'])->first();
        $data['status'] = 0;
        $data['requs'] = [];
        if(!empty($requs)){
            $data['status'] = 1;
            $data['requs'] = $requs;
        }
        return $data;
    }

    public function checkPvCompany(Request $request){
        $data['status'] = 0;
        if(empty($request) || empty($request->requirement_id) || empty($request->email)){
            return $data;
        }
        
        $data['status']            = 1;
        $data['isSamePvCandidate'] = $this->isSamePvCandidate($request->email, $request->requirement_id);

        return $data;
    }

    public function addEmployeeDetails($submission) {
        if(!$submission){
            return $this;
        }

        $employerName = $submission->employer_name;
        $employeeName = $submission->employee_name;
        $email        = $submission->employee_email;
        $phone        = $submission->employee_phone;

        $oldemployeeData = Admin::where('email', $email)->where('added_by', Auth::user()->id)->where('role','employee')->first();
        
        if($oldemployeeData){
            return $this;
        }

        Admin::create(
            [
                'added_by'      => Auth::user()->id,
                'name'          => $employerName,
                'email'         => $email,
                'phone'         => $phone,
                'employee_name' => $employeeName,
                'role'          => 'employee'
            ]
        );

        return $this;
    }

    public function checkEmpData(Request $request){
        if(empty($request->emp_email)){
            $data['status'] = 0;
            return $data;
        }

        $logggedInUserId = Auth::user()->id;
        $empEmail = $request->emp_email;
        $currentUserPocEmail = Admin::where(function ($query) use ($empEmail) {
            $query->where('email', '=', $empEmail)
                  ->orWhere('linked_data', 'like', '%'.$empEmail.'%');
            })->where('added_by', $logggedInUserId)->where('role','employee')->first(); 

        if(!empty($currentUserPocEmail)){
            $data['status'] = 1;
            $data['is_current_user_email'] = 1;
            $data['empdata'] = $currentUserPocEmail;
            $data['linking_data'] = $this->getEmployeeLinkData([], $currentUserPocEmail->email);
            return $data;
        }

        $otherUserEmpEmail = Admin::where(function ($query) use ($empEmail) {
            $query->where('email', '=', $empEmail)
                  ->orWhere('linked_data', 'like', '%'.$empEmail.'%');
            })->where('added_by','!=', $logggedInUserId)->where('role','employee')->first();

        if(!empty($otherUserEmpEmail)){
            $data['status'] = 1;
            $data['empdata'] = $otherUserEmpEmail;
            $data['emp_registered'] = 1;
            $data['linking_data'] =  $this->getEmployeeLinkData([], $otherUserEmpEmail->email);
            return $data;
        }

        $data['status'] = 1;
        $data['new_emp_email'] = 1;

        return $data;
    }

    public function saveEmpLinkingData(Request $request){
        if(empty($request->email)){
            $data['status'] = 0;
            return $data;
        }

        $email = $request->email;
        $type = $request->type;

        $existingRow = Admin::where('email', $request->emp_email)->where('role', 'employee')->first();

        if(!empty($existingRow)){
            $data['is_found'] = 1;
            $data['message'] = "Contact admin to merge vendor contact,  Email already exist for another contact!";
            return $data;
        }

        $oldData = Admin::where('email', $email)->where('role', 'employee')->first();
        
        if(empty($oldData)){
            $data['status'] = 0;
            return $data;
        }

        $linkedData = ($oldData->linked_data) ? json_decode($oldData->linked_data, 1) : [];

        $value = '';
        $linkType = '';
        $parentDiv = '';

        if($type == 'linking_email'){
            $value = $request->emp_email;
            $linkType = 'Employee Email';
            $parentDiv = 'linkEmployeeEmail';
        } elseif($type == 'linking_phone'){
            $value = $request->emp_phone;
            $linkType = 'Employee Phone Number';
            $parentDiv = 'linkEmployeePhoneNumber';
        }

        $found = 0;

        if($oldData && $oldData->linked_data){
            $linkedData = json_decode($oldData->linked_data, 1);
            foreach ($linkedData as $key => $linkValue) {
                if($key != $type){
                    continue;
                }
                foreach($linkValue as $values){
                    $existingValue = $values['value'];
                    if(strtolower($existingValue) == strtolower($value)){
                        $found = 1;
                    }
                }
            }
        }

        if($found){
            $data['is_found'] = 1;
            $data['message'] = "Contact admin to merge vendor contact,  $linkType already exist for another contact!";
            return $data;
        }

        $linkedValuesdata['value']    = $value;
        $linkedValuesdata['user_id']  = Auth::user()->id;;
        $linkedValuesdata['dateTime'] = \Carbon\Carbon::now();

        $linkedData[$type][] = $linkedValuesdata;

        Admin::where('email', $email)->update(['linked_data' => json_encode($linkedData)]);

        $data['status'] = 1;
        $data['user_name'] = Admin::getUserNameBasedOnId(Auth::user()->id);
        $data['date']  = date('m-d-y', strtotime(\Carbon\Carbon::now()));
        $data['value'] = $value;
        $data['parent_div'] = $parentDiv;
        return $data;
    }

}
