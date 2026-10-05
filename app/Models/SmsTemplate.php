<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsTemplate extends Model
{
    use SoftDeletes;
    protected $table = 'sms_templates';

    protected $primaryKey = 'id';

    public $timestamps = false; // because no created_at column

    protected $fillable = [
        'status',
        'template_id',
        'template_name',
        'sms_content',
        'updated_at'
    ];

    protected $casts = [
        'updated_at' => 'datetime',
    ];

    /**
     * Scope: Only approved templates
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'APPROVED');
    }

    /**
     * Get template by name safely
     */
    public static function getByName(string $name)
    {
        return self::approved()
            ->where('template_name', $name)
            ->first();
    }

    /**
     * Replace dynamic variables inside sms content
     * Example: XXXnameXXX, web_frienly_name
     */
    public function parseContent(array $replacements = []): string
    {
        $content = $this->sms_content;

        foreach ($replacements as $key => $value) {
            $content = str_replace($key, $value, $content);
        }

        return $content;
    }
}