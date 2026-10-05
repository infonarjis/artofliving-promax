<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfileReportSpam extends Model
{
    use SoftDeletes;
    protected $table = 'profile_report_spam';

    protected $primaryKey = 'id';

    public $timestamps = false; // table has only created_at

    protected $fillable = [
        'report_id',
        'report_matri_id',
        'report_by',
        'report_by_matri_id',
        'report_type',
        'reason',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Profile being reported
    public function reportedProfile()
    {
        return $this->belongsTo(Register::class, 'report_id', 'id');
    }

    // User who reported
    public function reportedBy()
    {
        return $this->belongsTo(Register::class, 'report_by', 'id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeByReporter($query, $memberId)
    {
        return $query->where('report_by', $memberId);
    }

    public function scopeForProfile($query, $profileId)
    {
        return $query->where('report_id', $profileId);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    public static function alreadyReported(int $reportId, int $reportBy): bool
    {
        return self::where('report_id', $reportId)
            ->where('report_by', $reportBy)
            ->exists();
    }
}