<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestCallBack extends Model
{
    use SoftDeletes;

    protected $table = 'request_call_back';

    protected $fillable = [
        'member_id',
        'matri_id',
        'name',
        'email',
        'mobile',
        'status',
    ];

    protected $casts = [
        'member_id'  => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /** the member who asked for the call back (null for visitors) */
    public function member()
    {
        return $this->belongsTo(Register::class, 'member_id');
    }
}