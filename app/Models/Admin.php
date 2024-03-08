<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Admin extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $guard = "admin";

    protected $fillable = [
        'name',
        'email',
        'phone',
        'indian_phone',
        'password',
        'role',
        'status',
        'employee_name',
        'added_by',
        'is_allow_transfer_key',
        'transfer_key',
        'color',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function setPasswordAttribute($password){
        $this->attributes['password'] = bcrypt($password);
    }

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static $status = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'In Active',
    ];

    public static function getUserNameBasedOnId($userId){
        $user = Admin::where('id',$userId)->first();
        return ($user && $user->name) ? $user->name : '';
    }

    public static function getUserIdWiseName(){
        return Admin::pluck('name', 'id')->toArray();
    }

    public static function getActiveBDM($defaltOption = false){
        $userData = [];

        if($defaltOption){
            $userData[''] = 'Please Select';
        }

        $admins = Admin::leftJoin('team_members', 'admins.id', '=', 'team_members.member_id')
            ->leftJoin('teams', 'team_members.team_id', '=', 'teams.id')
            ->select('admins.id', 'admins.name as admin_name', 'teams.team_name as team_name')
            ->where('role','bdm')
            ->where('status','active')
            ->orderBy('name')
            ->get();

        foreach ($admins as $admin) {
            if ($admin->team_name) {
                $userData[$admin->id] = $admin->admin_name .' ('. $admin->team_name.') ';
            } else {
                $userData[$admin->id] = $admin->admin_name;
            }
        }

        return $userData;
    }

    public static function getActiveRecruiter($defaltOption = false){
        $userData = [];

        if($defaltOption){
            $userData[''] = 'Please Select';
        }

        $admins = Admin::leftJoin('team_members', 'admins.id', '=', 'team_members.member_id')
            ->leftJoin('teams', 'team_members.team_id', '=', 'teams.id')
            ->select('admins.id', 'admins.name as admin_name', 'teams.team_name as team_name')
            ->where('role','recruiter')
            ->where('status','active')
            ->orderBy('name')
            ->get();

        foreach ($admins as $admin) {
            if ($admin->team_name) {
                $userData[$admin->id] = $admin->admin_name .' ('. $admin->team_name.') ';
            } else {
                $userData[$admin->id] = $admin->admin_name;
            }
        }

        return $userData;
    }

    public static function getActiveEmployers(){
        return Admin::where('status','active')->where('role', 'employee')->orderBy('name')->distinct()->pluck('name','name');
    }

    public static function getActiveEmployees(){
        return Admin::where('status','active')->where('role', 'employee')->orderBy('employee_name')->distinct()->pluck('employee_name','employee_name');
    }

    public static function getUserNameWiseColor()
    {
        return Admin::whereNotNull('color')->pluck('color', 'name')->toArray();
    }
}
