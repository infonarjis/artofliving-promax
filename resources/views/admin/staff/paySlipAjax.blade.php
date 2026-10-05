@php
    foreach ($dataArr as $key => $value) {
        $$key = $value;
    }
    if ($remaining_sick_leave <= 0) {
        $staff_data->monthly_applied_sick_leave = 0;
    }
    if ($remaining_paid_leave <= 0) {
        $staff_data->monthly_applied_paid_leave = 0;
    }
    $deductable_paid_leaves = max(0, $paid_leave->total_leave_days - $staff_data->monthly_applied_paid_leave);
    $deductable_sick_leaves = max(0, $sick_leave->total_leave_days - $staff_data->monthly_applied_sick_leave);
    $total_deductable_leaves = $deductable_paid_leaves + $deductable_sick_leaves;
    $total_applicable_leaves = $staff_data->monthly_applied_paid_leave + $staff_data->monthly_applied_sick_leave;
    $total_leaves = $paid_leave->total_leave_days + $sick_leave->total_leave_days;
    $total_remain_leaves = $remaining_paid_leave + $remaining_sick_leave;
    $total_present_days = $total_working_days - $total_leaves;
    $perday_salary = $staff_data->basic_salary / $total_working_days;
    $total_working_hours = $total_present_days * _getConstant('payroll.STAFF_WORKING_HOUR');
    $total_attendance_hour = $total_hours;
    $overtime_shortfall_hrs = $total_working_hours - $total_attendance_hour;
    $attendance_leaves = round(
        ($total_working_hours - $total_attendance_hour) / _getConstant('payroll.STAFF_WORKING_HOUR'),
    );
    if ($attendance_leaves < 0) {
        $attendance_leaves = 0;
    }
    $total_applicable_this_month_leaves =
        $staff_data->monthly_applied_sick_leave + $staff_data->monthly_applied_paid_leave;
    $paid_adjusted = $staff_data->monthly_applied_paid_leave;
    $sick_adjusted = $staff_data->monthly_applied_sick_leave;
    if ($paid_leave->total_leave_days < $staff_data->monthly_applied_paid_leave) {
        $paid_adjusted = $paid_leave->total_leave_days;
    }
    if ($sick_leave->total_leave_days < $staff_data->monthly_applied_sick_leave) {
        $sick_adjusted = $sick_leave->total_leave_days;
    }
    $paid_leave_adjusted = $paid_adjusted + $sick_adjusted;

    $payable_days = $total_present_days + $paid_leave_adjusted - $attendance_leaves;
    $net_payable_salary = $perday_salary * $payable_days;
    $total_unpaid_leaves = $total_deductable_leaves + $attendance_leaves;

    ## Salary Calculation :
    $basic_salary = $staff_data->basic_salary;
    $total_earning = 0;
    $total_deduction = 0;
    $deduction_amount = 0;
    if ($net_payable_salary < $basic_salary) {
        $deduction_amount = $basic_salary - $net_payable_salary;
        $total_deduction = $deduction_amount;
    }
    $net_salary = $basic_salary + $total_earning - $total_deduction;
@endphp
<div class="panel panel-primary staff-summary">
    <div class="panel-heading">
        <strong><i class="bx bxs-user"></i> Staff Salary & Attendance Summary</strong>
        <span class="pull-right label label-info"
            style="font-size: 13px;">{{ _displayDate($salary_month_year, 'F Y') }}</span>
    </div>

    <div class="panel-body">
        <!-- Employee Info -->
        <div class="section-block">
            <h4><i class="bx bxs-briefcase"></i> Employee Profile</h4>
            <div class="row info-row">
                <div class="col-sm-4"><strong>ID :</strong> {{ $staff_data->staff_prefix }}</div>
                <div class="col-sm-4"><strong>Email :</strong> {{ $staff_data->email }}</div>
                <div class="col-sm-4"><strong>Mobile :</strong> {{ $staff_data->mobile }}</div>
            </div>
        </div>

        <!-- Monthly Overview -->
        <div class="section-block">
            <h4><i class="bx bxs-calendar"></i> Monthly Overview</h4>
            <div class="row info-row">
                <div class="col-sm-4"><strong>Total Days :</strong> <span class="text-dark">{{ $total_days }}</span>
                </div>
                <div class="col-sm-4"><strong>Working Days :</strong> <span
                        class="text-primary">{{ $total_working_days }}</span></div>
                <div class="col-sm-4"><strong>Public Holidays :</strong> <span
                        class="text-warning">{{ $total_holi_day }}</span></div>
                <div class="col-sm-4"><strong>Present Days :</strong> <span
                        class="text-success">{{ $total_present_days }}</span></div>
                <div class="col-sm-4"><strong>Leave/Absent (Hrs Basis) :</strong> <span
                        class="text-danger">{{ $attendance_leaves }}</span></div>
            </div>
        </div>

        <!-- Attendance -->
        <div class="section-block">
            <h4><i class="bx bxs-time"></i> Attendance & Work Hours</h4>
            <div class="row info-row">
                <div class="col-sm-4"><strong>Total Working Hours :</strong> {{ $total_working_hours }} hrs</div>
                <div class="col-sm-4"><strong>Actual Attendance Hours :</strong> {{ $total_attendance_hour }} hrs</div>
                <div class="col-sm-4">
                    <strong>Overtime / Shortfall :</strong>
                    @if ($overtime_shortfall_hrs > 0)
                        <span class="text-danger"><i class="bx bxs-down-arrow"></i> {{ $overtime_shortfall_hrs }} hrs
                            short</span>
                    @else
                        <span class="text-success"><i class="bx bxs-up-arrow"></i> {{ abs($overtime_shortfall_hrs) }}
                            hrs overtime</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Leave Summary -->
        <div class="section-block">
            <h4><i class="bx bx-check-shield"></i> Leave Summary</h4>

            <div class="row info-row">
                <div class="col-sm-4"><strong>Annual Paid :</strong> {{ $staff_data->total_paid_leave }}</div>
                <div class="col-sm-4"><strong>Annual Sick :</strong> {{ $staff_data->total_sick_leave }}</div>
                <div class="col-sm-4"><strong>Total Annual :</strong> <span
                        class="text-info">{{ $staff_data->total_paid_leave + $staff_data->total_sick_leave }}</span>
                </div>
            </div>

            <div class="row info-row text-success">
                <div class="col-sm-4"><strong>Applicable Paid :</strong> {{ $staff_data->monthly_applied_paid_leave }}
                </div>
                <div class="col-sm-4"><strong>Applicable Sick :</strong> {{ $staff_data->monthly_applied_sick_leave }}
                </div>
                <div class="col-sm-4"><strong>Total Applicable :</strong> {{ $total_applicable_this_month_leaves }}
                </div>
            </div>

            <div class="row info-row">
                <div class="col-sm-4"><strong>Applied Paid :</strong> {{ $paid_leave->total_leave_days }}</div>
                <div class="col-sm-4"><strong>Applied Sick :</strong> {{ $sick_leave->total_leave_days }}</div>
                <div class="col-sm-4"><strong>Total Applied :</strong> {{ $total_leaves }}</div>
            </div>

            <div class="row info-row">
                <div class="col-sm-4"><strong>Remaining Paid :</strong> {{ $remaining_paid_leave }}</div>
                <div class="col-sm-4"><strong>Remaining Sick :</strong> {{ $remaining_sick_leave }}</div>
                <div class="col-sm-4"><strong>Total Remaining :</strong> {{ $total_remain_leaves }}</div>
            </div>

            <div class="row info-row text-danger">
                <div class="col-sm-4"><strong>Deductible Paid :</strong> {{ $deductable_paid_leaves }}</div>
                <div class="col-sm-4"><strong>Deductible Sick :</strong> {{ $deductable_sick_leaves }}</div>
                <div class="col-sm-4"><strong>Total Deductible :</strong> {{ $total_deductable_leaves }}</div>
            </div>
        </div>

        <!-- Salary -->
        <div class="section-block">
            <h4><i class="bx bx-money"></i> Salary & Payable Summary</h4>
            <div class="row info-row">
                <div class="col-sm-4"><strong>Basic Salary :</strong>
                    {{ _getConstant('payroll.STAFF_SALARY_CURRENCY') }}
                    {{ number_format($staff_data->basic_salary, 2) }}</div>
                <div class="col-sm-4"><strong>Per Day Salary :</strong>
                    {{ _getConstant('payroll.STAFF_SALARY_CURRENCY') }} {{ number_format(round($perday_salary), 2) }}
                </div>
                <div class="col-sm-4"><strong>Payable Days :</strong> {{ $payable_days }}</div>
                <div class="col-sm-4"><strong>Commission Earned : {{ _getConstant('payroll.STAFF_SALARY_CURRENCY') }}
                        {{ number_format(round($total_commission_earned), 2) }}</strong></div>
            </div>
            <div class="highlight-box">
                <p style="margin: 5px 0px 5px;"><strong>Net Payable Salary :</strong>
                    <span class="amount">{{ _getConstant('payroll.STAFF_SALARY_CURRENCY') }}
                        {{ number_format(round($net_payable_salary), 2) }}</span>
                </p>
            </div>
        </div>

        <div class="alert alert-warning mb-0">
            <strong>Note :</strong> {{ $total_unpaid_leaves }} unpaid leaves detected this month.<br>
            Salary has been adjusted automatically.
        </div>
    </div>
</div>
<div class="salary-section">
    <div class="row">
        <!-- Left Side -->
        <div class="col-md-8">
            <form action="{{ route('admin.staff.saveSalarySlip') }}" method="POST" id="salaryForm">
                @csrf
                <input type="hidden" name="staff_id" id="staff_id" value="{{ $staff_data->id }}">
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label>Month & Year</label>
                        <input type="month" id="month_year" class="form-control" name="month_year"
                            value="{{ $salary_month_year }}">
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="">Salary Pay Date</label>
                        <input type="date" class="form-control" name="salary_pay_date" value="">
                    </div>
                    <div class="form-group col-sm-4">
                        <label>Select Staff Pay Head</label>
                        <select class="form-control" id="staff_pay_head_id">
                            <option value="">Select Staff Pay Head</option>
                            @foreach ($staff_pay_head_data as $key => $value)
                                <option data-type="{{ $value->pay_head_type }}" value="{{ $value->id }}">
                                    {{ $value->title }} ({{ $value->pay_head_type }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mt-3"></div>
                    <div class="" id="addNewColumn">
                        <div class="form-inline row mb-15">
                            <div class="col-sm-6">
                                <label>Staff Basic Salary</label>
                            </div>
                            <div class="col-sm-3">
                                <input type="number" class="form-control" id="basicSalary"
                                    value="{{ $staff_data->basic_salary }}" readonly>
                            </div>
                        </div>
                        <div class="form-inline row mb-15">
                            <div class="col-sm-6">
                                <label class="text-danger">{{ $total_unpaid_leaves }} Unpaid Leaves</label>
                            </div>
                            <div class="col-sm-3">
                                <input type="number" class="form-control deductionAmount calculateSalary"
                                    value="{{ number_format(round($deduction_amount), 2, '.', '') }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <input type="hidden" name="basic_salary" value="{{ $staff_data->basic_salary }}">
                    <input type="hidden" name="total_days" value="{{ $total_days }}">
                    <input type="hidden" name="working_days" value="{{ $total_working_days }}">
                    <input type="hidden" name="total_holi_day" value="{{ $total_holi_day }}">
                    <input type="hidden" name="total_present_days" value="{{ $total_present_days }}">
                    <input type="hidden" name="attendance_leaves" value="{{ $attendance_leaves }}">
                    <input type="hidden" name="total_working_hours" value="{{ $total_working_hours }}">
                    <input type="hidden" name="total_attendance_hour" value="{{ $total_attendance_hour }}">
                    <input type="hidden" name="overtime_shortfall_hrs" value="{{ $overtime_shortfall_hrs }}">
                    <input type="hidden" name="total_paid_leave" value="{{ $staff_data->total_paid_leave }}">
                    <input type="hidden" name="total_sick_leave" value="{{ $staff_data->total_sick_leave }}">
                    <input type="hidden" name="total_annual_leave"
                        value="{{ $staff_data->total_paid_leave + $staff_data->total_sick_leave }}">
                    <input type="hidden" name="applicable_paid_leave"
                        value="{{ $staff_data->monthly_applied_paid_leave }}">
                    <input type="hidden" name="applicable_sick_leave"
                        value="{{ $staff_data->monthly_applied_sick_leave }}">
                    <input type="hidden" name="total_applicable_this_month_leaves"
                        value="{{ $total_applicable_this_month_leaves }}">
                    <input type="hidden" name="applied_paid_leave" value="{{ $paid_leave->total_leave_days }}">
                    <input type="hidden" name="applied_sick_leave" value="{{ $sick_leave->total_leave_days }}">
                    <input type="hidden" name="total_leaves" value="{{ $total_leaves }}">
                    <input type="hidden" name="remaining_paid_leave" value="{{ $remaining_paid_leave }}">
                    <input type="hidden" name="remaining_sick_leave" value="{{ $remaining_sick_leave }}">
                    <input type="hidden" name="total_remain_leaves" value="{{ $total_remain_leaves }}">
                    <input type="hidden" name="deductable_paid_leaves" value="{{ $deductable_paid_leaves }}">
                    <input type="hidden" name="deductable_sick_leaves" value="{{ $deductable_sick_leaves }}">
                    <input type="hidden" name="total_deductable_leaves" value="{{ $total_deductable_leaves }}">
                    <input type="hidden" name="perday_salary" value="{{ $perday_salary }}">
                    <input type="hidden" name="payable_days" value="{{ $payable_days }}">
                    <input type="hidden" name="net_payable_salary" value="{{ $net_payable_salary }}">
                    <input type="hidden" name="total_unpaid_leaves" value="{{ $total_unpaid_leaves }}">
                    <input type="hidden" name="total_unpaid_leaves_amount" value="{{ $deduction_amount }}">
                    <div class="form-group col-sm-12 text-center mt-3" style="margin-top: 10px;">
                        <button class="btn btn-generate" id="salaryBtn"><i class="glyphicon glyphicon-list-alt"></i>
                            Generate Salary Slips</button>
                    </div>
                </div>
            </form>
        </div>
        <!-- Right Side -->
        <div class="col-sm-4">
            <div class="salary-summary-box">
                <p><strong>Staff Basic Salary </strong> <span>: {{ _getConstant('payroll.STAFF_SALARY_CURRENCY') }}<b
                            class="basicSalary"> {{ number_format(round($staff_data->basic_salary)) }}</b></span></p>
                <p><strong>Total Earning </strong> <span class="text-success">:
                        {{ _getConstant('payroll.STAFF_SALARY_CURRENCY') }}<b class="totalEarning">
                            {{ number_format(round($total_earning), 2) }}</b></span></p>
                <p><strong>Total Deduction </strong> <span class="text-danger">:
                        {{ _getConstant('payroll.STAFF_SALARY_CURRENCY') }}<b class="totalDeduction">
                            {{ number_format(round($total_deduction), 2) }}</b></span></p>
                <hr>
                <p><strong>Net Payable Amount </strong> <span class="text-success">:
                        {{ _getConstant('payroll.STAFF_SALARY_CURRENCY') }}<b class="netPayable">
                            {{ number_format(round($net_salary), 2) }}</b></span></p>
            </div>
        </div>
    </div>
</div>
