@if (isset($resultArr) && count($resultArr) > 0)
<table class="table">
    <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
    <thead>
        <tr class="text-nowrap table_header">
            <th scope="col">Client Name</th>
            <th scope="col">Client Number</th>
            <th scope="col">Duration</th>
            <th scope="col">Call Type</th>
            <th scope="col">Call Date</th>
            <th scope="col">Call Time</th>
            <th scope="col">Note</th>
            <th scope="col">Recording</th>
            <th scope="col">Synced At</th>
            <th scope="col">Emp Name</th>
            <th scope="col">Emp Code</th>
            <th scope="col">Emp Number</th>
        </tr>
    </thead>
    @foreach ($resultArr as $key =>$value)
    <tbody>
        <tr class="table_data_val">
            <td>{{ _displayNotAvailable($value['client_name']) }}</td>
            <td>{{ _displayNotAvailable($value['client_country_code'].'-'.$value['client_number']) }}</td>
            <td>{{ _displayNotAvailable($value['duration']) }}</td>
            <td>{{ _displayNotAvailable($value['call_type']) }}</td>
            <td>{{ _displayNotAvailable($value['call_date']) }}</td>
            <td>{{ _displayNotAvailable($value['call_time']) }}</td>
            <td>{{ _displayNotAvailable($value['note']) }}</td>
            <td>
                @if(!blank($value['call_recording_url']))
                    <a href="{{ _displayNotAvailable($value['call_recording_url']) }}"
                        class="badge background-theme text-white"
                        download>
                            Download
                        </a>
                @else
                N/A
                @endif
            </td>
            <td>{{ _displayNotAvailable($value['synced_at']) }}</td>
            <td>{{ _displayNotAvailable($value['emp_name']) }}</td>
            <td>{{ _displayNotAvailable($value['emp_code']) }}</td>
            <td>{{ _displayNotAvailable($value['emp_number']) }}</td>
        </tr>
    </tbody>
    @endforeach
</table>
@else
    <div class="No_dataFound text-center p-4">
        <img src="{{ _assetUrl('upload_path.ADMIN_NO_DATA_FOUND') }}" alt="No Data Found" class="Nodata-img">
    </div>
@endif
@include('admin.commonPagination')
