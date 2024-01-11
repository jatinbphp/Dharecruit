<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PVCompany;
use App\Models\Requirement;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PVCompanyController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('accessright:manage_pvcompany');
    }

    public function index(Request $request)
    {
        $data['menu'] = "PV Company";
        $data['search'] = $request['search'];

        if ($request->ajax()) {
            $user = $this->getUser();
            if($user['role'] == 'admin'){
                $data = PVCompany::select();
            }else{
                $data = PVCompany::where('user_id',Auth::user()->id)->select();
            }

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function($row){
                    $statusBtn = '';
                    if ($row->status == "active") {
                        $statusBtn .= '<div class="btn-group-horizontal" id="assign_remove_"'.$row->id.'">
                        <button class="btn btn-success unassign ladda-button" data-style="slide-left" id="remove" url="'.route('pv_company.unassign').'" ruid="'.$row->id.'"  type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Active</span> </button>
                                                </div>';
                        $statusBtn .= '<div class="btn-group-horizontal" id="assign_add_"'.$row->id.'"  style="display: none"  >
                                                    <button class="btn btn-danger assign ladda-button" data-style="slide-left" id="assign" uid="'.$row->id.'" url="'.route('pv_company.assign').'" type="button"  style="height:28px; padding:0 12px"><span class="ladda-label">In Active</span></button>
                                                </div>';
                    } else {
                        $statusBtn .= '<div class="btn-group-horizontal" id="assign_add_"'.$row->id.'">
                                                    <button class="btn btn-danger assign ladda-button" id="assign" data-style="slide-left" uid="'.$row->id.'" url="'.route('pv_company.assign').'"  type="button" style="height:28px; padding:0 12px"><span class="ladda-label">In Active</span></button>
                                                </div>';
                        $statusBtn .= '<div class="btn-group-horizontal" id="assign_remove_"'.$row->id.'" style="display: none" >
                                                    <button class="btn  btn-success unassign ladda-button" id="remove" ruid="'.$row->id.'" data-style="slide-left" url="'.route('pv_company.unassign').'" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Active</span></button>
                                                </div>';
                    }
                    return $statusBtn;
                })
                ->addColumn('action', function($row){
                    $btn = '<div class="btn-group btn-group-sm"><a href="'.url('admin/pv_company/'.$row->id.'/edit').'"><button class="btn btn-sm btn-info tip" data-toggle="tooltip" title="Edit PV Company" data-trigger="hover" type="submit" ><i class="fa fa-edit"></i></button></a></div>';

//                    $btn .= '<span data-toggle="tooltip" title="Delete Moi" data-trigger="hover">
//                                    <button class="btn btn-sm btn-danger deletePvCompany" data-id="'.$row->id.'" type="button"><i class="fa fa-trash"></i></button>
//                                </span>';
                    return $btn;
                })
                ->rawColumns(['status','action'])
                ->make(true);
        }

        return view('admin.pv_company.index', $data);
    }

    public function create()
    {
        $data['menu'] = "PV Company";
        return view("admin.pv_company.create",$data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required',
            'email' => 'required|email',
            'phone' => 'required|numeric|digits:10',
        ]);

        $input = $request->all();
        $input['user_id'] = Auth::user()->id;
        $pv_company = PVCompany::create($input);

        \Session::flash('success', 'PV Company has been inserted successfully!');
        return redirect()->route('pv_company.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $data['menu'] = "PV Company";
        $data['pv_company'] = PVCompany::where('id',$id)->first();
        return view('admin.pv_company.edit',$data);
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
            Requirement::where('poc_email', $request->email)->update([
                'pv_company_name' => $request->name,
                'poc_name' => $request->poc_name,
                'poc_phone_number' => $request->phone,
                'poc_location' => $request->poc_location,
                'pv_company_location' => $request->pv_company_location,
                'client_name' => $request->client_name,
            ]);
        }
        $pv_company = PVCompany::findorFail($id);
        $pv_company->update($input);

        \Session::flash('success','PV Company has been updated successfully!');
        return redirect()->route('pv_company.index');
    }

    public function destroy($id)
    {
        $pv_company = PVCompany::findOrFail($id);
        if(!empty($pv_company)){
            $pv_company->delete();
            return 1;
        }else{
            return 0;
        }
    }

    public function assign(Request $request){
        $pv_company = PVCompany::findorFail($request['id']);
        $pv_company['status'] = "active";
        $pv_company->update($request->all());
    }

    public function unassign(Request $request){
        $pv_company = PVCompany::findorFail($request['id']);
        $pv_company['status'] = "inactive";
        $pv_company->update($request->all());
    }
}
