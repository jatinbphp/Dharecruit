<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CandidateController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('accessright:manage_candidate');
    }

    public function index(Request $request)
    {
        $data['menu'] = "Candidate";

        if ($request->ajax()) {
            $data = Submission::select(['id', 'candidate_id', 'name', 'email', 'phone']);

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    return '<div class="btn-group btn-group-sm"><a href="'.url('admin/manage_candidate/'.$row->id.'/edit').'"><button class="btn btn-sm btn-info tip" data-toggle="tooltip" title="Edit PV Company" data-trigger="hover" type="submit" ><i class="fa fa-edit"></i></button></a></div>';
                })
                ->rawColumns(['status','action'])
                ->make(true);
        }

        return view('admin.candidate.index', $data);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $data['menu'] = "Candidate";
        $data['candidate'] = Submission::where('id',$id)->first();
        return view('admin.candidate.edit',$data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required|numeric|digits:10',
            'email' => 'required|email',
        ]);

        $input = $request->all();
        unset($input['email']);
        if(!empty($request->email)){
            Submission::where('email', $request->email)
                ->update([
                        'name' => $request->name,
                        'phone' => $request->phone,
                    ]
                );
        }

        \Session::flash('success','Candidate has been updated successfully!');
        return redirect()->route('manage_candidate.index');
    }

    public function destroy($id)
    {
        //
    }
}
