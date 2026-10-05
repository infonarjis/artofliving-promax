<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Franchise extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $table = 'franchise';

    protected $fillable = [
        'type', 'status', 'username', 'email', 'mobile',
        'password', 'commission', 'referral_code',
        'c_password', 'ip_address', 'last_login',
        'last_activity'
    ];

    protected $hidden = [
        'password', 'password_decrypted', 'c_password',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'last_login' => 'datetime',
        'commission' => 'float',
    ];

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'APPROVED');
    }

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function assignedMembers(): HasMany
    {
        return $this->hasMany(Register::class, 'franchise_assign_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'franchise_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(CommentMaster::class, 'posted_by')
                    ->where('posted_user_type', 'franchise');
    }

    public function matchmakerList(): HasMany
    {
        return $this->hasMany(MemberMatchmakerList::class, 'personalize_member_id');
    }

    // ─── Helpers ───────────────────────────────────────────────────────────────

    public function isApproved(): bool
    {
        return $this->status === 'APPROVED';
    }

    public function totalCommissionEarned(): float
    {
        return $this->payments()->sum('franchise_comm_amt') ?? 0.0;
    }
}