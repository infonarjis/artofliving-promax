<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SendMatchesSms extends Model
{
    use HasFactory;

    protected $table = 'send_matches_sms';

    protected $fillable = [
        'match_schedule_id',
        'sms_mobile',
        'email',
        'my_id',
        'other_id',
        'email_sent_status',
        'sms_sent_status',
        'sent_date',
    ];

    protected $casts = [
        'sent_date' => 'datetime',
    ];

    /**
     * Relationship: My Member
     */
    public function myMember()
    {
        return $this->belongsTo(Register::class, 'my_id');
    }

    /**
     * Relationship: Other Member
     */
    public function otherMember()
    {
        return $this->belongsTo(Register::class, 'other_id');
    }

    public function matchSchedule()
    {
        return $this->belongsTo(MatchSchedule::class, 'match_schedule_id');
    }
}
