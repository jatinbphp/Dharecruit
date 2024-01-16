<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class POCTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'pv_company_id',
        'transfer_by',
        'transfer_to',
        'transfer_type',
    ];

    const TRANSFER_TYPE_KEY = 1;
    const TRANSFER_TYPE_AUTOMATIC = 2;
    const TRANSFER_TYPE_SELF = 3;
    const TRANSFER_TYPE_KEY_TEXT = 'Key';
    const TRANSFER_TYPE_AUTOMATIC_TEXT = 'Automatic';
    const TRANSFER_TYPE_SELF_TEXT = 'Self';

    public static $transferKeyValuePair = [
        self::TRANSFER_TYPE_KEY => self::TRANSFER_TYPE_KEY_TEXT,
        self::TRANSFER_TYPE_AUTOMATIC => self::TRANSFER_TYPE_AUTOMATIC_TEXT,
        self::TRANSFER_TYPE_SELF => self::TRANSFER_TYPE_SELF_TEXT,
    ];
}
