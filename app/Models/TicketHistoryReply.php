<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketHistoryReply extends Model
{
    use SoftDeletes;

    protected $table = 'ticket_history_reply';

    protected $fillable = [
        'ticket_id',
        'ticket_number',
        'user_id',
        'user_type',
        'comment',
        'created_at',
        'updated_oppsite',
    ];

    public $timestamps = false; // table does not have updated_at

    protected $casts = [
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * User types
     */
    const USER_ADMIN  = 'Admin';
    const USER_CLIENT = 'Client';
    const USER_STAFF  = 'Staff';

    /**
     * Opposite update status
     */
    const OPPOSITE_PENDING = 'Pending';
    const OPPOSITE_UPDATED = 'Updated';

    /**
     * Relationship: Reply belongs to ticket
     */
    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id', 'id');
    }
}