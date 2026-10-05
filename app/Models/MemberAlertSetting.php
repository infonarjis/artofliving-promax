<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberAlertSetting extends Model
{
    use SoftDeletes;
    protected $table = 'member_alert_setting';

    protected $primaryKey = 'id';

    public $timestamps = false; // you are manually managing datetime

    protected $fillable = [
        'member_id',
        'template_id',
        'template_type',
        'enabled',
        'created_at',
        'updated_at',
    ];

    /**
     * Enum helpers
     */
    const TYPE_EMAIL        = 'email';
    const TYPE_SMS          = 'sms';
    const TYPE_NOTIFICATION = 'notification';

    /**
     * Relationships
     */

    public function member()
    {
        return $this->belongsTo(Register::class, 'member_id', 'id');
    }

    /**
     * Scopes (VERY IMPORTANT for your project usage)
     */

    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('template_type', $type);
    }

    /**
     * Helper to check if alert is enabled
     */
    // public static function isEnabled($memberId, $templateId, $type)
    // {
    //     return self::where('member_id', $memberId)
    //         ->where('template_type', $type)
    //         ->where('template_id', $templateId)
    //         ->where('enabled', false)
    //         ->exists();
    // }
    
    public static function isEnabled($memberId, $templateId, $type)
    {
        return self::where('member_id', $memberId)
            ->where('template_type', $type)
            ->where('template_id', $templateId)
            ->where('enabled', 0)
            ->exists();
    }
}
