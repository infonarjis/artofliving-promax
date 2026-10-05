<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffKpiAssignMember extends Model
{
    use SoftDeletes;

    protected $table = 'staff_kpi_assign_members';

    protected $fillable = [
        'staff_id',
        'member_id',
        'matri_id',
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
