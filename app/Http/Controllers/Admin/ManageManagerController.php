<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Team;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class ManageManagerController extends Controller
{
    public function index()
    {
        $data['menu'] = 'Manage Manager';
        $data['teamBdmData']  = Team::where('team_type', Team::TEAM_TYPE_BDM)->get();
        $data['teamRecData']  = Team::where('team_type', Team::TEAM_TYPE_RECRUITER)->get();
        $data['allBdmData']   = Admin::getActiveBDM(true);
        $data['allRecData']   = Admin::getActiveRecruiter(true);
        $data['teamWiseData'] = TeamMember::select('team_id', 'member_id')
            ->get()
            ->groupBy('team_id')
            ->map(function ($item) {
                return $item->pluck('member_id')->toArray();
            })
            ->toArray();

        return view('admin.manager.index', $data);
    }

    public function updateTeamManager(Request $request)
    {
        $data['status'] = 0;
        if(empty($request->user_id) || empty($request->team_id)){
            return $data;
        }

        $team = Team::find($request->team_id);

        if($team->id){
            $team->manager_id = $request->user_id;
            $team->save();
            $data['status'] = 1;
        }
        return $data;
    }
}
