@if (isset($resultArr) && count($resultArr) > 0)
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col">Email</th>
                <th scope="col">Created On</th>
            </tr>
        </thead>
        @foreach ($resultArr as $key =>$value)
        <tbody>
            <tr class="table_data_val">
                @if(_getConstant('DISABLE_DEMO') ==  'Enabled')
                    <td>{{ _getConstant('DISABLE_IN_DEMO_LABEL') }}</td>
                @else
                    <td>{{ _displayNotAvailable($value->email) }}</td>
                @endif
                <td>{{ _displayDate($value->created_at, 'j F, Y h:i A') }}</td>
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
