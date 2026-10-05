<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffCommission extends Model
{
    use SoftDeletes;

    protected $table = 'staff_commissions';

    protected $fillable = [
        'staff_id',
        'matri_id',
        'payment_id',
        'plan_id',
        'plan_name',
        'plan_amount',
        'plan_offer_amount',
        'currency',
        'commission_percentage',
        'commssion_amount',
    ];

    protected $casts = [
        'plan_amount'            => 'double',
        'plan_offer_amount'      => 'double',
        'commission_percentage'  => 'double',
        'commssion_amount'       => 'double',
    ];

    protected $dates = [
        'deleted_at',
    ];

    /**
     * Staff relation
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}