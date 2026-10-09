<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventRegister extends Model
{
    use SoftDeletes;
    protected $table      = 'events_register';
    protected $primaryKey = 'id';
    public    $timestamps = false; // uses manual created_at only

    protected $fillable = [
        'event_id',
        'member_id',
        'matri_id',
        'name',
        'mobile',
        'email',
        'hear_about_us',
        'currency',
        'ticket_price',
        'tickets_qty',
        'payment_mode',
        'transaction_id',
        'gateway_name',
        'payment_response',
        'tax_applicable',
        'tax_name',
        'tax_percentage',
        'tax_amount',
        'grand_total',
        'created_at'
    ];

    protected $casts = [
        'ticket_price'   => 'float',
        'tax_percentage' => 'float',
        'tax_amount'     => 'float',
        'grand_total'    => 'float',
        'tickets_qty'    => 'integer',
        'created_at'     => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Constants
    // -------------------------------------------------------------------------
    const PAYMENT_PENDING = 'Pending';
    const PAYMENT_ONLINE  = 'Online';
    const PAYMENT_FAILED  = 'Failed';
    const PAYMENT_CASH    = 'Cash';

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('payment_mode', self::PAYMENT_ONLINE);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('payment_mode', self::PAYMENT_PENDING);
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    /** Whether this registration has been paid */
    public function getIsPaidAttribute(): bool
    {
        return $this->payment_mode === self::PAYMENT_ONLINE;
    }

    /** Formatted grand total with currency */
    public function getFormattedTotalAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->grand_total, 2);
    }
}