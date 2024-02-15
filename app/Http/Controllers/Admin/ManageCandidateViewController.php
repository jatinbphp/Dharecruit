<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;

class ManageCandidateViewController extends Controller
{
    public function index()
    {
        $data['menu'] = "Manage Candidate View";
        $data['allRecruiter'] = Admin::where('status', 'active')->where('role', 'recruiter')->pluck('name', 'id')->toArray();
        $data['userWiseTeam'] = getUserIdWiseTeamName();
        $data['userWiseLimit'] = Admin::where('status', 'active')->where('role', 'recruiter')->pluck('view_limit', 'id')->toArray();

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
}
