<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchSchedule extends Model
{
    protected $fillable = [
        'schedule_date',
        'send_total_match',
        'match_criteria',
        'match_sending_mode',
        'total_members',
        'processed_members',
        'matches_sent',
        'emails_sent',
        'sms_sent',
        'last_processed_id',
        'status',
        'started_at',
        'completed_at',
        'error_message',
    ];

    protected $casts = [
        'schedule_date' => 'date',
        'match_criteria' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * All the match/SMS rows created during this specific scheduler run.
     */
    public function sendMatchesSms()
    {
        return $this->hasMany(SendMatchesSms::class, 'match_schedule_id');
    }

    /**
     * How far along processing is (0-100), based on members walked vs. total eligible.
     */
    public function getProgressPercentAttribute(): int
    {
        if ($this->total_members <= 0) {
            return $this->status === 'completed' ? 100 : 0;
        }

        return (int) min(100, round(($this->processed_members / $this->total_members) * 100));
    }

    /**
     * How many members are still left to process this run.
     */
    public function getRemainingMembersAttribute(): int
    {
        return max(0, $this->total_members - $this->processed_members);
    }
}
