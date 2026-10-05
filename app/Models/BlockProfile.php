<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockProfile extends Model
{
    use SoftDeletes;
    protected $table = 'block_profile';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'sender_member_id',
        'receiver_member_id',
        'sender_matri_id',
        'receiver_matri_id',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ==============================
    // RELATIONSHIPS
    // ==============================

    // Sender user
    public function sender()
    {
        return $this->belongsTo(Register::class, 'sender_member_id', 'id');
    }

    // Receiver user
    public function receiver()
    {
        return $this->belongsTo(Register::class, 'receiver_member_id', 'id');
    }

    // ==============================
    // SCOPES (IMPORTANT)
    // ==============================

    // Active records (approved + not deleted)
    public function scopeActive($query)
    {
        return $query->where('status', 'APPROVED');
    }

    // By sender
    public function scopeBySender($query, $senderId)
    {
        return $query->where('sender_member_id', $senderId);
    }

    // By receiver
    public static function getBlockedMemberIds(int $memberId): array
    {
        $blocks = self::active()
            ->where(function ($q) use ($memberId) {
                $q->where('sender_member_id', $memberId)
                    ->orWhere('receiver_member_id', $memberId);
            })
            ->get(['sender_member_id', 'receiver_member_id']);

        $senderIds   = $blocks->pluck('sender_member_id')->map(fn($id) => (int) $id);
        $receiverIds = $blocks->pluck('receiver_member_id')->map(fn($id) => (int) $id);

        return $senderIds->merge($receiverIds)
            ->unique()
            ->reject(fn($id) => $id === $memberId)  // remove self
            ->values()
            ->toArray();
    }

    public static function isEitherBlocked(int $userA, int $userB): bool
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

    public static function getEitherBlockedMap($memberId, array $ids)
    {
        return self::where(function ($q) use ($memberId, $ids) {
            $q->where('sender_member_id', $memberId)
                ->whereIn('receiver_member_id', $ids);
        })
            ->orWhere(function ($q) use ($memberId, $ids) {
                $q->whereIn('sender_member_id', $ids)
                    ->where('receiver_member_id', $memberId);
            })
            ->pluck('receiver_member_id', 'receiver_member_id')
            ->toArray();
    }
}
