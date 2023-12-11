<?php

namespace App\Models;

use App\Models\Visa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Requirement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id','job_id','job_title','no_of_position','experience','location','work_type','duration','visa','client','vendor_rate','my_rate',
    'priority','reason','term','category','moi','job_keyword','notes','description','pv_company_name','poc_name','poc_email','poc_phone_number',
    'poc_location','pv_company_location','client_name','display_client','status','recruiter','color','candidate','submissionCounter','is_show_recruiter',
    'is_update_requirement','is_show_recruiter_after_update','updated_fileds','parent_requirement_id',];

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_EXP_HOLD = 'exp_hold';
    const STATUS_EXP_NEED = 'exp_need';
    const STATUS_HOLD     = 'hold';
    const STATUS_UNHOLD   = 'unhold';

    public static $status = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'In Active',
    ];

    public static $exprieStatus = [
        self::STATUS_EXP_HOLD => 'Exp-Hold',
        self::STATUS_EXP_NEED => 'Exp-Need',
    ];

    public static $allStatus = [
        ''                    => 'Please Select',
        self::STATUS_HOLD     => 'Hold',
        self::STATUS_UNHOLD   => 'Need',
        self::STATUS_EXP_HOLD => 'Exp-Hold',
        self::STATUS_EXP_NEED => 'Exp-Need',
    ];
    const PRIORITY_0 = '';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_MEDIUM = 'medium';

    public static $priority = [
        self::PRIORITY_0 => 'Select Priority',
        self::PRIORITY_HIGH => 'High',
        self::PRIORITY_MEDIUM => 'Medium',
    ];

    const TERM0 = '';
    const TERM1 = 'c2c';
    const TERM2 = 'c2h';
    const TERM3 = 'w2';
    const TERM4 = 'fulltime';

    public static $term = [
        self::TERM0 => 'Select Term',
        self::TERM1 => 'C2C',
        self::TERM2 => 'C2H',
        self::TERM3 => 'W2',
        self::TERM4 => 'Full Time',
    ];

    const TYPE0 = '';
    const TYPE1 = 'Onsite';
    const TYPE2 = 'Hybrid';
    const TYPE3 = 'Remote';

    public static $workType = [
        self::TYPE0 => 'Please Select',
        self::TYPE1 => 'Onsite',
        self::TYPE2 => 'Hybrid',
        self::TYPE3 => 'Remote',
    ];

    public function BDM(){
        return $this->belongsTo('App\Models\Admin','user_id');
    }

    public function Category(){
        return $this->belongsTo('App\Models\Category','category');
    }

    public function PvCompany(){
        return $this->belongsTo('App\Models\PVCompany','pv_company_name');
    }

    public function MOI(){
        return $this->belongsTo('App\Models\Moi','moi');
    }

    public function Visa(){
        return $this->belongsTo('App\Models\Visa','visa');
    }

    public static function getVisaNames(string $ids){
        $ids = array_filter(explode(',',$ids));
        $visaNames = Visa::whereIn('id', $ids)->pluck('name')->toArray();
        return implode(', ',$visaNames);
    }

    public static function getMoiNames(string $ids){
        $ids = array_filter(explode(',',$ids));
        $moiNames = Moi::whereIn('id', $ids)->pluck('name')->toArray();
        return implode(', ',$moiNames);
    }
}
