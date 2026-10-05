@if (isset($resultArr) && count($resultArr) > 0)
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col" class="text-center"><input class="all_check pointer" type="checkbox"></th>
                <th scope="col">Franchise Name</th>
                <th scope="col">Franchise Email</th>
                <th scope="col">Login At</th>
                <th scope="col">Login Out</th>
                <th scope="col">Ip Address</th>
            </tr>
        </thead>
        @foreach ($resultArr as $key => $value)
            <tbody>
                <tr class="table_data_val">
                    <th scope="row" class="text-center"><input type="checkbox" class="checkboxId"
                            id="ps<?php echo $value->id; ?>" name="id[]" value="<?php echo $value->id; ?>"></th>
                    <td>{{ _displayNotAvailable($value->franchise->username) }}</td>
                    @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                        <td>{{ _getConstant('DISABLE_IN_DEMO_LABEL') }}</td>
                    @else
                        <td>{{ _displayNotAvailable($value->franchise->email) }}</td>
                    @endif
                    <td>{{ _displayDate($value->login_at, 'j F, Y h:i A') }}</td>
                    <td>{{ _displayDate($value->logout_at, 'j F, Y h:i A') }}</td>
                    <td>{{ _displayNotAvailable($value->ip_address) }}</td>
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
