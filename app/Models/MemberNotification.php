<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class MemberNotification extends BaseModel
{
    use SoftDeletes;
    protected $table = 'member_notification';

    protected $fillable = [
        'sender_member_id', 'receiver_member_id', 'sender_matri_id',
        'receiver_matri_id', 'title', 'message', 'action',
        'notification_by', 'image', 'is_read', 'status'
    ];

    protected $casts = [
        // 'created_at'   => 'datetime',
        // 'read_at'   => 'datetime',
        // 'updated_at'   => 'datetime',
        // 'is_read'      => 'boolean',
        // 'notification_by'=> 'integer',
        // 'status'       => 'boolean',
    ];

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', 0);
    }

    public function scopeByAdmin($query)
    {
        return $query->where('notification_by', 1);
    }

    public function scopeByUser($query)
    {
        return $query->where('notification_by', 0);
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

    public function markAsRead(): void
    {
        $this->update(['is_read' => 1]);
    }
}