<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfflinePayment extends Model
{
    use SoftDeletes;
    protected $table = 'offline_payment';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'account_holder_number',
        'account_number',
        'bank_name',
        'bank_branch',
        'ifsc_code',
        'qr_code',
        'payment_gateway_img',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'APPROVED');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'APPROVED');
    }

    public function scopeUnapproved(Builder $query): Builder
    {
        return $query->where('status', 'UNAPPROVED');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isApproved(): bool
    {
        return $this->status === 'APPROVED';
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors (Optional image paths)
    |--------------------------------------------------------------------------
    */

    public function getQrCodeUrlAttribute(): ?string
    {
        return $this->qr_code
            ? asset('uploads/offline_payment/' . $this->qr_code)
            : null;
    }

    public function getPaymentGatewayImgUrlAttribute(): ?string
    {
        return $this->payment_gateway_img
            ? asset('uploads/offline_payment/' . $this->payment_gateway_img)
            : null;
    }
}