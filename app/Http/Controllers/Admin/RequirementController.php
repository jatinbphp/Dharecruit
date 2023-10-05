<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Moi;
use App\Models\PVCompany;
use App\Models\Requirement;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequirementController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('accessright:manage_requirement');
    }

    public function index(Request $request)
    {
        $data['menu'] = "Requirements";
        $data['search'] = $request['search'];

        if ($request->ajax()) {
            $user = $this->getUser();
            if($user['role'] == 'admin'){
                $data = Requirement::select();
            }else{
                $data = Requirement::where('user_id',Auth::user()->id)->select();
            }

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function($row){
                    $statusBtn = '';
                    if ($row->status == "active") {
                        $statusBtn .= '<div class="btn-group-horizontal" id="assign_remove_"'.$row->id.'">
                        <button class="btn btn-success unassign ladda-button" data-style="slide-left" id="remove" url="'.route('requirement.unassign').'" ruid="'.$row->id.'"  type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Active</span> </button>
                                                </div>';
                        $statusBtn .= '<div class="btn-group-horizontal" id="assign_add_"'.$row->id.'"  style="display: none"  >
                                                    <button class="btn btn-danger assign ladda-button" data-style="slide-left" id="assign" uid="'.$row->id.'" url="'.route('requirement.assign').'" type="button"  style="height:28px; padding:0 12px"><span class="ladda-label">In Active</span></button>
                                                </div>';
                    } else {
                        $statusBtn .= '<div class="btn-group-horizontal" id="assign_add_"'.$row->id.'">
                                                    <button class="btn btn-danger assign ladda-button" id="assign" data-style="slide-left" uid="'.$row->id.'" url="'.route('requirement.assign').'"  type="button" style="height:28px; padding:0 12px"><span class="ladda-label">In Active</span></button>
                                                </div>';
                        $statusBtn .= '<div class="btn-group-horizontal" id="assign_remove_"'.$row->id.'" style="display: none" >
                                                    <button class="btn  btn-success unassign ladda-button" id="remove" ruid="'.$row->id.'" data-style="slide-left" url="'.route('requirement.unassign').'" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Active</span></button>
                                                </div>';
                    }
                    return $statusBtn;
                })
                ->addColumn('action', function($row){
                    $btn = '<div class="btn-group btn-group-sm"><a href="'.url('admin/requirement/'.$row->id.'/edit').'"><button class="btn btn-sm btn-info tip" data-toggle="tooltip" title="Edit Requirement" data-trigger="hover" type="submit" ><i class="fa fa-edit"></i></button></a></div>';

                    $btn .= '<span data-toggle="tooltip" title="Delete Requirement" data-trigger="hover">
                                    <button class="btn btn-sm btn-danger deleteRequirement" data-id="'.$row->id.'" type="button"><i class="fa fa-trash"></i></button>
                                </span>';
                    return $btn;
                })
                ->rawColumns(['status','action'])
                ->make(true);
        }

        return view('admin.requirement.index', $data);
    }

    public function create()
    {
        $data['menu'] = "Requirements";
        $data['category'] = Category::where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
        $data['moi'] = Moi::where('user_id',Auth::user()->id)->where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
        $data['pv_company'] = PVCompany::where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
        return view("admin.requirement.create",$data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'job_title' => 'required',
            'no_of_position' => 'required',
            'experience' => 'required',
            'location' => 'required',
            'work_type' => 'required',
            'duration' => 'required',
            'visa' => 'required',
            'client' => 'required',
            'vendor_rate' => 'required',
            'my_rate' => 'required',
            'priority' => 'required',
            'term' => 'required',
            'category' => 'required',
            'moi' => 'required',
            'job_keyword' => 'required',
            'description' => 'required',
            'pv_company_name' => 'required',
            'poc_name' => 'required',
            'poc_email' => 'required',
            'poc_phone_number' => 'required',
            'client_name' => 'required',
        ]);

        $input = $request->all();
        $input['user_id'] = Auth::user()->id;
        Requirement::create($input);

        \Session::flash('success', 'Requirement has been inserted successfully!');
        return redirect()->route('requirement.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $data['menu'] = "Requirements";
        $data['requirement'] = Requirement::where('id',$id)->first();
        $data['category'] = Category::where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
        $data['moi'] = Moi::where('user_id',Auth::user()->id)->where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
        $data['pv_company'] = PVCompany::where('user_id',Auth::user()->id)->where('status','active')->pluck('name','id')->prepend('Please Select','');
        return view('admin.requirement.edit',$data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'job_title' => 'required',
            'no_of_position' => 'required',
            'experience' => 'required',
            'location' => 'required',
            'work_type' => 'required',
            'duration' => 'required',
            'visa' => 'required',
            'client' => 'required',
            'vendor_rate' => 'required',
            'my_rate' => 'required',
            'priority' => 'required',
            'term' => 'required',
            'category' => 'required',
            'moi' => 'required',
            'job_keyword' => 'required',
            'description' => 'required',
            'pv_company_name' => 'required',
            'poc_name' => 'required',
            'poc_email' => 'required',
            'poc_phone_number' => 'required',
            'client_name' => 'required',
        ]);

        $input = $request->all();
        $requirement = Requirement::findorFail($id);
        $requirement->update($input);

        \Session::flash('success','Requirement has been updated successfully!');
        return redirect()->route('requirement.index');
    }

    public function destroy($id)
    {
        $requirements = Requirement::findOrFail($id);
        if(!empty($requirements)){
            $requirements->delete();
            return 1;
        }else{
            return 0;
        }
    }

    public function assign(Request $request){
        $requirement = Requirement::findorFail($request['id']);
        $requirement['status'] = "active";
        $requirement->update($request->all());
    }

    public function unassign(Request $request){
        $requirement = Requirement::findorFail($request['id']);
        $requirement['status'] = "inactive";
        $requirement->update($request->all());
    }
}
