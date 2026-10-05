<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\AffiliateMember;
use Illuminate\Database\Eloquent\SoftDeletes;

class AffiliateMemberLoginHistory extends Model
{
    use SoftDeletes;
    protected $table = 'affiliate_member_login_history';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'affiliate_member_id',
        'login_at',
        'ip_address',
        'browser',
        'os',
        'device',
        'is_mobile',
        'is_tablet',
        'is_bot',
        'is_bot_name'
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'is_mobile' => 'boolean',
        'is_tablet' => 'boolean',
        'is_bot' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function affiliateMember(): BelongsTo
    {
        return $this->belongsTo(AffiliateMember::class, 'affiliate_member_id');
    }

    public function scopeOfMember($query, int $memberId)
    {
        return $query->where('affiliate_member_id', $memberId);
    }
}