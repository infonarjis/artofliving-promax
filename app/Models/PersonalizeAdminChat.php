<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonalizeAdminChat extends Model
{
    use SoftDeletes;
    protected $table = 'personalize_admin_chat';
    protected $fillable = [
        'member_id',
        'sender_type',   // 1=admin, 2=member
        'message',
        'sender_name',
        'sender_profile',
        'status',
        'is_read'
    ];

    public $timestamps = true;
}
