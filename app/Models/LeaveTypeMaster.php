<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveTypeMaster extends Model
{
    use SoftDeletes;

    protected $table = 'leave_type_master';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'leave_type',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Optional: Status constants for better maintainability
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_UNAPPROVED = 'UNAPPROVED';

    /**
     * Scope: Only approved leave types
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope: Only unapproved leave types
     */
    public function scopeUnapproved($query)
    {
        return $query->where('status', self::STATUS_UNAPPROVED);
    }
}
