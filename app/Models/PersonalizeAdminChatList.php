<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonalizeAdminChatList extends Model
{
    use SoftDeletes;
    protected $table = 'personalize_admin_chat_list';
    protected $fillable = [
        'member_id',
        'matri_id',
        'admin_unread_count',
        'web_unread_count',
        'last_message',
        'last_message_time'
    ];

    public $timestamps = true;

    public function member()
    {
        return $this->belongsTo(Register::class, 'member_id', 'id');
    }
}
