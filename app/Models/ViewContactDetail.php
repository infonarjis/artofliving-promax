<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ViewContactDetail extends Model
{
    use SoftDeletes;
    protected $table = 'view_contact_details';

    protected $fillable = [
        'sender_member_id', 'receiver_member_id',
        'sender_matri_id', 'receiver_matri_id', 'status'
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

    public function sender(): BelongsTo
    {
        return $this->belongsTo(Register::class, 'sender_member_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(Register::class, 'receiver_member_id');
    }
}