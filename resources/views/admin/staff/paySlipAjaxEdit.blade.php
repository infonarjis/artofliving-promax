@php
    foreach($dataArr as $key => $value) {
        $$key = $value;
    }
@endphp
<div class="panel panel-primary staff-summary">
    <div class="panel-heading">
        <strong><i class="bx bxs-user"></i> Staff Salary & Attendance Summary</strong>
        <a href="{{ route('admin.staff.downloadSalarySlip', $salary_data->id) }}" target="_blank" class="btn btn-primary">
            <i class="bx bxs-download"></i> Download File
        </a>
        <span class="pull-right label label-info" style="font-size: 13px;">{{ _displayDate($salary_month_year, 'F Y') }}</span>
    </div>

    <div class="panel-body">
        <!-- Employee Info -->
        <div class="section-block">
            <h4><i class="bx bxs-briefcase"></i> Employee Profile</h4>
            <div class="row info-row">
                <div class="col-sm-4"><strong>ID:</strong> {{ $staff_data->staff_prefix }}</div>
                <div class="col-sm-4"><strong>Email:</strong> {{ $staff_data->email }}</div>
                <div class="col-sm-4"><strong>Mobile:</strong> {{ $staff_data->mobile }}</div>
            </div>
        </div>

        <!-- Monthly Overview -->
        <div class="section-block">
            <h4><i class="bx bxs-calendar"></i> Monthly Overview</h4>
            <div class="row info-row">
                <div class="col-sm-4"><strong>Total Days:</strong> <span class="text-dark">{{ $staff_salary_details->total_days }}</span></div>
                <div class="col-sm-4"><strong>Working Days:</strong> <span class="text-primary">{{ $staff_salary_details->working_days }}</span></div>
                <div class="col-sm-4"><strong>Public Holidays:</strong> <span class="text-warning">{{ $staff_salary_details->total_holi_day }}</span></div>
                <div class="col-sm-4"><strong>Present Days:</strong> <span class="text-success">{{ $staff_salary_details->total_present_days }}</span></div>
                <div class="col-sm-4"><strong>Leave/Absent (Hrs Basis):</strong> <span class="text-danger">{{ $staff_salary_details->attendance_leaves }}</span></div>
            </div>
        </div>

        <!-- Attendance -->
        <div class="section-block">
            <h4><i class="bx bxs-time"></i> Attendance & Work Hours</h4>
            <div class="row info-row">
                <div class="col-sm-4"><strong>Total Working Hours:</strong> {{ $staff_salary_details->total_working_hours }} hrs</div>
                <div class="col-sm-4"><strong>Actual Attendance Hours:</strong> {{ $staff_salary_details->total_attendance_hour }} hrs</div>
                <div class="col-sm-4">
                    <strong>Overtime / Shortfall:</strong>
                    @if ($staff_salary_details->overtime_shortfall_hrs > 0)
                        <span class="text-danger"><i class="bx bxs-down-arrow"></i> {{ $staff_salary_details->overtime_shortfall_hrs }} hrs short</span>
                    @else
                        <span class="text-success"><i class="bx bxs-up-arrow"></i> {{ $staff_salary_details->overtime_shortfall_hrs }} hrs overtime</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Leave Summary -->
        <div class="section-block">
            <h4><i class="bx bx-check-shield"></i> Leave Summary</h4>

            <div class="row info-row">
                <div class="col-sm-4"><strong>Annual Paid:</strong> {{ $staff_salary_details->total_paid_leave }}</div>
                <div class="col-sm-4"><strong>Annual Sick:</strong> {{ $staff_salary_details->total_sick_leave }}</div>
                <div class="col-sm-4"><strong>Total Annual:</strong> <span class="text-info">{{ $staff_salary_details->total_annual_leave }}</span></div>
            </div>

            <div class="row info-row text-success">
                <div class="col-sm-4"><strong>Applicable Paid:</strong> {{ $staff_salary_details->applicable_paid_leave }}</div>
                <div class="col-sm-4"><strong>Applicable Sick:</strong> {{ $staff_salary_details->applicable_sick_leave }}</div>
                <div class="col-sm-4"><strong>Total Applicable:</strong> {{ $staff_salary_details->total_applicable_this_month_leaves }}</div>
            </div>

            <div class="row info-row">
                <div class="col-sm-4"><strong>Applied Paid:</strong> {{ $staff_salary_details->applied_paid_leave }}</div>
                <div class="col-sm-4"><strong>Applied Sick:</strong> {{ $staff_salary_details->applied_sick_leave }}</div>
                <div class="col-sm-4"><strong>Total Applied:</strong> {{ $staff_salary_details->total_leaves }}</div>
            </div>

            <div class="row info-row">
                <div class="col-sm-4"><strong>Remaining Paid:</strong> {{ $staff_salary_details->remaining_paid_leave }}</div>
                <div class="col-sm-4"><strong>Remaining Sick:</strong> {{ $staff_salary_details->remaining_sick_leave }}</div>
                <div class="col-sm-4"><strong>Total Remaining:</strong> {{ $staff_salary_details->total_remain_leaves }}</div>
            </div>

            <div class="row info-row text-danger">
                <div class="col-sm-4"><strong>Deductible Paid:</strong> {{ $staff_salary_details->deductable_paid_leaves }}</div>
                <div class="col-sm-4"><strong>Deductible Sick:</strong> {{ $staff_salary_details->deductable_sick_leaves }}</div>
                <div class="col-sm-4"><strong>Total Deductible:</strong> {{ $staff_salary_details->total_deductable_leaves }}</div>
            </div>
        </div>

        <!-- Salary -->
        <div class="section-block">
            <h4><i class="bx bx-money"></i> Salary & Payable Summary</h4>
            <div class="row info-row">
                <div class="col-sm-4"><strong>Basic Salary:</strong> {{ _getConstant('payroll.STAFF_SALARY_CURRENCY') }} {{ number_format($staff_salary_details->basic_salary, 2) }}</div>
                <div class="col-sm-4"><strong>Per Day Salary:</strong> {{ _getConstant('payroll.STAFF_SALARY_CURRENCY') }} {{ number_format(round($staff_salary_details->perday_salary), 2) }}</div>
                <div class="col-sm-4"><strong>Payable Days:</strong> {{ $staff_salary_details->payable_days }}</div>
                <div class="col-sm-4"><strong>Commission Earned: {{ _getConstant('payroll.STAFF_SALARY_CURRENCY') }} {{ number_format(round($total_commission_earned), 2) }}</strong></div>
            </div>
            <div class="highlight-box">
                <p style="margin: 5px 0px 5px;"><strong>Net Payable Salary:</strong>
                    <span class="amount">{{ _getConstant('payroll.STAFF_SALARY_CURRENCY') }} {{ number_format(round($staff_salary_details->net_payable_salary), 2) }}</span>
                </p>
            </div>
        </div>

        <div class="alert alert-warning">
            <strong>Note:</strong> {{ $staff_salary_details->total_unpaid_leaves }} unpaid leaves detected this month.<br>
            Salary has been adjusted automatically.
        </div>
    </div>
</div>
<div class="salary-section">
    <div class="row">
        <!-- Left Side -->
        <!-- <form action="<?php //echo base_url('control-panel/staff/update_salary_slip') ?>" method="POST" id="salaryForm"> -->
        <div class="col-md-8">
            <form action="{{ route('admin.staff.updateSalarySlip') }}" method="POST" id="salaryForm">
                @csrf
                <input type="hidden" name="staff_id" id="staff_id" value="{{ $staff_data->id }}">
                <input type="hidden" name="staff_salary_id" id="staff_salary_id" value="{{ $salary_data->id }}">
                <input type="hidden" name="total_unpaid_leaves_amount" value="{{ $staff_salary_details->total_unpaid_leaves_amount }}">
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label>Month & Year</label>
                        <input type="month" id="month_year" class="form-control" name="month_year" value="{{ $salary_data->month_year }}">
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="">Salary Pay Date</label>
                        <input type="date" class="form-control" name="salary_pay_date" value="{{ $salary_data->salary_pay_date }}">
                    </div>
                    <div class="form-group col-sm-4">
                        <label>Select Staff Pay Head</label>
                        <select class="form-control" id="staff_pay_head_id">
                            <option value="">Select Staff Pay Head</option>
                            @foreach ($staff_pay_head_data as $key => $value)
                                <option data-type="{{ $value->pay_head_type }}" value="{{ $value->id }}">{{ $value->title }} ({{ $value->pay_head_type }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="clearfix"></div>
                    <div class="" id="addNewColumn">
                        <div class="form-inline row mb-15">
                            <div class="col-sm-6">
                                <label>Staff Basic Salary</label>
                            </div>
                            <div class="col-sm-3">
                                <input type="number" class="form-control" id="basicSalary" value="{{ $salary_data->basic_salary }}" readonly>
                            </div>
                        </div>
                        <div class="form-inline row mb-15">
                            <div class="col-sm-6">
                                <label class="text-danger">{{ $staff_salary_details->total_unpaid_leaves }} Unpaid Leaves</label>
                            </div>
                            <div class="col-sm-3">
                                <input type="number" class="form-control deductionAmount calculateSalary" value="{{ number_format(round($staff_salary_details->total_unpaid_leaves_amount), 2, '.', '') }}" readonly>
                            </div>
                        </div>
                        @if (!empty($staff_salary_pay_heads))
                            @foreach ($staff_salary_pay_heads as $key => $value)
                                <div class="form-inline row mb-15">
                                    <div class="col-sm-6">
                                        <label class="{{ ($value->pay_head_type == 'Earning') ? 'text-success' : 'text-danger' }}">{{ $value->pay_head_title }} ({{ $value->pay_head_type }})</label>
                                    </div>
                                    <div class="col-sm-3">
                                        <input type="number" class="form-control {{ ($value->pay_head_type == 'Earning') ? 'earningAmount' : 'deductionAmount' }} calculateSalary" name="pay_head_id[{{ $value->pay_head_id }}]" id="pay_heads_{{ $value->pay_head_id }}" value="{{ $value->pay_head_amount }}">
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-sm-12 text-center mt-3" style="margin-top: 10px;">
                        <button class="btn btn-generate" id="salaryBtn"><i class="glyphicon glyphicon-list-alt"></i> Generate Salary Slips</button>
                    </div>
                </div>
            </form>
        </div>
        <!-- Right Side -->
        <div class="col-sm-4">
            <div class="salary-summary-box">
                <p><strong>Staff Basic Salary</strong> <span>: {{ _getConstant('payroll.STAFF_SALARY_CURRENCY') }}<b class="basicSalary"> {{ number_format(round($salary_data->basic_salary), 2) }}</b></span></p>
                <p><strong>Total Earning</strong> <span class="text-success">: {{ _getConstant('payroll.STAFF_SALARY_CURRENCY') }}<b class="totalEarning"> {{ number_format(round($salary_data->total_earning), 2) }}</b></span></p>
                <p><strong>Total Deduction</strong> <span class="text-danger">: {{ _getConstant('payroll.STAFF_SALARY_CURRENCY') }}<b class="totalDeduction"> {{ number_format(round($salary_data->total_deduction), 2) }}</b></span></p>
                <hr>
                <p><strong>Net Payable Amount</strong> <span class="text-success">: {{ _getConstant('payroll.STAFF_SALARY_CURRENCY') }}<b class="netPayable"> {{ number_format(round($salary_data->total_net_payable_salary), 2) }}</b></span></p>
            </div>
        </div>
    </div>
</div>