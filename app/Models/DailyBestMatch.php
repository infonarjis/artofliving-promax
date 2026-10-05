<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyBestMatch extends Model
{
    protected $table = 'daily_best_matches';

    protected $fillable = [
        'member_id',
        'matched_member_id',
        'match_percent',
        'score_breakdown',
        'rank',
        'computed_date',
    ];

    protected $casts = [
        'score_breakdown' => 'array',
        'computed_date'   => 'date',
    ];

    public function matchedMember()
    {
        return $this->belongsTo(Register::class, 'matched_member_id');
    }
}
