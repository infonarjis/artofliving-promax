<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AffiliateMemberAssign extends Model
{
    use SoftDeletes;

    protected $table = 'affiliate_member_assign';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'affiliate_member_id',
        'member_id',
        'is_verify',
        'verify_date',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_verify' => 'boolean',
        'verify_date' => 'datetime',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Affiliate who owns these members
    public function affiliate()
    {
        return $this->belongsTo(AffiliateMember::class, 'affiliate_member_id');
    }

    // The actual member assigned
    public function member()
    {
        return $this->belongsTo(Register::class, 'member_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeVerified($query)
    {
        return $query->where('is_verify', 1);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 1);
    }

    public function scopePending($query)
    {
        return $query->where('status', 0);
    }
}