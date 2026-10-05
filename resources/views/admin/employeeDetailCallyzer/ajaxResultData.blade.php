@if (isset($resultArr) && count($resultArr) > 0)
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col">Emp Code</th>
                <th scope="col">Emp Name</th>
                <th scope="col">Emp Mobile Number</th>
                <th scope="col">Registered At</th>
                <th scope="col">Modified At</th>
                <th scope="col">Last Call At</th>
                <th scope="col">Last Sync Request At</th>
                <th scope="col">Device Details</th>
                <th scope="col">App Settings</th>
            </tr>
        </thead>
        @foreach ($resultArr as $key => $value)
            <tbody>
                <tr class="table_data_val">
                    <td>{{ _displayNotAvailable($value['emp_code']) }}</td>
                    <td>{{ _displayNotAvailable($value['emp_name']) }}</td>
                    <td>{{ _displayNotAvailable($value['emp_country_code'] . '-' . $value['emp_number']) }}</td>
                    <td>{{ _displayNotAvailable($value['registered_at']) }}</td>
                    <td>{{ _displayNotAvailable($value['modified_at']) }}</td>
                    <td>{{ _displayNotAvailable($value['last_call_at']) }}</td>
                    <td>{{ _displayNotAvailable($value['last_sync_req_at']) }}</td>
                    <td>
                        @foreach ($value['device_details'] as $detailKey => $detailValue)
                            <span class="">
                                {{ ucwords(str_replace('_', ' ', $detailKey)) }} :
                                {{ _displayNotAvailable($detailValue) }}
                            </span>
                            @if (!$loop->last && $loop->index < 5)
                                <br>
                            @endif
                        @endforeach
                    </td>
                    <td>
                        @foreach ($value['app_settings'] as $detailKey => $detailValue)
                            <span class="">
                                {{ ucwords(str_replace('_', ' ', $detailKey)) }} :
                                {{ _displayNotAvailable($detailValue) }}
                            </span>
                            @if (!$loop->last && $loop->index < 5)
                                <br>
                            @endif
                        @endforeach
                    </td>
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
