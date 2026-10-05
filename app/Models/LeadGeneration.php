<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadGeneration extends Model
{
    use SoftDeletes;
    protected $table = 'lead_generations';

    protected $primaryKey = 'id';

    public $timestamps = false; // because you manage created_at/updated_at manually

    protected $fillable = [
        'username',
        'email',
        'gender',
        'marital_status',
        'phone_no_1',
        'phone_no_2',
        'phone_no_3',
        'country',
        'interest',
        'adminrole_id',
        'franchised_by',
        'staff_assign_id',
        'staff_assign_date',
        'franchise_assign_id',
        'franchise_assign_date',
        'validate_number',
        'is_registered',
        'member_matri_id',
        'commented',
        'followup_date',
        'followup_time',
        'lead_status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'staff_assign_date'     => 'date',
        'franchise_assign_date' => 'date',
        'created_at'            => 'datetime',
        'updated_at'            => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    public function scopeUnassigned($query)
    {
        return $query->whereNull('staff_assign_id');
    }

    public function scopeRegistered($query)
    {
        return $query->where('is_registered', 'Yes');
    }

    public function scopeNotRegistered($query)
    {
        return $query->where('is_registered', 'No');
    }

    /*
    |--------------------------------------------------------------------------
    | Mutators
    |--------------------------------------------------------------------------
    */

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower(trim($value));
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */


    public function assignStaff($staffId)
    {
        $this->update([
            'staff_assign_id'   => $staffId,
            'staff_assign_date' => now(),
        ]);
    }

    ## Relations:
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_assign_id');
    }

    public function franchise(): BelongsTo
    {
        return $this->belongsTo(Franchise::class, 'franchise_assign_id', 'id');
    }
}
