<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhotoRequest extends Model
{
    use SoftDeletes;
    protected $table = 'photo_request';

    public $timestamps = false; // table has created_at & updated_at but no created_at

    protected $fillable = [
        'sender_member_id',
        'receiver_member_id',
        'sender_matri_id',
        'receiver_matri_id',
        'receiver_response',
        'created_at',
        'status',
        'is_read_sender',
        'is_read_receiver',
        'is_notify',
        'updated_at'
    ];

    protected $casts = [
        'created_at'  => 'datetime',
        'updated_at' => 'datetime',
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

    // In PhotoRequest model
    public static function getAcceptedReceiverIds(int $senderId, array $receiverIds): array
    {
        return self::active()
            ->accepted()
            ->where('sender_member_id', $senderId)
            ->whereIn('receiver_member_id', $receiverIds)
            ->pluck('receiver_member_id')
            ->toArray();
    }

    public function scopeBetweenMembers($query, $userId, $otherId)
    {
        return $query->where(function ($q) use ($userId, $otherId) {
            $q->where('sender_member_id', $userId)
                ->where('receiver_member_id', $otherId);
        })->orWhere(function ($q) use ($userId, $otherId) {
            $q->where('receiver_member_id', $userId)
                ->where('sender_member_id', $otherId);
        });
    }
}
