<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MembershipPlan extends Model
{
    use SoftDeletes;
    protected $table = 'membership_plans';

    protected $fillable = [
        'plan_name',
        'plan_type',
        'plan_amount',
        'international_currency_code',
        'international_plan_amount',
        'plan_discount',
        'plan_description',
        'currency_code',
        'validity_days',
        'interests_limit',
        'view_profile_limit',
        'contact_views_limit',
        'video_minutes_limit',
        'audio_minutes_limit',
        'can_chat',
        'is_personalized',

        'in_app_purchase_android_id',
        'in_app_purchase_android_amount',
        'international_in_app_purchase_android_amount',
        'in_app_purchase_ios_id',
        'in_app_purchase_ios_amount',
        'international_in_app_purchase_ios_amount',

        'status'
    ];

    protected $casts = [
        'plan_amount'         => 'float',
        'plan_discount'       => 'float',
        // 'can_chat'            => 'boolean',
        // 'is_personalized'     => 'boolean',
        'validity_days'       => 'integer',
        'interests_limit'     => 'integer',
        'view_profile_limit' => 'integer',
        'video_minutes_limit' => 'integer',
        'audio_minutes_limit' => 'integer',
        'created_at'          => 'datetime',
        'updated_at'          => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', 'APPROVED');
    }

    public function scopePaid($query)
    {
        return $query->where('plan_type', 'PAID');
    }

    public function scopeFree($query)
    {
        return $query->where('plan_type', 'FREE');
    }

    public function scopePersonalized($query)
    {
        return $query->where('is_personalized', 1);
    }

    public function scopeStandard($query)
    {
        return $query->where('is_personalized', 0);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function payments(): HasMany
    {
        return $this->hasMany(MembershipPayment::class, 'plan_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods (very useful in your matrimony logic)
    |--------------------------------------------------------------------------
    */

    public function finalAmount(): float
    {
        return max($this->plan_amount - $this->plan_discount, 0);
    }

    public function hasChat(): bool
    {
        return $this->can_chat;
    }

    public function hasInterestLimit(): bool
    {
        return $this->interests_limit > 0;
    }

    public function hasContactViewLimit(): bool
    {
        return $this->contact_views_limit > 0;
    }
    
    public function hasProfileViewLimit(): bool
    {
        return $this->view_profile_limit > 0;
    }

    public function hasVideoMinutes(): bool
    {
        return $this->video_minutes_limit > 0;
    }

    public function hasAudioMinutes(): bool
    {
        return $this->audio_minutes_limit > 0;
    }
}