<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Moi;
use DataTables;

class MoiController extends Controller{
    public function __construct(Request $request){
        $this->middleware('auth');
    }

    public function index(Request $request){
        $data['menu']="Moi";


         if ($request->ajax()) {
             $data = Moi::select();
            return Datatables::of($data)
                    ->addIndexColumn()
                    
                    ->addColumn('action', function($row){
                        $btn = '<div class="btn-group btn-group-sm"><a href="'.url('admin/moi/'.$row->id.'/edit').'"><button class="btn btn-sm btn-info tip" data-toggle="tooltip" title="Edit Stock" data-trigger="hover" type="submit" ><i class="fa fa-edit"></i></button></a></div>';
                        $btn .= '<span data-toggle="tooltip" title="Delete Stock" data-trigger="hover">
                                    <button class="btn btn-sm btn-danger delete_moi" data-id="'.$row->id.'" type="button"><i class="fa fa-trash"></i></button>
                                </span>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('moi.index', $data);
    }

    public function create(){
        $data['menu']="Mois";
        return view('moi.create',$data);
    }

    public function store(Request $request){
        $this->validate($request, [
            'name' => 'required',
        ]);

        $input = $request->all();
        $moi = moi::create($input);
        \Session::flash('success', 'moi has been inserted successfully!');
        return redirect()->route('moi.index');
    }

    public function show(string $id){
        //
    }

    public function edit(string $id){
        $data['menu']="Mois";
        $data['mois'] = moi::findorFail($id);
        return view('moi.edit',$data);
    }

    public function update(Request $request, $id){
        $this->validate($request, [
            'name' => 'required',
        ]);

        $input = $request->all();
        $moi = moi::findorFail($id);
        $moi->update($input);

        \Session::flash('success','moi has been updated successfully!');
         return redirect()->route('moi.index');
    }

    public function destroy($id){
        $moi = moi::findOrFail($id);
        if(!empty($moi)){
            $moi->delete();
            return response()->json('success', 200);
        }else{
           return response()->json('fail', 500);
        }
    }
    
}
