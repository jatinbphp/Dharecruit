<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    CONST TEAM_TYPE_BDM = 'team_type_bdm';
    CONST TEAM_TYPE_RECRUITER = 'team_type_recruiter';
    CONST TEAM_TYPE_BDM_TEXT = 'BDM Team';
    CONST TEAM_TYPE_RECRUITER_TEXT = 'Recruiter Team';

    protected $fillable = [
        'team_lead_id',
        'team_name',
        'team_type',
        'team_color',
        'manager_id',
    ];

    public static function getTeamType()
    {
        return [
            ''                        => 'Please Select Team',
            self::TEAM_TYPE_BDM       => self::TEAM_TYPE_BDM_TEXT,
            self::TEAM_TYPE_RECRUITER => self::TEAM_TYPE_RECRUITER_TEXT,
        ];
    }
    public function teamMembers()
    {
        return $this->hasMany(TeamMember::class)->has('membersData');
    }

    public function TeanLead(){
        return $this->belongsTo('App\Models\Admin','team_lead_id');
    }
}
