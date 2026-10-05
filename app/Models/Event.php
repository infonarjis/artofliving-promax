<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $table = 'events';

    protected $fillable = [
        'status',
        'title',
        'description',
        'contact_number',
        'contact_email',
        'event_date',
        'event_time',
        'venue',
        'image',
        'image_2',
        'image_3',
        'image_4',
        'currency',
        'ticket_price',
        'total_tickets',
        'sold_tickets',
        'tax_applicable',
        'tax_name',
        'tax_percentage',
        'event_facebook_link',
        'event_twitter_link',
        'event_youtube_link',
        'event_instagram_link',
        'event_pinterest_link',
        'map_address',
    ];

    protected $casts = [
        'ticket_price'   => 'float',
        'tax_percentage' => 'float',
        'total_tickets'  => 'integer',
        'sold_tickets'   => 'integer',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    const STATUS_APPROVED   = 'APPROVED';
    const STATUS_UNAPPROVED = 'UNAPPROVED';
    const STATUS_PENDING    = 'PENDING';

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function registrations()
    {
        return $this->hasMany(EventRegister::class, 'event_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->whereDate(
            'event_date',
            '>=',
            now()->toDateString()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getAvailableTicketsAttribute(): int
    {
        return max(
            0,
            ($this->total_tickets ?? 0) -
                ($this->sold_tickets ?? 0)
        );
    }

    public function getIsSoldOutAttribute(): bool
    {
        return $this->available_tickets <= 0;
    }

    public function getFormattedPriceAttribute(): string
    {
        return $this->currency . ' ' . number_format(
            $this->ticket_price,
            2
        );
    }

    /**
     * Get total tickets sold from registrations.
     */
    public function getCalculatedSoldTicketsAttribute(): int
    {
        return (int) $this->registrations()
            ->where('payment_mode', EventRegister::PAYMENT_ONLINE)
            ->sum('tickets_qty');
    }
}
