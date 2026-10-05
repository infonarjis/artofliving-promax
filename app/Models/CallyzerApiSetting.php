<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CallyzerApiSetting extends Model
{
    use SoftDeletes;

    protected $table = 'callyzer_api_setting';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'api_mode',
        'api_key',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Scope: approved records only
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'APPROVED');
    }

    /**
     * Helper: check if API is in Live mode
     */
    public function isLive(): bool
    {
        return $this->api_mode === 'Live';
    }

    /**
     * Helper: check if API is in Test mode
     */
    public function isTest(): bool
    {
        return $this->api_mode === 'Test';
    }
}