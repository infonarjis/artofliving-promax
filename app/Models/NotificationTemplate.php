<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationTemplate extends Model
{
    use SoftDeletes;

    protected $table = 'notification_templates';

    protected $primaryKey = 'id';

    public $timestamps = false; // custom datetime columns

    protected $fillable = [
        'title',
        'description',
        'action',
        'notification_type',
        'sender_type',
        'receiver_type',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* -------------------- SCOPES -------------------- */

    public function scopeActive($query)
    {
        return $query->where('status', 'APPROVED');
    }

    public function scopeForSender($query, string $senderType)
    {
        return $query->where('sender_type', $senderType);
    }

    public function scopeForReceiver($query, string $receiverType)
    {
        return $query->where('receiver_type', $receiverType);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('notification_type', $type);
    }

    /* -------------------- HELPERS -------------------- */

    /**
     * Get template by notification type and sender/receiver type.
     */
    public static function getByType(
        string $type,
        string $senderType = 'System',
        string $receiverType = 'User'
    ) {
        return self::active()
            ->forSender($senderType)
            ->forReceiver($receiverType)
            ->byType($type)
            ->first();
    }

    /**
     * Replace dynamic variables like {{viewer_id}}
     */
    public function parseDescription(array $replacements = []): string
    {
        $content = $this->description;

        foreach ($replacements as $key => $value) {
            $content = str_replace($key, $value, $content);
        }

        return $content;
    }
}
