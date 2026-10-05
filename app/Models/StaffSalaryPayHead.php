<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffSalaryPayHead extends Model
{
    use SoftDeletes;

    protected $table = 'staff_salary_pay_heads';

    protected $fillable = [
        'staff_salary_id',
        'staff_id',
        'pay_head_id',
        'pay_head_title',
        'pay_head_type',
        'pay_head_amount',
        'month_year',
        'status',
    ];

    protected $casts = [
        'staff_salary_id' => 'integer',
        'staff_id'        => 'integer',
        'pay_head_id'     => 'integer',
        'pay_head_amount' => 'double',
    ];

    protected $dates = [
        'deleted_at',
    ];

    /**
     * Salary relation
     */
    public function staffSalary()
    {
        return $this->belongsTo(
            StaffSalaryMaster::class,
            'staff_salary_id'
        );
    }

    /**
     * Staff relation
     */
    public function staff()
    {
        return $this->belongsTo(
            Staff::class,
            'staff_id'
        );
    }

    /**
     * Pay Head relation
     */
    public function payHead()
    {
        return $this->belongsTo(
            StaffPayHeadMaster::class,
            'pay_head_id'
        );
    }
}
