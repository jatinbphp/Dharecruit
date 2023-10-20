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
        'education_details','resume_experience','linkedin_id','relocation','vendor_rate','notes','documents','common_skills','skills_match','reason','status','employer_name','employee_name','employee_email','employee_phone','pv_status','pv_reason'
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPT = 'accepted';
    //const STATUS_INTERVIEW = 'interview';
    const STATUS_REJECTED = 'rejected';

    public static $status = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_ACCEPT => 'Accepted',
        //self::STATUS_INTERVIEW => 'Interview',
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

    const PER0 = '';
    const PER10 = '10%';
    const PER20 = '20%';
    const PER30 = '30%';
    const PER40 = '40%';
    const PER50 = '50%';
    const PER60 = '60%';
    const PER70 = '70%';
    const PER80 = '80%';
    const PER90 = '90%';
    const PER100 = '100%';

    public static $percentage = [
        self::PER0 => 'Please Select',
        self::PER10 => '10%',
        self::PER20 => '20%',
        self::PER30 => '30%',
        self::PER40 => '40%',
        self::PER50 => '50%',
        self::PER60 => '60%',
        self::PER70 => '70%',
        self::PER80 => '80%',
        self::PER90 => '90%',
        self::PER100 => '100%',
    ];

    const STATUS_REJECTED_BY_PV          = 'rejected_by_pv';
    const STATUS_SUBMITTED_TO_END_CLIENT = 'submitted_to_end_client';
    const STATUS_NO_RESPONSE_FROM_PV     = 'no_response_from_pv';

    public static $pvStatus = [
        self::STATUS_REJECTED_BY_PV          => 'Rejected By PV',
        self::STATUS_SUBMITTED_TO_END_CLIENT => 'Submitted To End Client',
        self::STATUS_NO_RESPONSE_FROM_PV     => 'No Response From PV',
    ];

    public function Recruiters(){
        return $this->belongsTo('App\Models\Admin','user_id');
    }

    public function Requirement(){
        return $this->belongsTo('App\Models\Requirement','requirement_id');
    }
}
