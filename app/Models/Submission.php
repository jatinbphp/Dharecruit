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
        'education_details','resume_experience','linkedin_id','relocation','vendor_rate','notes','documents','common_skills','skills_match','reason','status','employer_name','employee_name','employee_email','employee_phone','pv_status','pv_reason','is_show',
        'candidate_id','log_data','bdm_status_updated_at','pv_status_updated_at','interview_status_updated_at'
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPT = 'accepted';
    //const STATUS_INTERVIEW = 'interview';
    const STATUS_REJECTED = 'rejected';
    const STATUS_NOT_VIEWED = 'no_viewed';
    const STATUS_NO_UPDATES = 'no_updates';

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
    const STATUS_REJECTED_BY_END_CLIENT  = 'rejected_by_end_client';
    const STATUS_NO_RESPONSE_FROM_PV     = 'no_response_from_pv';
    const STATUS_POSITION_CLOSED         = 'position_closed';

    const STATUS_REJECTED_BY_PV_TEXT          = 'Rejected By PV';
    const STATUS_SUBMITTED_TO_END_CLIENT_TEXT = 'Submitted To End Client';
    const STATUS_REJECTED_BY_END_CLIENT_TEXT  = 'Rejected By End Client';
    const STATUS_NO_RESPONSE_FROM_PV_TEXT     = 'No Response From PV';
    const STATUS_POSITION_CLOSED_TEXT         = 'Position Closed';


    public static $pvStatus = [
        self::STATUS_REJECTED_BY_PV          => self::STATUS_REJECTED_BY_PV_TEXT,
        self::STATUS_SUBMITTED_TO_END_CLIENT => self::STATUS_SUBMITTED_TO_END_CLIENT_TEXT,
        self::STATUS_REJECTED_BY_END_CLIENT  => self::STATUS_REJECTED_BY_END_CLIENT_TEXT,
        self::STATUS_NO_RESPONSE_FROM_PV     => self::STATUS_NO_RESPONSE_FROM_PV_TEXT,
        self::STATUS_POSITION_CLOSED         => self::STATUS_POSITION_CLOSED_TEXT,
    ];

    public function Recruiters(){
        return $this->belongsTo('App\Models\Admin','user_id');
    }

    public function Requirement(){
        return $this->belongsTo('App\Models\Requirement','requirement_id');
    }

    public static $toggleOptions = [
        'pv_name' => 'Show PV',
        'client' => 'Show Client',
        'poc_name' => 'Show Poc',
        'show_employer_name' => 'Show Employer',
        'emp_poc' => 'Show Emp POC',
    ];

    public static $hideForBDA = [
        'emp_poc',
    ];

    public static $hideForReq = [
        'poc_name',
        'pv_name',
    ];

    public static $manageLogFileds = [
        // 'email',
        'phone',
        'work_authorization',
        'education_details',
        'linkedin_id',
        'location',
        'last_4_ssn',
        'resume_experience',
        'employer_detail',
        'relocation',
        // 'employer_name',
    ];

    const STATUS_SERVED                   = 'served';
    const STATUS_UNSERVED                 = 'un_served';
    const STATUS_ALLOCATED                = 'allocated';
    const STATUS_NOT_ALLOCATED            = 'not_allocated';
    const STATUS_ALLOCATED_BUT_NOT_SERVED = 'allocated_but_not_served';

    const STATUS_SERVED_BY_ME         = 'served_by_me';
    const STATUS_ALLOCATED_BY_ME      = 'allocated_by_me';
    const STATUS_ALLOCATED_BY_ME_BUT_NOT_SERVED_BY_ME = 'allocated_by_me_but_not_served_by_me';
    const STATUS_ALLOCATED_BY_ME_BUT_NOT_SERVED_BY_ANYONE = 'allocated_by_me_but_not_served_by_anyone';


    const STATUS_SERVED_TEXT                   = 'Served';
    const STATUS_UNSERVED_TEXT                 = 'Un Served';
    const STATUS_ALLOCATED_TEXT                = 'Allocated';
    const STATUS_NOT_ALLOCATED_TEXT            = 'Not Allocated';
    const STATUS_ALLOCATED_BUT_NOT_SERVED_TEXT = 'Allocated But NOT Served';

    const STATUS_SERVED_BY_ME_TEXT         = 'Served By Me';
    const STATUS_ALLOCATED_BY_ME_TEXT      = 'Allocated By Me';
    const STATUS_ALLOCATED_BY_ME_BUT_NOT_SERVED_BY_ME_TEXT = 'Allocated By Me But Not Served By Me';
    const STATUS_ALLOCATED_BY_ME_BUT_NOT_SERVED_BY_ANYONE_TEXT = 'Allocated By Me But Not Served By Anyone';


    public static function getServedOptions() {
        $servedOptions[''] = 'Please Select';
        $userRole = \Auth::user()->role;
        if($userRole == 'recruiter'){
            $servedOptions[self::STATUS_SERVED_BY_ME] = self::STATUS_SERVED_BY_ME_TEXT;
            $servedOptions[self::STATUS_ALLOCATED_BY_ME] = self::STATUS_ALLOCATED_BY_ME_TEXT;
            $servedOptions[self::STATUS_ALLOCATED_BY_ME_BUT_NOT_SERVED_BY_ME] = self::STATUS_ALLOCATED_BY_ME_BUT_NOT_SERVED_BY_ME_TEXT;
            $servedOptions[self::STATUS_ALLOCATED_BY_ME_BUT_NOT_SERVED_BY_ANYONE] = self::STATUS_ALLOCATED_BY_ME_BUT_NOT_SERVED_BY_ANYONE_TEXT;
        }

        if(in_array($userRole, ['admin', 'bdm'])){
            $servedOptions[self::STATUS_SERVED] = self::STATUS_SERVED_TEXT;
            $servedOptions[self::STATUS_UNSERVED] = self::STATUS_UNSERVED_TEXT;
            $servedOptions[self::STATUS_ALLOCATED] = self::STATUS_ALLOCATED_TEXT;
            $servedOptions[self::STATUS_NOT_ALLOCATED] = self::STATUS_NOT_ALLOCATED_TEXT;
            $servedOptions[self::STATUS_ALLOCATED_BUT_NOT_SERVED] = self::STATUS_ALLOCATED_BUT_NOT_SERVED_TEXT;
        }

        return $servedOptions;
    }

    public static function getBDMFilterOptions() {
        return [
            self::STATUS_ACCEPT   => 'Accepted',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_NOT_VIEWED => 'Not Viewed',
            self:: STATUS_NO_UPDATES => 'No updates',
        ];
    }
}
