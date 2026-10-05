<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffAssignApprovedMember extends Model
{
    use SoftDeletes;

    protected $table = 'staff_assign_approved_members';

    protected $fillable = [
        'staff_id',
        'matri_id',
        'member_id',
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
