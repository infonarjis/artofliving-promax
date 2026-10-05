@if (isset($resultArr) && !empty($resultArr))
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col">Emp Code</th>
                <th scope="col">Emp Name</th>
                <th scope="col">Emp Mobile Number</th>
                <th scope="col">Total Incoming Calls</th>
                <th scope="col">Total Incoming Duration</th>
                <th scope="col">Total Incoming Connected Calls</th>
                <th scope="col">Total Outgoing Calls</th>
                <th scope="col">Total Outgoing Duration</th>
                <th scope="col">Total Outgoing Connected Calls</th>
                <th scope="col">Total Missed Calls</th>
                <th scope="col">Total Rejected Calls</th>
                <th scope="col">Total Calls</th>
                <th scope="col">Total Duration</th>
                <th scope="col">Total Connected Calls</th>
            </tr>
        </thead>
        @foreach ($resultArr as $key => $value)
            <tbody>
                <tr class="table_data_val">
                    <td>{{ _displayNotAvailable($value['emp_code']) }}</td>
                    <td>{{ _displayNotAvailable($value['emp_name']) }}</td>
                    <td>{{ _displayNotAvailable($value['emp_country_code'] . '-' . $value['emp_number']) }}</td>
                    <td>{{ _displayNotAvailable($value['total_incoming_calls']) }}</td>
                    <td>{{ _displayNotAvailable($value['total_incoming_duration']) }}</td>
                    <td>{{ _displayNotAvailable($value['total_incoming_connected_calls']) }}</td>
                    <td>{{ _displayNotAvailable($value['total_outgoing_calls']) }}</td>
                    <td>{{ _displayNotAvailable($value['total_outgoing_duration']) }}</td>
                    <td>{{ _displayNotAvailable($value['total_outgoing_connected_calls']) }}</td>
                    <td>{{ _displayNotAvailable($value['total_missed_calls']) }}</td>
                    <td>{{ _displayNotAvailable($value['total_rejected_calls']) }}</td>
                    <td>{{ _displayNotAvailable($value['total_calls']) }}</td>
                    <td>{{ _displayNotAvailable($value['total_duration']) }}</td>
                    <td>{{ _displayNotAvailable($value['total_connected_calls']) }}</td>
                </tr>
            </tbody>
        @endforeach
    </table>
@else
    <div class="No_dataFound text-center p-4">
        <img src="{{ _assetUrl('upload_path.ADMIN_NO_DATA_FOUND') }}" alt="No Data Found" class="Nodata-img">
    </div>
@endif
