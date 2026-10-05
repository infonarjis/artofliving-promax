<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class SendBulkEmail extends Model
{
    use SoftDeletes;
    protected $table = 'send_bulk_email';

    protected $primaryKey = 'id';

    public $timestamps = false; // No updated_at column in table

    protected $fillable = [
        'email',
        'email_subject',
        'email_content',
        'is_processing',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */
}