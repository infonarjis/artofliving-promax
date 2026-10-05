<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MatchList extends Model
{
    use SoftDeletes;
    protected $table = 'match_list';

    protected $fillable = [
        'sender_member_id', 'receiver_member_id', 'is_notify',
        'sent_type', 'sent_by', 'sent_by_id', 'response'
    ];

    protected $casts = [
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'response'    => 'integer',
        'sent_by'     => 'integer',
        'sent_by_id'  => 'integer',
    ];

    // ─── Scopes ────────────────────────────────────────────────────────────────
    public function scopeAccepted($query)
    {
        return $query->where('response', 1);
    }

    public function scopePending($query)
    {
        return $query->where('response', 0);
    }

    public function scopeRejected($query)
    {
        return $query->where('response', 2);
    }

    public function scopeManual($query)
    {
        return $query->where('sent_type', '1');
    }

    public function scopeAuto($query)
    {
        return $query->where('sent_type', '2');
    }

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function sender(): BelongsTo
    {
        return $this->belongsTo(Register::class, 'sender_member_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(Register::class, 'receiver_member_id');
    }

    // ─── Helpers ───────────────────────────────────────────────────────────────

    public function isAccepted(): bool
    {
        return $this->response === 1;
    }

    public function isPending(): bool
    {
        return $this->response === 0;
    }
}