@if (isset($resultArr) && count($resultArr) > 0)
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col">Staff</th>
                <th scope="col">Punch In</th>
                <th scope="col">Punch In Remarks</th>
                <th scope="col">Punch Out</th>
                <th scope="col">Punch Out Remarks</th>
                <th scope="col">Attendance Status</th>
                <th scope="col">Ip Address</th>
                <th scope="col">Total Working Hrs</th>
                <th scope="col">Created On</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resultArr as $key => $value)
            <tr class="table_data_val">
                <td>{{ _displayNotAvailable($value->staff->username ?? null) }} ({{ _displayNotAvailable($value->staff->staff_prefix ?? null) }})</td>
                <td>{{ _displayNotAvailable($value->punch_in) }}</td>
                <td>{{ _displayNotAvailable($value->punch_in_remarks) }}</td>
                <td>{{ _displayNotAvailable($value->punch_out) }}</td>
                <td>{{ _displayNotAvailable($value->punch_out_remarks) }}</td>
                <td>{{ _displayNotAvailable($value->attendance_status) }}</td>
                <td>{{ _displayNotAvailable($value->ip_address) }}</td>
                <td>
                    @if($value->punch_in && $value->punch_out)
                        {{ _calculateTimeDifference($value->punch_in, $value->punch_out) }}
                    @else
                        {{ _displayNotAvailable(null) }}
                    @endif
                </td>
                <td>{{ _displayDate($value->created_at, 'j F, Y h:i A') }}</td>
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
