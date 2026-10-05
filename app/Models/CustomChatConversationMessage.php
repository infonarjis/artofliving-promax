<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomChatConversationMessage extends Model
{
    use SoftDeletes;
    protected $table = 'custom_chat_conversation_message';

    protected $primaryKey = 'id';

    public $timestamps = false; // because you're using send_on instead

    protected $fillable = [
        'conversation_id',
        'sender_member_id',
        'sender_member_matri_id',
        'receiver_member_id',
        'receiver_member_matri_id',
        'type',
        'message',
        'is_read',
        'chat_status',
        'blocked_member_id',
        'send_on'
    ];

    protected $casts = [
        'send_on' => 'datetime'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function conversation()
    {
        return $this->belongsTo(CustomChatConversation::class, 'conversation_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    public function scopeUnread($query)
    {
        return $query->whereIn('chat_status', [0, 1]);
    }

    public function sender()
    {
        return $this->belongsTo(Register::class, 'sender_member_id');
    }

    public function receiver()
    {
        return $this->belongsTo(Register::class, 'receiver_member_id');
    }
}
