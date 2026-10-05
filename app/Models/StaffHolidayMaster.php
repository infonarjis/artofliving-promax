<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffHolidayMaster extends Model
{
    use SoftDeletes;

    protected $table = 'staff_holiday_masters';

    protected $fillable = [
        'title',
        'description',
        'holiday_date',
        'status',
        'lang_code',
        'lang_id',
    ];

    protected $dates = [
        'deleted_at',
    ];
}