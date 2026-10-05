<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnnouncementBanner extends Model
{
    use SoftDeletes;
    protected $table = 'annoucement_banner'; // keep original table name

    protected $primaryKey = 'id';

    public $timestamps = false; 
    // because you are using DATETIME with default current_timestamp (not Laravel timestamps)

    protected $fillable = [
        'title',
        'description',
        'link',
        'image',
        'lang_code',
        'lang_id',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // -----------------------------------------
    // Scopes (very useful in your project)
    // -----------------------------------------

    public function scopeActive($query)
    {
        return $query->where('status', 'APPROVED');
    }

    public function scopeLang($query, $langCode)
    {
        return $query->where('lang_code', $langCode);
    }
}