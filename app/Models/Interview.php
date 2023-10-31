<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'hiring_manager',
        'client',
        'interview_date',
        'interview_time',
        'candidate_phone_number',
        'candidate_email',
        'time_zone',
        'status',
        'recruiter_name',
        'job_id',
        'submission_id',
    ];

    const STATUS_SCHEDULED               = 'scheduled';
    const STATUS_RE_SCHEDULED            = 're_scheduled';
    const STATUS_SELECTED_FOR_NEXT_ROUND = 'selected_for_next_round';
    const STATUS_CONFIRMED_POSITION      = 'confirmed_position';
    const STATUS_BACKOUT                 = 'backout';
    const STATUS_REJECTED                = 'rejected';

    const STATUS_SCHEDULED_TEXT               = 'Scheduled';
    const STATUS_RE_SCHEDULED_TEXT            = 'Re Scheduled';
    const STATUS_SELECTED_FOR_NEXT_ROUND_TEXT = 'Selected For Next Round';
    const STATUS_CONFIRMED_POSITION_TEXT      = 'Confirmed Position';
    const STATUS_BACKOUT_TEXT                 = 'Backout';
    const STATUS_REJECTED_TEXT                = 'Rejected';

    public static $interviewStatusOptions = [
        ''                                   => 'Select Interview Status',
        self::STATUS_SCHEDULED               => self::STATUS_SCHEDULED_TEXT,
        self::STATUS_RE_SCHEDULED            => self::STATUS_RE_SCHEDULED_TEXT,
        self::STATUS_SELECTED_FOR_NEXT_ROUND => self::STATUS_SELECTED_FOR_NEXT_ROUND_TEXT,
        self::STATUS_CONFIRMED_POSITION      => self::STATUS_CONFIRMED_POSITION_TEXT,
        self::STATUS_BACKOUT                 => self::STATUS_BACKOUT_TEXT,
        self::STATUS_REJECTED                => self::STATUS_REJECTED_TEXT,
    ];

    public static $toggleOptions = [
        'poc_name' => 'Show Poc',
        'client' => 'Show Client',
        'employer_name' => 'Show Employer',
        'candidate_phone' => 'Show Candidate Phone',
        'candidate_email' => 'Show Candidate Email',
        'hiring_manager' => 'Show Hiring Manager',
        'pv_name' => 'Show PV',
        'emp_poc' => 'Show Emp POC',
    ];

    public static $hideForBDA = [
        'emp_poc',
    ];

    public static $hideForReq = [
        'poc_name',
        'pv_name',
    ];

    public function Submission(){
        return $this->belongsTo('App\Models\Submission','submission_id');
    }
}
