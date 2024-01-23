<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignToRecruiter extends Model
{
    use HasFactory;

    protected $fillable = [
        'requirement_id',
        'recruiter_id',
        'filling_confident',
        'is_profile_pending',
    ];

    const FILLING_CONFIDENT_WILL_TRY = 1;
    const FILLING_CONFIDENT_YES = 2;
}
