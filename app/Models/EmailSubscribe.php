<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailSubscribe extends Model
{
    use SoftDeletes;
    protected $table = 'email_subscribe';
    public $timestamps = false; // since created_at is handled manually
    protected $fillable = ['email', 'created_at'];
}