<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PVCompany extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id','name','status','email','phone','poc_name','poc_location','pv_company_location','client_name', 'is_transfer', 'assigned_user_id'];

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static $status = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'In Active',
    ];

    public static function getActivePVCompanyies(){
        return PVCompany::where('status','active')->orderBy('name')->distinct()->pluck('name','name');
    }

    public static function getActivePOCNames(){
        return PVCompany::where('status','active')->orderBy('poc_name')->distinct()->pluck('poc_name','poc_name');
    }

    public function pocTransfers()
    {
        return $this->hasMany(\App\Models\POCTransfer::class, 'pv_company_id')->latest();
    }
}
