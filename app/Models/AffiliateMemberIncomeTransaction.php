<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AffiliateMemberIncomeTransaction extends Model
{
    use SoftDeletes;
    protected $table = 'affiliate_member_income_transaction';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'affiliate_member_id',
        'amount',
        'is_transfered',
        'admin_remark',
        'status'
    ];

    protected $casts = [
        'amount' => 'double',
        'is_transfered' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function affiliateMember()
    {
        return $this->belongsTo(AffiliateMember::class, 'affiliate_member_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeApproved($query)
    {
        return $query->where('status', 'APPROVED');
    }

    public function scopeNotTransferred($query)
    {
        return $query->where('is_transfered', 0);
    }
}