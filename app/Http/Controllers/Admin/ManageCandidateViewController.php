<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Setting;
use Illuminate\Http\Request;

class ManageCandidateViewController extends Controller
{
    public function index()
    {
        $data['menu'] = "Manage Candidate View";
        $data['allRecruiter'] = Admin::where('status', 'active')->where('role', 'recruiter')->pluck('name', 'id')->toArray();
        $data['userWiseTeam'] = getUserIdWiseTeamName();
        $data['userWiseLimit'] = Admin::where('status', 'active')->where('role', 'recruiter')->pluck('view_limit', 'id')->toArray();
        $globalValue = 0;
        $settingRow = Setting::where('name', 'candidate_global_view_count')->first();
        if(!empty($settingRow) && $settingRow->value){
            $globalValue = $settingRow->value;
        }
        $data['globalValue'] = $globalValue;

       return view('admin.candidate_view.index', $data);
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
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    public function updateRecruiterLimit(Request $request)
    {
        $data['status'] = 0;
        if(empty($request->recruiter_id) || empty($request->limit)){
            return $data;
        }

        $userRow = Admin::where('id', $request->recruiter_id)->first();
        if(!empty($userRow)){
            $userRow->view_limit = $request->limit;
            $userRow->save();
            $data['status'] = 1;
        }
        return $data;
    }

    public function updateRecruiterGlobalLimit(Request $request)
    {
        $data['status'] = 0;
        if(empty($request->limit)){
            return $data;
        }

        $settingRow = Setting::where('name', 'candidate_global_view_count')->first();
        if(!empty($settingRow)){
            $settingRow->value = $request->limit;
            $settingRow->save();
            $data['status'] = 1;
        } else {
            Setting::create([
                'name' => 'candidate_global_view_count',
                'value' => $request->limit,
            ]);
            $data['status'] = 1;
        }
        return $data;
    }
}
