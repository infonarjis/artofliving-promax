<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class CouponCode extends Model
{
    use SoftDeletes;
    protected $table = 'coupon_code';

    protected $fillable = [
        'status',
        'plan_id',
        'coupon_code',
        'discount_amount',
        'active_from',
        'expired_on'
    ];

    protected $casts = [
        'discount_amount' => 'float',
        // 'active_from'     => 'date',
        // 'expired_on'      => 'date',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Scopes (CRITICAL for this table)
    |--------------------------------------------------------------------------
    */

    // Not deleted
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'APPROVED');
    }

    // Currently valid by date
    public function scopeValidDate(Builder $query): Builder
    {
        $today = Carbon::today()->toDateString();

        return $query->whereDate('active_from', '<=', $today)
                     ->whereDate('expired_on', '>=', $today);
    }

    // Find by code
    public function scopeByCode(Builder $query, string $code): Builder
    {
        return $query->where('coupon_code', $code);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getIsValidAttribute(): bool
    {
        $today = Carbon::today();

        return $this->status === 'APPROVED'
            && $this->active_from <= $today
            && $this->expired_on >= $today;
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    // If plan_id refers to plans table
    public function plan()
    {
        return $this->belongsTo(MembershipPlan::class, 'plan_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'coupon_id');
    }

    public function totalUsedCount(): int
    {
        return $this->payments()->count();
    }

    public function userUsedCount($memberId): int
    {
        return $this->payments()->where('member_id', $memberId)->count();
    }
}