<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class SendBulkNotification extends Model
{
    use SoftDeletes;
    protected $table = 'send_bulk_notification';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'title',
        'message',
        'target_type',
        'target_value',
        'status'
    ];

    protected $casts = [
        'status'             => 'boolean',
        'created_at'         => 'datetime',
        'updated_at'         => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'APPROVED');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getIsApprovedAttribute(): bool
    {
        return (bool) $this->status;
    }
}
