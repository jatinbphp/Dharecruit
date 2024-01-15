<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class POCTransfer extends Model
{
    use HasFactory;

    protected $_fillable = [
        'pv_company_id',
        'transfer_by',
        'transfer_to',
        'transfer_type',
    ];
}
