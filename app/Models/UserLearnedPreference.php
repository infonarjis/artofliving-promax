<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLearnedPreference extends Model
{
    protected $table = 'user_learned_preferences';

    protected $fillable = [
        'member_id',
        'preference_data',
        'numeric_data',
        'sample_size',
        'last_learned_at',
    ];

    protected $casts = [
        'preference_data' => 'array',
        'numeric_data'    => 'array',
        'last_learned_at' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(Register::class, 'member_id');
    }
}
