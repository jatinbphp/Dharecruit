<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Requirement;
use App\Models\Submission;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('accessright:manage_submission');
    }

    public function index(Request $request){
        $data['menu'] = "Requirements";
        $data['search'] = $request['search'];

        if ($request->ajax()) {
            $data = $this->Filter($request);
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    if($row['submissionCounter'] < 3){
                        $rId = !empty($row->recruiter) ? explode(',',$row->recruiter) : [];
                        if(!empty($rId) && in_array(Auth::user()->id,$rId)){
                            $btn = '<div class="btn-group btn-group-sm mr-2"><a href="'.url('admin/submission/'.$row->id).'"><button class="btn btn-sm btn-info tip" data-toggle="tooltip" title="View Submission" data-trigger="hover" type="submit" ><i class="fa fa-eye"></i></button></a></div>';
                            $btn .= '<div class="btn-group btn-group-sm"><a href="'.url('admin/submission/new/'.$row->id).'"><button class="btn btn-sm btn-success tip" data-toggle="tooltip" title="Add New Submission" data-trigger="hover" type="submit" ><i class="fa fa-upload"></i></button></a></div>';
                        }else{
                            $btn = '<span data-toggle="tooltip" title="Assign Requirement" data-trigger="hover">
                                    <button class="btn btn-sm btn-warning assignRequirement mr-2" data-id="'.$row->id.'" type="button"><i class="fa fa-plus-square"></i></button>
                                </span>';
                        }
                    }else{
                        $btn = '<div class="btn-group btn-group-sm mr-2"><button class="btn btn-sm btn-danger tip" data-toggle="tooltip" title="Hold Submission" data-trigger="hover" type="button" ><i class="fa fa-ban"></i> Hold</button></div>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $data['type'] = 1;
        return view('admin.submission.index', $data);
    }

    public function myRequirement(Request $request){
        $data['menu'] = "My Requirements";
        $data['search'] = $request['search'];

        if ($request->ajax()) {
            $request['authId'] = Auth::user()->id;
            $data = $this->Filter($request);
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    if($row->submissionCounter < 3){
                        $rId = !empty($row->recruiter) ? explode(',',$row->recruiter) : [];
                        if(!empty($rId) && in_array(Auth::user()->id,$rId)){
                            $btn = '<div class="btn-group btn-group-sm mr-2"><a href="'.url('admin/submission/'.$row->id).'"><button class="btn btn-sm btn-info tip" data-toggle="tooltip" title="View Submission" data-trigger="hover" type="submit" ><i class="fa fa-eye"></i></button></a></div>';
                            $btn .= '<div class="btn-group btn-group-sm"><a href="'.url('admin/submission/new/'.$row->id).'"><button class="btn btn-sm btn-success tip" data-toggle="tooltip" title="Add New Submission" data-trigger="hover" type="submit" ><i class="fa fa-upload"></i></button></a></div>';
                        }else{
                            $btn = '<span data-toggle="tooltip" title="Assign Requirement" data-trigger="hover">
                                    <button class="btn btn-sm btn-warning assignRequirement mr-2" data-id="'.$row->id.'" type="button"><i class="fa fa-plus-square"></i></button>
                                </span>';
                        }
                    }else{
                        $btn = '<div class="btn-group btn-group-sm mr-2"><button class="btn btn-sm btn-danger tip" data-toggle="tooltip" title="Hold Submission" data-trigger="hover" type="button" ><i class="fa fa-ban"></i> Hold</button></div>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $data['type'] = 2;
        return view('admin.submission.index', $data);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'location' => 'required',
            'phone' => 'required',
            'employer_detail' => 'required',
            'work_authorization' => 'required',
            'recruiter_rate' => 'required',
            'last_4_ssn' => 'required',
            'education_details' => 'required',
            'resume_experience' => 'required',
            'linkedin_id' => 'required',
            'relocation' => 'required',
            'vendor_rate' => 'required',
            'resume' => 'required|mimes:doc,docx,pdf',
        ]);

        $input = $request->all();
        $input['user_id'] = Auth::user()->id;
        if($file = $request->file('resume')){
            $input['documents'] = $this->fileMove($file,'user_documents');
        }
        Submission::create($input);

        $requirement = Requirement::where('id',$request['requirement_id'])->first();
        if($requirement['submissionCounter'] < 3){
            $in['submissionCounter'] = $requirement['submissionCounter'] + 1;
            $requirement->update($in);
        }

        if($requirement['submissionCounter'] == 3){
            $in['status'] = 'hold';
            $requirement->update($in);
        }

        \Session::flash('success', 'New submission has been inserted successfully!');
        return redirect(route('submission.show',['submission'=>$request['requirement_id']]));
    }

    public function show(Request $request, $id)
    {
        $data['menu'] = "Requirements";
        $data['all_submission'] = Submission::where('requirement_id',$id)->get();

        if ($request->ajax()) {
            $user = $this->getUser();
            if($user['role'] == 'admin'){
                $data = Submission::where('requirement_id',$id)->select();
            }else{
                $data = Submission::where('user_id',$user['id'])->where('requirement_id',$id)->select();
            }

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
                    $btn = '<span data-toggle="tooltip" title="Delete Submission" data-trigger="hover">
                                    <button class="btn btn-sm btn-danger deleteResume" data-id="'.$row->id.'" type="button"><i class="fa fa-trash"></i></button>
                                </span>';
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
        $data['requirement'] = Requirement::where('id',$id)->first();
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
        //
    }

    public function update(Request $request, $id)
    {
        //
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
}
