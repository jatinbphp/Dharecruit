<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use DataTables;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('accessright:manage_permission');
    }

    public function index(Request $request)
    {
        $data['menu'] = "Permission";
        $data['search'] = $request['search'];

        if ($request->ajax()) {
            $data = Permission::select();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('type', function($row){
                    return ucwords(str_replace('_',' ',$row['type']));
                })
                ->addColumn('action', function($row){
                    $btn = '<div class="btn-group btn-group-sm"><a href="'.url('admin/permission/'.$row->id.'/edit').'"><button class="btn btn-sm btn-info tip" data-toggle="tooltip" title="Edit Permission" data-trigger="hover" type="submit" ><i class="fa fa-edit"></i></button></a></div>';
                    return $btn;
                })
                ->rawColumns(['type','action'])
                ->make(true);
        }

        return view('admin.permission.index', $data);
    }

    public function create(){
        //
    }

    public function store(Request $request){
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $data['menu'] = "Permission";
        $data['permission'] = Permission::where('id',$id)->first();
        $data['permission']['type'] = ucfirst(str_replace('_',' ',$data['permission']['type']));
        $data['selectedModules'] = !empty($data['permission']['access_modules']) ? explode(",",$data['permission']['access_modules']) :[];
        return view('admin.permission.edit',$data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'access_modules' => 'required',
        ]);

        $input = $request->all();
        $permission = Permission::findorFail($id);
        $input['type'] = $permission['type'];
        $input['access_modules'] = !empty($request['access_modules']) ? implode(',', $request['access_modules']) : "";
        $permission->update($input);

        \Session::flash('success','Permission has been updated successfully!');
        return redirect()->route('permission.index');
    }

    public function destroy($id){
        //
    }

    public function assign(Request $request){
        //
    }

    public function unassign(Request $request){
        //
    }
}
