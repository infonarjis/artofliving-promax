<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;
    protected $table = 'payments';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true;

    // Mass assignable attributes
    protected $fillable = [
        'member_id',
        'plan_id',
        'previous_payment_id',
        'plan_name',
        'plan_type',
        'plan_discount',
        'plan_activate_date',
        'plan_expiry_date',
        'plan_validity_days',
        'addon_validity_days',
        'carried_forward_days',
        'total_validity_days',
        'plan_interest',
        'addon_interest',
        'carried_forward_interest',
        'interests_total',
        'interests_used',
        'plan_view_profile',
        'addon_view_profile',
        'carried_forward_view_profile',
        'view_profile_total',
        'view_profile_used',
        'plan_contact_views',
        'addon_contact_views',
        'carried_forward_contact_views',
        'contact_views_total',
        'contact_views_used',
        'plan_video_minutes',
        'addon_video_minutes',
        'carried_forward_video_minutes',
        'video_minutes_total',
        'video_minutes_used',
        'plan_audio_minutes',
        'addon_audio_minutes',
        'carried_forward_audio_minutes',
        'audio_minutes_total',
        'audio_minutes_used',
        'can_chat',
        'payment_mode',
        'transaction_id',
        'plan_amount',
        'currency_code',
        'tax_name',
        'tax_percentage',
        'tax_amount',
        'discount_detail',
        'coupon_id',
        'discount_amount',
        'grand_total',
        'franchise_id',
        'franchise_comm_per',
        'franchise_comm_amt',
        'current_plan',
        'is_renewal',
        'plan_state',
        'queue_order',
        'queued_at',
        'activated_at',
        'payment_note',
        'assign_by',
        'purchase_token',
        'auto_renewing',
        'payment_received_from',
        'status',
    ];

    // Cast attributes to proper types
    protected $casts = [
        // 'plan_activate_date' => 'date',
        // 'plan_expiry_date' => 'date',
        'plan_validity_days' => 'integer',
        'addon_validity_days' => 'integer',
        'carried_forward_days' => 'integer',
        'total_validity_days' => 'integer',
        'plan_interest' => 'integer',
        'addon_interest' => 'integer',
        'carried_forward_interest' => 'integer',
        'interests_total' => 'integer',
        'interests_used' => 'integer',
        'plan_view_profile' => 'integer',
        'addon_view_profile' => 'integer',
        'carried_forward_view_profile' => 'integer',
        'view_profile_total' => 'integer',
        'view_profile_used' => 'integer',
        'plan_contact_views' => 'integer',
        'addon_contact_views' => 'integer',
        'carried_forward_contact_views' => 'integer',
        'contact_views_total' => 'integer',
        'contact_views_used' => 'integer',
        'plan_video_minutes' => 'integer',
        'addon_video_minutes' => 'integer',
        'carried_forward_video_minutes' => 'integer',
        'video_minutes_total' => 'integer',
        'video_minutes_used' => 'integer',
        'plan_audio_minutes' => 'integer',
        'addon_audio_minutes' => 'integer',
        'carried_forward_audio_minutes' => 'integer',
        'audio_minutes_total' => 'integer',
        'audio_minutes_used' => 'integer',
        // 'can_chat' => 'boolean',
        'plan_amount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'franchise_comm_per' => 'decimal:2',
        'franchise_comm_amt' => 'decimal:2',
        'current_plan' => 'string',
        'is_renewal' => 'string',
        'plan_state' => 'string',
        'queue_order' => 'integer',
        'queued_at' => 'datetime',
        'activated_at' => 'datetime',
        'status' => 'string',
    ];

    /**
     * Relationships
     */

    // Example: Payment belongs to a member
    public function member()
    {
        return $this->belongsTo(Register::class, 'member_id', 'id');
    }

    // Example: Payment belongs to a plan
    public function plan()
    {
        return $this->belongsTo(MembershipPlan::class, 'plan_id', 'id');
    }

    public function franchise()
    {
        return $this->belongsTo(Franchise::class, 'franchise_id', 'id');
    }

    // The plan this payment renewed/upgraded from, if any.
    public function previousPayment()
    {
        return $this->belongsTo(Payment::class, 'previous_payment_id', 'id');
    }

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'SUCCESS');
    }

    public function scopeCurrent($query)
    {
        return $query->where('current_plan', 'Yes');
    }

    public function scopePersonalize($query)
    {
        return $query->where('personalize_plan', 'Yes');
    }

    // The plan actually running right now (recharge-style flow).
    public function scopeActivePlanState($query)
    {
        return $query->where('plan_state', 'Active');
    }

    // Purchased but waiting for the current plan to expire.
    public function scopeQueued($query)
    {
        return $query->where('plan_state', 'Queued');
    }

    public function scopeExpiredPlanState($query)
    {
        return $query->where('plan_state', 'Expired');
    }

    // ─── Helpers ───────────────────────────────────────────────────────────────

    public function isExpired(): bool
    {
        return $this->plan_expired && now()->greaterThan($this->plan_expired);
    }

    public function remainingDays(): int
    {
        if (! $this->plan_expired) {
            return 0;
        }
        return max(0, (int) now()->diffInDays($this->plan_expired, false));
    }

    public function getInterestsRemainingAttribute()
    {
        return max(0, (int)$this->interests_total - (int)$this->interests_used);
    }

    public function getViewProfileRemainingAttribute()
    {
        return max(0, (int)$this->view_profile_total - (int)$this->view_profile_used);
    }

    public function getContactViewsRemainingAttribute()
    {
        return max(0, (int)$this->contact_views_total - (int)$this->contact_views_used);
    }

    public function getVideoMinutesRemainingAttribute()
    {
        // FIX: this previously compared against `plan_video_minutes` only,
        // which ignored add-on / carried-forward minutes and understated
        // (or, after this change's totals grow, overstated) what was left.
        // Remaining must be measured against the full total.
        return max(0, (int)$this->video_minutes_total - (int)$this->video_minutes_used);
    }

    public function getAudioMinutesRemainingAttribute()
    {
        // FIX: same bug as above, mirrored for audio minutes.
        return max(0, (int)$this->audio_minutes_total - (int)$this->audio_minutes_used);
    }

    public function getCanVideoCallAttribute(): bool
    {
        return $this->video_minutes_remaining > 0;
    }

    public function getCanVoiceCallAttribute(): bool
    {
        return $this->audio_minutes_remaining > 0;
    }

    public function getIsQueuedAttribute(): bool
    {
        return $this->plan_state === 'Queued';
    }

    public function getIsActivePlanStateAttribute(): bool
    {
        return $this->plan_state === 'Active';
    }
}