<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomChatConversation extends Model
{
    use SoftDeletes;
    protected $table = 'custom_chat_conversation';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'member1_id',
        'member1_matri_id',
        'member2_id',
        'member2_matri_id',
        'member1_block',
        'member2_block',
        'last_message_date',
        'ai_suggestions',

        'request_status',
        'requested_by',
        'responded_at',
    ];

    protected $casts = [
        'member1_block' => 'boolean',
        'member2_block' => 'boolean',
        'last_message_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function messages()
    {
        return $this->hasMany(CustomChatConversationMessage::class, 'conversation_id');
    }

    public function lastMessage()
    {
        return $this->hasOne(CustomChatConversationMessage::class, 'conversation_id')
            ->latest('send_on');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function member1()
    {
        return $this->belongsTo(Register::class, 'member1_id');
    }

    public function member2()
    {
        return $this->belongsTo(Register::class, 'member2_id');
    }
}
