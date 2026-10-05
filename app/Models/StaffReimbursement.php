<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffReimbursement extends Model
{
    use SoftDeletes;

    protected $table = 'staff_reimbursements';

    protected $fillable = [
        'staff_id',
        'title',
        'description',
        'receipt',
        'status',
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