<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Submission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id','requirement_id','name','email','location','phone','employer_detail','work_authorization','recruiter_rate','last_4_ssn',
        'education_details','resume_experience','linkedin_id','relocation','vendor_rate','notes','documents','status'
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_INTERVIEW = 'interview';
    const STATUS_REJECTED = 'rejected';

    public static $status = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_INTERVIEW => 'Interview',
        self::STATUS_REJECTED => 'Rejected',
    ];

    const DETAILS0 = '';
    const DETAILS1 = 'W2 with benefits';
    const DETAILS2 = 'W2 without benefits';
    const DETAILS3 = '1099 with benefits';
    const DETAILS4 = '1099 without benefits';
    const DETAILS5 = 'C2C';

    public static $empDetails = [
        self::DETAILS0 => 'Please Select',
        self::DETAILS1 => 'W2 with benefits',
        self::DETAILS2 => 'W2 without benefits',
        self::DETAILS3 => '1099 with benefits',
        self::DETAILS4 => '1099 without benefits',
        self::DETAILS5 => 'C2C'
    ];

    public function Recruiters(){
        return $this->belongsTo('App\Models\Admin','user_id');
    }
}
