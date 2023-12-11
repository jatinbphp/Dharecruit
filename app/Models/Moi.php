<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Moi extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id','name','status'];

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static $status = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'In Active',
    ];

    public static function getActiveMoies(){
        return Moi::where('status','active')->pluck('name','id')->prepend('Please Select',''); 
    }
}
