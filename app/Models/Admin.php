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
        'added_by'
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

    public static function getActiveBDM($defaltOption = false){
        if($defaltOption){
            return Admin::where('role','bdm')->where('status','active')->pluck('name','id')->prepend('Please Select','');
        }
        return Admin::where('role','bdm')->where('status','active')->pluck('name','id');
    }

    public static function getActiveRecruiter($defaltOption = false){
        if($defaltOption){
            return Admin::where('role','recruiter')->where('status','active')->pluck('name','id')->prepend('Please Select','');
        }
        return Admin::where('role','recruiter')->where('status','active')->pluck('name','id');
    }
}
