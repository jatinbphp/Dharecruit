<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterviewDocuments extends Model
{
    use HasFactory;
    protected $fillable = [
        'interview_id',
        'document',
    ];
}
