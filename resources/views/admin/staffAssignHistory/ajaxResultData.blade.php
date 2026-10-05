@if (isset($resultArr) && count($resultArr) > 0)
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col">Matri Id</th>
                <th scope="col">User Name</th>
                <th scope="col">Email</th>
                <th scope="col">Assign To</th>
                <th scope="col">Assign Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($resultArr as $key => $value)
                <tr class="table_data_val">
                    <td>{{ _displayNotAvailable($value->register->matri_id) }}</td>
                    <td>{{ _displayNotAvailable($value->register->fullname) }}</td>
                    @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                        <td>{{ _getConstant('DISABLE_IN_DEMO_LABEL') }}</td>
                    @else
                        <td>{{ _displayNotAvailable($value->register->email) }}</td>
                    @endif
                    <td>{{ _displayNotAvailable($value->staff->username) }}</td>
                    <td>{{ _displayDate($value->assign_date, 'j F, Y h:i A') }}</td>
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
