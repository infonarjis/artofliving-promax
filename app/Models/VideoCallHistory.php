<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VideoCallHistory extends Model
{
    use SoftDeletes;
    protected $table = 'video_call_history';

    public $timestamps = false;

    protected $fillable = [
        'sender_member_id', 'receiver_member_id', 'sender_matri_id',
        'receiver_matri_id', 'active_call_minute', 'type', 'created_at',
        'end_reason', 'status', 'updated_at'
    ];

    protected $casts = [
        'created_at'          => 'datetime',
        'updated_at'         => 'datetime',
        'active_call_minute' => 'integer',
    ];

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'APPROVED');
    }

    public function scopeVideoCalls($query)
    {
        return $query->where('type', 'videoCall');
    }

    public function scopeVoiceCalls($query)
    {
        return $query->where('type', 'voiceCall');
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

    public function durationFormatted(): string
    {
        $minutes = (int) ($this->active_call_minute / 60);
        $seconds = $this->active_call_minute % 60;
        return sprintf('%02d:%02d', $minutes, $seconds);
    }
}