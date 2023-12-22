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
    ];
}
