<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Team;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ManageTeamController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('accessright:manage_team');
    }

    public function index()
    {
        return view('admin.team.index', $this->getAllListData());
    }

    public function create()
    {
        $data['menu'] = "Manage Team";
        return view("admin.team.create",$data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'team_type' => 'required',
            'team_name' => 'required',
            'team_lead' => 'required',
            'team_color' => 'required',
            'team_members' => 'required',
        ]);

        $teamName = Team::where('team_name', $request->team_name)->first();

        if(!empty($teamName)){
            \Session::flash('danger', "$teamName->team_name Already Exists.");
            return redirect()->back();
        }

        $teamData = [
            'team_lead_id' => $request->team_lead,
            'team_name'    => $request->team_name,
            'team_type'    => $request->team_type,
            'team_color'   => $request->team_color,
        ];

        $team = Team::create($teamData);

        if($team->id && $request->team_members) {
            foreach ($request->team_members as $member){
                $teamMember = [
                    'team_id' => $team->id,
                    'member_id' => $member
                ];
                TeamMember::create($teamMember);
            }
        }

        \Session::flash('success', 'Category has been inserted successfully!');
        return redirect()->route('manage_team.index');

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

    public function fetchUsers(Request $request)
    {
        if(!empty($request->team_type)){
            if($request->team_type == 'team_type_recruiter'){
                return Admin::where('role', 'recruiter')->whereNotIn('id', $this->getAllAssignedUsers())->where('status', 'active')->pluck('name', 'id')->toArray();
            } elseif($request->team_type == 'team_type_bdm'){
                return Admin::where('role', 'bdm')->whereNotIn('id', $this->getAllAssignedUsers())->where('status', 'active')->pluck('name', 'id')->toArray();
            }
        }
        return [];
    }

    public function fetchTeamMember(Request $request)
    {
        if(!empty($request->team_lead) && !empty($request->team_type)){
            if($request->team_type == 'team_type_recruiter'){
                return Admin::where('role', 'recruiter')->where('id', '!=', $request->team_lead)->whereNotIn('id', $this->getAllAssignedUsers())->where('status', 'active')->pluck('name', 'id')->toArray();
            } elseif($request->team_type == 'team_type_bdm'){
                return Admin::where('role', 'bdm')->where('id', '!=', $request->team_lead)->whereNotIn('id', $this->getAllAssignedUsers())->where('status', 'active')->pluck('name', 'id')->toArray();
            }
        }
        return [];
    }

    public function getAllAssignedUsers()
    {
        $allLeaders = Team::pluck('team_lead_id')->toArray();
        $allTeamMembers = TeamMember::pluck('member_id')->toArray();
        return array_merge($allLeaders, $allTeamMembers);
    }

    public function checkTeamName($teamName) {
        $existingTeam = Team::where('team_name', 'like' , '%'.$teamName.'%')->exists();

        if ($existingTeam) {
            return 0;
        } else {
            return 1;
        }
    }

    public function updateUserTeam(Request $request)
    {
        $data['status'] = 0;
        if(empty($request->user_id) || empty($request->team_id) || empty($request->type)){
            $data['html'] = view('admin.team.teamData', $this->getAllListData())->toHtml();
            return $data;
        }

        $isInTeam = TeamMember::where('member_id', $request->user_id)->exists();
        if($isInTeam && $request->type == 'update'){
            $data['html'] = view('admin.team.teamData', $this->getAllListData())->toHtml();
            return $data;
        }

        if($request->type == 'update'){
            TeamMember::create([
                'team_id' => $request->team_id,
                'member_id' => $request->user_id,
            ]);
            $data['status'] = 1;
        } else {
            TeamMember::where('member_id', $request->user_id)->where('team_id', $request->team_id)->delete();
            $data['status'] = 1;
        }
        $data['html'] = view('admin.team.teamData', $this->getAllListData())->toHtml();
        return $data;
    }

    public function updateTeamName(Request $request)
    {
        $data['status'] = 0;
        if(empty($request->team_id) || empty($request->team_name)){
            $data['html'] = view('admin.team.teamData', $this->getAllListData())->toHtml();
            return $data;
        }

        $existingTeam = Team::where('team_name', $request->team_name)->exists();
        if($existingTeam){
            $data['status'] = 2;
            return $data;
        }

        $team = Team::find($request->team_id);

        if($team->id){
            $team->team_name = $request->team_name;
            $team->save();
            $data['status'] = 1;
        }

        $data['html'] = view('admin.team.teamData', $this->getAllListData())->toHtml();
        return $data;
    }

    public function getAllListData()
    {
        $data['menu']         = "Manage Team";
        $data['teamBdmData']  = Team::where('team_type', Team::TEAM_TYPE_BDM)->get();
        $data['teamRecData']  = Team::where('team_type', Team::TEAM_TYPE_RECRUITER)->get();
        $data['allLeadData']  = Team::pluck('team_lead_id')->toArray();
        $data['allBdmUsers']  = Admin::getActiveBDM();
        $data['allRecUsers']  = Admin::getActiveRecruiter();
        $data['teamWiseData'] = TeamMember::select('team_id', 'member_id')
            ->get()
            ->groupBy('team_id')
            ->map(function ($item) {
                return $item->pluck('member_id')->toArray();
            })
            ->toArray();

        return $data;
    }

    public function updateTeamLeadName(Request $request)
    {
        $data['status'] = 0;

        if( empty($request->team_type) || empty($request->team_lead_id)){
            return $data;
        }

        $userData = [];

        if($request->team_type == 'team_type_recruiter'){
            $userData = Admin::where('role', 'recruiter')->where('id', '!=', $request->team_lead)->whereNotIn('id', $this->getAllAssignedUsers())->where('status', 'active')->pluck('name', 'id')->toArray();
        } elseif($request->team_type == 'team_type_bdm'){
            $userData = Admin::where('role', 'bdm')->where('id', '!=', $request->team_lead)->whereNotIn('id', $this->getAllAssignedUsers())->where('status', 'active')->pluck('name', 'id')->toArray();
        }

        $data['status'] = 1;
        $data['user_data'] = $userData;
        return $data;
    }

    public function updateTeamLead(Request $request)
    {
        $data['status'] = 0;
        if(empty($request->team_lead_id) || empty($request->team_id)){
            $data['html'] = view('admin.team.teamData', $this->getAllListData())->toHtml();
            return $data;
        }

        $team = Team::find($request->team_id);

        if($team->id){
            $team->team_lead_id = $request->team_lead_id;
            $team->save();
            $data['status'] = 1;
        }
        $data['html'] = view('admin.team.teamData', $this->getAllListData())->toHtml();
        return $data;
    }
}
