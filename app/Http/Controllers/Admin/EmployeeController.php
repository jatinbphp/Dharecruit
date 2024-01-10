<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Requirement;
use App\Models\Submission;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('accessright:manage_employee');
    }

    public function index(Request $request)
    {
        $data['menu'] = "Employee";

        if ($request->ajax()) {
            $user = $this->getUser();
            $data = Admin::where('role','employee')->select();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function($row){
                    $statusBtn = '';
                    if ($row->status == "active") {
                        $statusBtn .= '<div class="btn-group-horizontal" id="assign_remove_"'.$row->id.'">
                        <button class="btn btn-success unassign ladda-button" data-style="slide-left" id="remove" url="'.route('employee.unassign').'" ruid="'.$row->id.'"  type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Active</span> </button>
                                                </div>';
                        $statusBtn .= '<div class="btn-group-horizontal" id="assign_add_"'.$row->id.'"  style="display: none"  >
                                                    <button class="btn btn-danger assign ladda-button" data-style="slide-left" id="assign" uid="'.$row->id.'" url="'.route('employee.assign').'" type="button"  style="height:28px; padding:0 12px"><span class="ladda-label">In Active</span></button>
                                                </div>';
                    } else {
                        $statusBtn .= '<div class="btn-group-horizontal" id="assign_add_"'.$row->id.'">
                                                    <button class="btn btn-danger assign ladda-button" id="assign" data-style="slide-left" uid="'.$row->id.'" url="'.route('employee.assign').'"  type="button" style="height:28px; padding:0 12px"><span class="ladda-label">In Active</span></button>
                                                </div>';
                        $statusBtn .= '<div class="btn-group-horizontal" id="assign_remove_"'.$row->id.'" style="display: none" >
                                                    <button class="btn  btn-success unassign ladda-button" id="remove" ruid="'.$row->id.'" data-style="slide-left" url="'.route('employee.unassign').'" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Active</span></button>
                                                </div>';
                    }
                    return $statusBtn;
                })
                ->addColumn('action', function($row){
                    $btn = '<div class="btn-group btn-group-sm"><a href="'.url('admin/employee/'.$row->id.'/edit').'"><button class="btn btn-sm btn-info tip" data-toggle="tooltip" title="Edit PV Company" data-trigger="hover" type="submit" ><i class="fa fa-edit"></i></button></a></div>';

//                    $btn .= '<span data-toggle="tooltip" title="Delete Moi" data-trigger="hover">
//                                    <button class="btn btn-sm btn-danger deletePvCompany" data-id="'.$row->id.'" type="button"><i class="fa fa-trash"></i></button>
//                                </span>';
                    return $btn;
                })
                ->rawColumns(['status','action'])
                ->make(true);
        }

        return view('admin.employee.index', $data);
    }

    public function create()
    {

    }

    public function store(Request $request)
    {

    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $data['menu'] = "Employee";
        $data['employee'] = Admin::where('id',$id)->first();
        return view('admin.employee.edit',$data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required',
        ]);

        $input = $request->all();
        unset($input['email']);
        if(!empty($request->email)){
            Submission::where('employee_email', $request->email)->update([
                'employer_name' => $request->name,
                'employee_name' => $request->employee_name,
                'employee_phone' => $request->phone,
            ]);
        }
        $employee = Admin::findorFail($id);
        $employee->update($input);

        \Session::flash('success','Employee has been updated successfully!');
        return redirect()->route('employee.index');
    }

    public function destroy($id)
    {
        $employee = Admin::findOrFail($id);
        if(!empty($employee)){
            $employee->delete();
            return 1;
        }else{
            return 0;
        }
    }

    public function assign(Request $request){
        $employee = Admin::findorFail($request['id']);
        $employee['status'] = "active";
        $employee->update($request->all());
    }

    public function unassign(Request $request){
        $employee = Admin::findorFail($request['id']);
        $employee['status'] = "inactive";
        $employee->update($request->all());
    }
}
