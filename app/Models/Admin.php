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
        'status'
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
}