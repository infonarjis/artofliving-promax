<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MemberRiskScore extends Model
{
    use HasFactory;

    protected $table = 'member_risk_scores';

    protected $fillable = [
        'member_id',
        'suspend_reason',
        'risk_score',
        'warning_count',
        'is_restricted',
        'is_suspended',
        'last_warning_at',
        'suspended_at',
    ];

    protected $casts = [
        'risk_score'     => 'integer',
        'warning_count'  => 'integer',
        'is_restricted'  => 'boolean',
        'is_suspended'   => 'boolean',
        'last_warning_at'=> 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function member()
    {
        return $this->belongsTo(Register::class, 'member_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods (AI Suspicious Monitoring Friendly)
    |--------------------------------------------------------------------------
    */

    public function addRisk(int $points): void
    {
        $this->increment('risk_score', $points);
    }

    public function addWarning(): void
    {
        $this->increment('warning_count');
        $this->update(['last_warning_at' => now()]);
    }

    public function restrict(): void
    {
        $this->update(['is_restricted' => true]);
    }

    public function suspend(): void
    {
        $this->update([
            'is_suspended' => true,
            'is_restricted' => true,
        ]);
    }

    public function clearRestrictions(): void
    {
        $this->update([
            'is_restricted' => false,
            'risk_score' => 0,
            'warning_count' => 0,
        ]);
    }
}