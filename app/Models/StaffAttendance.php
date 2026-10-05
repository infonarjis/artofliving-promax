<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffAttendance extends Model
{
    use SoftDeletes;

    protected $table = 'staff_attendance';

    protected $fillable = [
        'staff_id',
        'punch_in',
        'punch_in_remarks',
        'punch_out',
        'punch_out_remarks',
        'attendance_status',
        'ip_address',
    ];

    protected $casts = [
        'punch_in'  => 'datetime',
        'punch_out' => 'datetime',
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