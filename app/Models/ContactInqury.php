<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactInqury extends Model
{
    use SoftDeletes;
    protected $table = 'contact_inqury';

    public $timestamps = false; // because table has only created_at (no updated_at)

    protected $fillable = [
        'name',
        'email',
        'mobile_number',
        'subject',
        'message',
        'created_at',
    ];
}