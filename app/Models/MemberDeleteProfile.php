<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberDeleteProfile extends Model
{
    use SoftDeletes;
    protected $table = 'member_delete_profile';

    public $timestamps = false;

    protected $fillable = [
        'sender', 'reason', 'is_pause_plan', 'admin_action_status',
        'sent_on', 'rejected_on', 'deleted_on'
    ];

    protected $casts = [
        'sent_on'             => 'datetime',
        'rejected_on'         => 'datetime',
        'deleted_on'          => 'datetime',
        'is_pause_plan'       => 'boolean',
        'admin_action_status' => 'integer',
    ];

    // ─── Scopes ────────────────────────────────────────────────────────────────
    public function scopePending($query)
    {
        return $query->where('admin_action_status', 0);
    }

    public function scopeDeleted($query)
    {
        return $query->where('admin_action_status', 1);
    }

    public function scopeRecovered($query)
    {
        return $query->where('admin_action_status', 2);
    }

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function member(): BelongsTo
    {
        return $this->belongsTo(Register::class, 'sender');
    }

    // ─── Helpers ───────────────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->admin_action_status === 0;
    }

    public function statusLabel(): string
    {
        return match ($this->admin_action_status) {
            0 => 'Pending',
            1 => 'Deleted',
            2 => 'Recovered',
            default => 'Unknown',
        };
    }
}