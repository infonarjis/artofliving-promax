<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShortlistProfile extends Model
{
    use SoftDeletes;
    protected $table = 'shortlist_profile';

    protected $primaryKey = 'id';

    public $timestamps = false; // because you are using custom datetime

    protected $fillable = [
        'sender_member_id',
        'receiver_member_id',
        'sender_matri_id',
        'receiver_matri_id',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Sender User
    public function sender()
    {
        return $this->belongsTo(Register::class, 'sender_member_id');
    }

    // Receiver User
    public function receiver()
    {
        return $this->belongsTo(Register::class, 'receiver_member_id');
    }

    // ─── Scopes ────────────────────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', 'APPROVED');
    }
}