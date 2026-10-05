<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AddOnPayment extends Model
{
    use SoftDeletes;
    // Table name (optional if table name follows Laravel convention)
    protected $table = 'add_on_payments';

    // Primary key
    protected $primaryKey = 'id';

    // Auto-incrementing
    public $incrementing = true;

    // Key type
    protected $keyType = 'int';

    // Timestamps
    public $timestamps = true;

    // Mass assignable attributes
    protected $fillable = [
        'member_id',
        'payment_id',
        'add_on_id',
        'package_title',
        'package_category',
        'package_count',
        'package_amount',
        'description',

        'coupon_id',
        'discount_amount',
        'tax_amount',
        'grand_total',
        'transaction_id',
        'payment_mode',
        'payment_received_from',
        'purchase_token',

        'created_at',
        'updated_at',
        'status',
    ];

    // Casts
    protected $casts = [
        'created_at' => 'datetime',
        'status' => 'string',
    ];

    /**
     * Relation to Member (user)
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Register::class, 'member_id'); // replace Member::class with your user model if different
    }

    /**
     * Relation to Payment
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    /**
     * Relation to AddOn package
     */
    public function addOn(): BelongsTo
    {
        return $this->belongsTo(AddOnPayment::class, 'add_on_id'); // create AddOn model if not exists
    }
}
