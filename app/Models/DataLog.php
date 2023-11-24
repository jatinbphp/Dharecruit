<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'section',
        'section_id',
        'user_id',
        'candidate_id',
        'job_id',
        'data',
    ];

    const SECTION_SUBMISSION = 'submission';

    public function userDetail(){
        return $this->belongsTo('App\Models\Admin','user_id');
    }
}
