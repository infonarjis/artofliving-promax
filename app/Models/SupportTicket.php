<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportTicket extends Model
{
    use SoftDeletes;

    protected $table = 'support_ticket';

    protected $fillable = [
        'status',
        'subject',
        'priority',
        'description',
        'attachment_1',
        'attachment_2',
        'attachment_3',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    const STATUS_OPEN    = 'Open';
    const STATUS_RESOLVE = 'Resolve';
    const STATUS_CLOSE   = 'Close';
    const STATUS_REOPEN  = 'Reopen';

    protected static function booted()
    {
        static::created(function ($ticket) {
            $ticket->updateQuietly([
                'ticket_number' => 'TICKET-' . str_pad($ticket->id, 6, '0', STR_PAD_LEFT),
            ]);
        });
    }

    public function replies()
    {
        return $this->hasMany(TicketHistoryReply::class, 'ticket_id', 'id')->orderBy('created_at', 'asc');
    }
}