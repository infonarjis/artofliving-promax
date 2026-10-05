<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffPayHeadMaster extends Model
{
    use SoftDeletes;

    protected $table = 'staff_pay_head_masters';

    protected $fillable = [
        'title',
        'description',
        'pay_head_type',
        'lang_code',
        'lang_id',
        'status',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
