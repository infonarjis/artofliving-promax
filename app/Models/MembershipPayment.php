<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipPayment extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'package_ids',
        'coupon_code',
        'amount',
        'gateway',
        'transaction_id',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(Register::class, 'user_id');
    }

    public function plan()
    {
        return $this->belongsTo(MembershipPlan::class, 'plan_id');
    }
}
