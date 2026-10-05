<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserLoginHistory extends Model
{
    use SoftDeletes;
    protected $table = 'user_login_history';

    public $timestamps = false;

    protected $fillable = [
        'member_id', 'matri_id', 'email', 'login_from', 'login_at',
        'ip_address', 'browser', 'os', 'device', 'is_mobile',
        'is_tablet', 'is_bot', 'is_bot_name'
    ];

    protected $casts = [
        'login_at' => 'datetime',
    ];

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeFromApp($query, string $platform = 'Android')
    {
        return $query->where('login_from', $platform);
    }

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function member(): BelongsTo
    {
        return $this->belongsTo(Register::class, 'member_id');
    }
}