<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpressInterest extends Model
{
    use SoftDeletes;
    protected $table = 'express_interest';

    protected $fillable = [
        'sender_member_id', 'receiver_member_id', 'sender_matri_id',
        'receiver_matri_id', 'receiver_response', 'reminder_count',
        'is_read_sender', 'is_read_receiver', 'is_notify', 'send_type', 'status'
    ];

    protected $casts = [
        // 'created_at'     => 'datetime',
        // 'updated_at'     => 'datetime',
        'reminder_count' => 'integer',
    ];

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'APPROVED');
    }

    public function scopeAccepted($query)
    {
        return $query->where('receiver_response', 'Accepted');
    }

    public function scopePending($query)
    {
        return $query->where('receiver_response', 'Pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('receiver_response', 'Rejected');
    }

    public function scopeUnreadBySender($query)
    {
        return $query->where('is_read_sender', '0');
    }

    public function scopeUnreadByReceiver($query)
    {
        return $query->where('is_read_receiver', '0');
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
        return $this->receiver_response === 'Accepted';
    }

    public function isPending(): bool
    {
        return $this->receiver_response === 'Pending';
    }

    public function markReadBySender(): void
    {
        $this->update(['is_read_sender' => '1']);
    }

    public function markReadByReceiver(): void
    {
        $this->update(['is_read_receiver' => '1']);
    }

    public static function interestSentOrReceivedExists(int $userA, int $userB): bool
    {
        return self::where(function ($q) use ($userA, $userB) {
            $q->where(function ($q2) use ($userA, $userB) {
                $q2->where('sender_member_id', $userA)
                    ->where('receiver_member_id', $userB);
            })->orWhere(function ($q2) use ($userA, $userB) {
                $q2->where('sender_member_id', $userB)
                    ->where('receiver_member_id', $userA);
            });
        })->exists();
    }
}