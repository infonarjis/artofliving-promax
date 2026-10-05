<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommentsOfLeadGeneration extends Model
{
    use SoftDeletes;
    protected $table = 'comments_of_lead_generation';

    protected $primaryKey = 'id';

    public $timestamps = false; // using custom datetime fields

    protected $fillable = [
        'lead_generation_id',
        'posted_user_type',
        'posted_by',
        'comment',
        'next_followup_date',
        'next_followup_time',
        'follow_up_status',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'next_followup_date' => 'date'
    ];

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActiveFollowUp($query)
    {
        return $query->where('follow_up_status', '1');
    }

    public function scopeByIndex($query, $indexId)
    {
        return $query->where('lead_generation_id', $indexId);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    public function markFollowUpDone()
    {
        $this->update(['follow_up_status' => '1']);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships (adjust models if names differ)
    |--------------------------------------------------------------------------
    */

    public function leadGeneration()
    {
        return $this->belongsTo(LeadGeneration::class, 'lead_generation_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'posted_by');
    }

    public function franchise()
    {
        return $this->belongsTo(Franchise::class, 'posted_by');
    }
}
