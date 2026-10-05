<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiMatchQueue extends Model
{
    use HasFactory;

    protected $table = 'ai_match_queue';

    protected $fillable = [
        'member_id',
        'matched_member_id',
        'score',
        'match_date',
        'queue_status',
    ];

    protected $casts = [
        'match_date' => 'date',
        'queue_status' => 'boolean',
    ];

    /**
     * User who owns this match entry
     */
    public function member()
    {
        return $this->belongsTo(Register::class, 'member_id');
    }

    /**
     * The matched user
     */
    public function matchedMember()
    {
        return $this->belongsTo(Register::class, 'matched_member_id');
    }
}
