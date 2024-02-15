<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory;
    protected $fillable = [
        'team_id',
        'member_id',
    ];

    public function membersData(){
        return $this->belongsTo('App\Models\Admin','member_id');
    }
    public static function getTeamUsers(){
        $team = Team::with('teamMembers')
            ->where('team_lead_id', getLoggedInUserId())
            ->first();

        $members = [];
        foreach ($team->teamMembers as $member) {
            $members[$member->member_id] = $member->membersData->name;
        }

        return $members;
    }
}
