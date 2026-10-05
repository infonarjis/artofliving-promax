<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffLeave extends Model
{
    use SoftDeletes;

    protected $table = 'staff_leaves';

    protected $fillable = [
        'staff_id',
        'leave_type',
        'subject',
        'message',
        'total_leave',
        'leave_start_date',
        'leave_end_date',
        'leave_reply_msg',
        'status',
        'lang_code',
        'lang_id',
    ];

    protected $dates = [
        'deleted_at',
    ];

    /**
     * Staff relation
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}