<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanUpgradeHistory extends Model
{
    use SoftDeletes;

    protected $table = 'plan_upgrade_histories';

    protected $fillable = [
        'member_id',
        'previous_payment_id',
        'new_payment_id',
        'previous_plan_id',
        'previous_plan_name',
        'new_plan_id',
        'new_plan_name',
        'type',
        'status',
        'carried_forward_days',
        'carried_forward_view_profile',
        'carried_forward_interest',
        'carried_forward_contact_views',
        'carried_forward_video_minutes',
        'carried_forward_audio_minutes',
        'upgrade_date',
    ];

    protected $casts = [
        'type' => 'string',
        'status' => 'string',
        'carried_forward_days' => 'integer',
        'carried_forward_view_profile' => 'integer',
        'carried_forward_interest' => 'integer',
        'carried_forward_contact_views' => 'integer',
        'carried_forward_video_minutes' => 'integer',
        'carried_forward_audio_minutes' => 'integer',
        'upgrade_date' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(Register::class, 'member_id', 'id');
    }

    public function previousPayment()
    {
        return $this->belongsTo(Payment::class, 'previous_payment_id', 'id');
    }

    public function newPayment()
    {
        return $this->belongsTo(Payment::class, 'new_payment_id', 'id');
    }

    public function previousPlan()
    {
        return $this->belongsTo(MembershipPlan::class, 'previous_plan_id', 'id');
    }

    public function newPlan()
    {
        return $this->belongsTo(MembershipPlan::class, 'new_plan_id', 'id');
    }

    public function scopeQueued($query)
    {
        return $query->where('type', 'Queue');
    }

    public function scopeActivated($query)
    {
        return $query->where('type', 'Activate');
    }
}