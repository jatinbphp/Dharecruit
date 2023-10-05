<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'type','access_modules'
    ];

    const MODULE_PERMISSION = 'manage_permission';
    const MODULE_USER = 'manage_user';
    const MODULE_BDM_USER = 'manage_bdm_user';
    const MODULE_RECRUITER_USER = 'manage_recruiter_user';
    const MODULE_TL_RECRUITER_USER = 'manage_tl_recruiter_user';
    const MODULE_TL_BDM_USER = 'manage_tl_bdm_user';
    const MODULE_CATEGORY = 'manage_category';
    const MODULE_MOI = 'manage_moi';
    const MODULE_REQUIREMENTS = 'manage_requirement';
    const MODULE_PVCOMPANY = 'manage_pvcompany';

    public static $permission = [
        self::MODULE_PERMISSION => 'Manage Permission',
        self::MODULE_USER => 'Manage Users',
        self::MODULE_BDM_USER => 'Manage BDM Users',
        self::MODULE_RECRUITER_USER => 'Manage Recruiter Users',
        self::MODULE_TL_RECRUITER_USER => 'Manage TL Recruiter Users',
        self::MODULE_TL_BDM_USER => 'Manage TL BDM Users',
        self::MODULE_CATEGORY => 'Manage Category',
        self::MODULE_MOI => 'Manage MOI',
        self::MODULE_REQUIREMENTS => 'Manage Requirement',
        self::MODULE_PVCOMPANY => 'Manage PV Company',
    ];
}
