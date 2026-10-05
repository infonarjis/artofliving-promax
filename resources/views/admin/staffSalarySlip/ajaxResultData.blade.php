@if (isset($resultArr) && count($resultArr) > 0)
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col">Download</th>
                <th scope="col">Staff Name</th>
                <th scope="col">Payable Days</th>
                <th scope="col">Basic Salary</th>
                <th scope="col">Earnings</th>
                <th scope="col">Deductions</th>
                <th scope="col">Net Salary</th>
                <th scope="col">Salary Month</th>
                <th scope="col">Salary pay Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resultArr as $key => $value)
            <tr class="table_data_val">
                <td>
                    <a href="{{ route('admin.staff.downloadSalarySlip', $value->id) }}" target="_blank" class="btn btn-primary">
                        <i class="bx bxs-download"></i> Download
                    </a>
                </td>
                <td>{{ _displayNotAvailable($value->staff->username ?? null) }} ({{ _displayNotAvailable($value->staff->staff_prefix ?? null) }})</td>
                <td>{{ _displayNotAvailable($value->payable_days) }}</td>
                <td>{{ _displayNotAvailable($value->basic_salary) }}</td>
                <td>{{ _displayNotAvailable($value->total_earning) }}</td>
                <td>{{ _displayNotAvailable($value->total_deduction) }}</td>
                <td>{{ _displayNotAvailable($value->total_net_payable_salary) }}</td>
                <td>{{ _displayDate($value->month_year, 'F, Y') }}</td>
                <td>{{ _displayDate($value->salary_pay_date, 'j F, Y h:i A') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div class="No_dataFound text-center p-4">
        <img src="{{ _assetUrl('upload_path.ADMIN_NO_DATA_FOUND') }}" alt="No Data Found" class="Nodata-img">
    </div>
@endif
@include('admin.commonPagination')
