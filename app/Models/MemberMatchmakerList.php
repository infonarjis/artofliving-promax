<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberMatchmakerList extends Model
{
    use SoftDeletes;
    protected $table = 'member_matchmaker_list';

    protected $fillable = [
        'self_member_id', 'self_member_matri_id', 'opposite_member_id',
        'opposite_member_matri_id', 'personalize_member_id',
        'personalize_member_matri_id', 'staff_id', 'last_action', 'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'staff_id'   => 'integer',
    ];

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'APPROVED');
    }

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function selfMember(): BelongsTo
    {
        return $this->belongsTo(Register::class, 'self_member_id');
    }

    public function oppositeMember(): BelongsTo
    {
        return $this->belongsTo(Register::class, 'opposite_member_id');
    }

    public function personalizeMember(): BelongsTo
    {
        return $this->belongsTo(Register::class, 'personalize_member_id');
    }
}