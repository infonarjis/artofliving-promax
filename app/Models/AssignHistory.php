<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignHistory extends Model
{

    // Table name (optional if follows Laravel convention)
    protected $table = 'assign_history';

    // Primary key (optional if 'id')
    protected $primaryKey = 'id';

    // Auto-incrementing
    public $incrementing = true;

    // Timestamps
    public $timestamps = false; // since your table doesn't have created_at / updated_at

    // Mass assignable fields
    protected $fillable = [
        'assign_by',
        'assign_by_email',
        'assign_to',
        'user_type',
        'member_id',
        'lead_generation_id',
        'assign_date',
        'action'
    ];

    // Casts for proper data types
    protected $casts = [
        'assign_date' => 'datetime',
        'id' => 'integer',
    ];
    
    public function scopeStaff($query)
    {
        return $query->where('user_type', 'Staff');
    }

    public function scopeFranchise($query)
    {
        return $query->where('user_type', 'Franchise');
    }

    ## Relations:
    public function register()
    {
        return $this->belongsTo(Register::class, 'member_id');
    }

    public function leadGeneration()
    {
        return $this->belongsTo(LeadGeneration::class, 'lead_generation_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'assign_to');
    }
    ## Relations:
    public function franchise(): BelongsTo
    {
        return $this->belongsTo(Franchise::class, 'assign_to');
    }
}