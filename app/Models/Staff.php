<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Staff extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $table = 'staff';

    protected $fillable = [
        'staff_prefix', 'type', 'gender', 'username', 'email',
        'password', 'mobile', 'basic_salary', 'profile_image',
        'birthdate', 'marital_status','password_decrypted',
        'password',
        'role_id', 'ip_address', 'last_login',
        'status','last_activity'
    ];

    protected $casts = [
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'last_login'   => 'datetime',
        // 'birthdate'    => 'date',
        'role_id'         => 'integer',
    ];

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'APPROVED');
    }

    public function scopeMale($query)
    {
        return $query->where('gender', 'Male');
    }

    public function scopeFemale($query)
    {
        return $query->where('gender', 'Female');
    }

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function assignedMembers(): HasMany
    {
        return $this->hasMany(Register::class, 'staff_assign_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(CommentMaster::class, 'posted_by')
                    ->where('posted_user_type', 'staff');
    }

    public function meetingsHandled(): HasMany
    {
        return $this->hasMany(MatchMemberMeeting::class, 'staff_id');
    }

    public function matchmakerList(): HasMany
    {
        return $this->hasMany(MemberMatchmakerList::class, 'staff_id');
    }

    public function staffRole(): BelongsTo
    {
        return $this->belongsTo(StaffRole::class, 'role_id');
    }

    // ─── Helpers ───────────────────────────────────────────────────────────────

    public function isApproved(): bool
    {
        return $this->status === 'APPROVED';
    }
}