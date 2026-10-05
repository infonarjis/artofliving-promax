<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MatchPairMeeting extends Model
{
    use SoftDeletes;
    protected $table = 'match_pair_meeting';

    protected $fillable = [
        'match_id', 'member1_id', 'member1_matri_id', 'member2_id',
        'member2_matri_id', 'staff_id', 'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'APPROVED');
    }

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function member1(): BelongsTo
    {
        return $this->belongsTo(Register::class, 'member1_id');
    }

    public function member2(): BelongsTo
    {
        return $this->belongsTo(Register::class, 'member2_id');
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(MatchMemberMeeting::class, 'match_pair_meeting_id');
    }
}