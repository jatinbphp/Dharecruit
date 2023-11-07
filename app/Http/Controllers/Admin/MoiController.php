<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Moi;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MoiController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('accessright:manage_moi');
    }

    public function index(Request $request)
    {
        $data['menu'] = "Moi";
        $data['search'] = $request['search'];

        if ($request->ajax()) {
            $user = $this->getUser();
            if($user['role'] == 'admin'){
                $data = Moi::select();
            }else{
                $data = Moi::where('user_id',$user['id'])->select();
            }

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function($row){
                    $statusBtn = '';
                    if ($row->status == "active") {
                        $statusBtn .= '<div class="btn-group-horizontal" id="assign_remove_"'.$row->id.'">
                        <button class="btn btn-success unassign ladda-button" data-style="slide-left" id="remove" url="'.route('moi.unassign').'" ruid="'.$row->id.'"  type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Active</span> </button>
                                                </div>';
                        $statusBtn .= '<div class="btn-group-horizontal" id="assign_add_"'.$row->id.'"  style="display: none"  >
                                                    <button class="btn btn-danger assign ladda-button" data-style="slide-left" id="assign" uid="'.$row->id.'" url="'.route('moi.assign').'" type="button"  style="height:28px; padding:0 12px"><span class="ladda-label">In Active</span></button>
                                                </div>';
                    } else {
                        $statusBtn .= '<div class="btn-group-horizontal" id="assign_add_"'.$row->id.'">
                                                    <button class="btn btn-danger assign ladda-button" id="assign" data-style="slide-left" uid="'.$row->id.'" url="'.route('moi.assign').'"  type="button" style="height:28px; padding:0 12px"><span class="ladda-label">In Active</span></button>
                                                </div>';
                        $statusBtn .= '<div class="btn-group-horizontal" id="assign_remove_"'.$row->id.'" style="display: none" >
                                                    <button class="btn  btn-success unassign ladda-button" id="remove" ruid="'.$row->id.'" data-style="slide-left" url="'.route('moi.unassign').'" type="button" style="height:28px; padding:0 12px"><span class="ladda-label">Active</span></button>
                                                </div>';
                    }
                    return $statusBtn;
                })
                ->addColumn('action', function($row){
                    $btn = '<div class="btn-group btn-group-sm"><a href="'.url('admin/moi/'.$row->id.'/edit').'"><button class="btn btn-sm btn-info tip" data-toggle="tooltip" title="Edit Moi" data-trigger="hover" type="submit" ><i class="fa fa-edit"></i></button></a></div>';

                    $btn .= '<span data-toggle="tooltip" title="Delete Moi" data-trigger="hover">
                                    <button class="btn btn-sm btn-danger deleteMoi" data-id="'.$row->id.'" type="button"><i class="fa fa-trash"></i></button>
                                </span>';
                    return $btn;
                })
                ->rawColumns(['status','action'])
                ->make(true);
        }

        return view('admin.moi.index', $data);
    }

    public function create()
    {
        $data['menu'] = "Moi";
        return view("admin.moi.create",$data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required',
        ]);

        $input = $request->all();
        $input['user_id'] = Auth::user()->id;
        $moi = Moi::create($input);

        \Session::flash('success', 'Moi has been inserted successfully!');
        return redirect()->route('moi.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $data['menu'] = "Moi";
        $data['moi'] = Moi::where('id',$id)->first();
        return view('admin.moi.edit',$data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required',
        ]);

        $input = $request->all();
        $moi = Moi::findorFail($id);
        $moi->update($input);

        \Session::flash('success','Moi has been updated successfully!');
        return redirect()->route('moi.index');
    }

    public function destroy($id)
    {
        $mois = Moi::findOrFail($id);
        if(!empty($mois)){
            $mois->delete();
            return 1;
        }else{
            return 0;
        }
    }

    public function assign(Request $request){
        $moi = Moi::findorFail($request['id']);
        $moi['status'] = "active";
        $moi->update($request->all());
    }

    public function unassign(Request $request){
        $moi = Moi::findorFail($request['id']);
        $moi['status'] = "inactive";
        $moi->update($request->all());
    }
}
