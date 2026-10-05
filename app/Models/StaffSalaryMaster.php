<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffSalaryMaster extends Model
{
    use SoftDeletes;

    protected $table = 'staff_salary_master';

    protected $fillable = [
        'staff_id',
        'total_days',
        'working_days',
        'payable_days',
        'basic_salary',
        'total_earning',
        'total_deduction',
        'total_net_payable_salary',
        'month_year',
        'salary_pay_date',
        'status',
    ];

    protected $casts = [
        'staff_id'                 => 'integer',
        'total_days'               => 'integer',
        'working_days'             => 'integer',
        'payable_days'             => 'integer',
        'basic_salary'             => 'decimal:2',
        'total_earning'            => 'decimal:2',
        'total_deduction'          => 'decimal:2',
        'total_net_payable_salary' => 'decimal:2',
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
