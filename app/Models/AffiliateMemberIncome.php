<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AffiliateMemberIncome extends Model
{
    use SoftDeletes;
    
    protected $table = 'affiliate_member_income';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'affiliate_member_id',
        'member_id',
        'amount',
        'is_transfered',
        'income_type',
        'status'
    ];

    protected $casts = [
        'amount' => 'double',
        'is_transfered' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ===================== RELATIONS ===================== */

    // Affiliate who earned the income
    public function affiliate()
    {
        return $this->belongsTo(Register::class, 'affiliate_member_id', 'id');
    }

    // Member from whom income generated
    public function member()
    {
        return $this->belongsTo(Register::class, 'member_id', 'id');
    }

    /* ===================== SCOPES ===================== */

    public function scopeApproved($query)
    {
        return $query->where('status', 'APPROVED');
    }

    public function scopeNotTransferred($query)
    {
        return $query->where('is_transfered', 0);
    }

    public function scopeTransferred($query)
    {
        return $query->where('is_transfered', 1);
    }
}