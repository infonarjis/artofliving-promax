<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffSalaryDetail extends Model
{
    use SoftDeletes;

    protected $table = 'staff_salary_details';

    protected $fillable = [
        'staff_salary_id',
        'staff_id',
        'month_year',
        'basic_salary',
        'total_earning',
        'total_deduction',
        'total_net_payable_salary',
        'total_days',
        'working_days',
        'total_holi_day',
        'total_present_days',
        'attendance_leaves',
        'total_working_hours',
        'total_attendance_hour',
        'overtime_shortfall_hrs',
        'total_paid_leave',
        'total_sick_leave',
        'total_annual_leave',
        'applicable_paid_leave',
        'applicable_sick_leave',
        'total_applicable_this_month_leaves',
        'applied_paid_leave',
        'applied_sick_leave',
        'total_leaves',
        'remaining_paid_leave',
        'remaining_sick_leave',
        'total_remain_leaves',
        'deductable_paid_leaves',
        'deductable_sick_leaves',
        'total_deductable_leaves',
        'perday_salary',
        'payable_days',
        'net_payable_salary',
        'total_unpaid_leaves',
        'total_unpaid_leaves_amount',
        'status',
    ];

    protected $casts = [
        'staff_salary_id' => 'integer',
        'staff_id' => 'integer',

        'basic_salary' => 'double',
        'total_earning' => 'double',
        'total_deduction' => 'double',
        'total_net_payable_salary' => 'double',

        'total_days' => 'double',
        'working_days' => 'double',
        'total_holi_day' => 'double',
        'total_present_days' => 'double',
        'attendance_leaves' => 'double',

        'total_working_hours' => 'double',
        'total_attendance_hour' => 'double',
        'overtime_shortfall_hrs' => 'double',

        'total_paid_leave' => 'double',
        'total_sick_leave' => 'double',
        'total_annual_leave' => 'double',

        'applicable_paid_leave' => 'double',
        'applicable_sick_leave' => 'double',
        'total_applicable_this_month_leaves' => 'double',

        'applied_paid_leave' => 'double',
        'applied_sick_leave' => 'double',

        'total_leaves' => 'double',

        'remaining_paid_leave' => 'double',
        'remaining_sick_leave' => 'double',
        'total_remain_leaves' => 'double',

        'deductable_paid_leaves' => 'double',
        'deductable_sick_leaves' => 'double',
        'total_deductable_leaves' => 'double',

        'perday_salary' => 'double',
        'payable_days' => 'double',
        'net_payable_salary' => 'double',

        'total_unpaid_leaves' => 'double',
        'total_unpaid_leaves_amount' => 'double',
    ];

    protected $dates = [
        'deleted_at',
    ];

    /**
     * Salary Master relation
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
}
