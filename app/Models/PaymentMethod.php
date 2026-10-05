<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use SoftDeletes;
    protected $table = 'payment_method';

    protected $fillable = [
        'status',
        'name',
        'logo',
        'client_id',
        'client_secret',
        'payment_mode'
    ];

    // protected $hidden = [
    //     'client_id',
    //     'client_secret',
    // ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Only approved payment methods.
     * Exclude soft-deleted records.
    */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'APPROVED');
    }

    /**
     * Filter by payment mode (Test / Live).
     */
    public function scopeMode(Builder $query, string $mode): Builder
    {
        return $query->where('payment_mode', $mode);
    }
}