<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailTemplate extends Model
{
    use SoftDeletes;
    protected $table = 'email_templates';

    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $keyType = 'int';

    // Timestamps (Laravel default is true, your table already has created_at & updated_at)
    public $timestamps = true;


    // Mass assignable attributes
    protected $fillable = [
        'template_name',
        'email_subject',
        'email_content',
        'status'
    ];

    // Casts
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope to get only active (not deleted) templates
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'APPROVED');
    }
}
