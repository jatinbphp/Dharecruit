<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntityHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'requirement_id',
        'entity_type',
        'entity_value',
    ];

    const ENTITY_TYPE_BDM_STATUS = 'bdm_status';
    const ENTITY_TYPE_PV_STATUS  = 'pv_status';
}
