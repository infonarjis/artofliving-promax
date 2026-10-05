<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffDailyRevenue extends Model
{
    use SoftDeletes;

    protected $table = 'staff_daily_revenue';

    protected $fillable = [
        'staff_id',
        'matri_id',
        'member_id',
        'payment_id',
        'plan_id',
        'plan_name',
        'plan_amount',
        'plan_offer_amount',
    ];

    protected $casts = [
        'plan_amount'       => 'double',
        'plan_offer_amount' => 'double',
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
