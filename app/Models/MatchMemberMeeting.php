<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MatchMemberMeeting extends Model
{
    use SoftDeletes;
    protected $table = 'match_member_meeting';

    protected $fillable = [
        'match_id', 'match_pair_meeting_id', 'member1_id', 'member1_matri_id',
        'member2_id', 'member2_matri_id', 'staff_id', 'date_time', 'description',
        'address', 'google_map_link', 'member1_status', 'member1_reject_remark',
        'member2_status', 'member2_reject_remark', 'meeting_status',
        'meeting_remark', 'status', 'admin_remark'
    ];

    protected $casts = [
        'date_time'       => 'datetime',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
        'member1_status'  => 'integer',
        'member2_status'  => 'integer',
        'meeting_status'  => 'integer',
    ];

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'APPROVED');
    }

    public function scopeCompleted($query)
    {
        return $query->where('meeting_status', 1);
    }

    public function scopePending($query)
    {
        return $query->where('meeting_status', 0);
    }

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function pairMeeting(): BelongsTo
    {
        return $this->belongsTo(MatchPairMeeting::class, 'match_pair_meeting_id');
    }

    public function member1(): BelongsTo
    {
        return $this->belongsTo(Register::class, 'member1_id');
    }

    public function member2(): BelongsTo
    {
        return $this->belongsTo(Register::class, 'member2_id');
    }

    // ─── Helpers ───────────────────────────────────────────────────────────────

    public function member1StatusLabel(): string
    {
        return match ($this->member1_status) {
            0 => 'Pending', 1 => 'Accept', 2 => 'Reject', default => 'Unknown'
        };
    }

    public function member2StatusLabel(): string
    {
        return match ($this->member2_status) {
            0 => 'Pending', 1 => 'Accept', 2 => 'Reject', default => 'Unknown'
        };
    }

    public function isCompleted(): bool
    {
        return $this->meeting_status === 1;
    }
}