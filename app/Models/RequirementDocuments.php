<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequirementDocuments extends Model
{
    use HasFactory;

    protected $fillable = ['requirement_id','document'];
}
