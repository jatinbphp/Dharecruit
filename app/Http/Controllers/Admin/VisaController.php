<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Visa;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Auth;

class VisaController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('accessright:manage_visa');
    }

    public function index(Request $request)
    {
        $data['menu'] = "Visa";
        $data['search'] = $request['search'];

        if ($request->ajax()) {
            $user = $this->getUser();
            if($user['role'] == 'admin'){
                $data = Visa::select();
            }else{
                $data = Visa::where('user_id',$user['id'])->select();
            }

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function($row){
                    $statusBtn = '';
                    if ($row->status == "active") {
                        $statusBtn .= '<div class="btn-group-horizontal" id="assign_remove_"'.$row->id.'">
                        <button class="btn btn-success unassign ladda-button" data-style="slide-left" id="remove" url="'.route('visa.unassign').'" ruid="'.$row->id.'"  type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Active</span> </button>
                                                </div>';
                        $statusBtn .= '<div class="btn-group-horizontal" id="assign_add_"'.$row->id.'"  style="display: none"  >
                                                    <button class="btn btn-danger assign ladda-button" data-style="slide-left" id="assign" uid="'.$row->id.'" url="'.route('visa.assign').'" type="button"  style="height:28px; padding:0 12px"><span class="ladda-label">In Active</span></button>
                                                </div>';
                    } else {
                        $statusBtn .= '<div class="btn-group-horizontal" id="assign_add_"'.$row->id.'">
                                                    <button class="btn btn-danger assign ladda-button" id="assign" data-style="slide-left" uid="'.$row->id.'" url="'.route('visa.assign').'"  type="button" style="height:28px; padding:0 12px"><span class="ladda-label">In Active</span></button>
                                                </div>';
                        $statusBtn .= '<div class="btn-group-horizontal" id="assign_remove_"'.$row->id.'" style="display: none" >
                                                    <button class="btn  btn-success unassign ladda-button" id="remove" ruid="'.$row->id.'" data-style="slide-left" url="'.route('visa.unassign').'" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Active</span></button>
                                                </div>';
                    }
                    return $statusBtn;
                })
                ->addColumn('action', function($row){
                    $btn = '<div class="btn-group btn-group-sm"><a href="'.url('admin/visa/'.$row->id.'/edit').'"><button class="btn btn-sm btn-info tip" data-toggle="tooltip" title="Edit Moi" data-trigger="hover" type="submit" ><i class="fa fa-edit"></i></button></a></div>';

                    $btn .= '<span data-toggle="tooltip" title="Delete Visa" data-trigger="hover">
                                    <button class="btn btn-sm btn-danger deleteVisa" data-id="'.$row->id.'" type="button"><i class="fa fa-trash"></i></button>
                                </span>';
                    return $btn;
                })
                ->rawColumns(['status','action'])
                ->make(true);
        }

        return view('admin.visa.index', $data);
    }

    public function create()
    {
        $data['menu'] = "Visa";
        return view("admin.visa.create",$data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required',
        ]);

        $input = $request->all();
        $input['user_id'] = Auth::user()->id;
        $visa = Visa::create($input);

        \Session::flash('success', 'Visa has been inserted successfully!');
        return redirect()->route('visa.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $data['menu'] = "Visa";
        $data['visa'] = Visa::where('id',$id)->first();
        return view('admin.visa.edit',$data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required',
        ]);

        $input = $request->all();
        $visa = Visa::findorFail($id);
        $visa->update($input);

        \Session::flash('success','Visa has been updated successfully!');
        return redirect()->route('visa.index');
    }

    public function destroy($id)
    {
        $visa = Visa::findOrFail($id);
        if(!empty($visa)){
            $visa->delete();
            return 1;
        }else{
            return 0;
        }
    }

    public function assign(Request $request){
        $visa = Visa::findorFail($request['id']);
        $visa['status'] = "active";
        $visa->update($request->all());
    }

    public function unassign(Request $request){
        $visa = Visa::findorFail($request['id']);
        $visa['status'] = "inactive";
        $visa->update($request->all());
    }
}
