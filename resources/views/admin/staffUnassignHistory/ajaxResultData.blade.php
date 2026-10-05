@if (isset($resultArr) && count($resultArr) > 0)
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col" class="text-center"><input class="all_check pointer" type="checkbox"></th>
                <th scope="col">Username</th>
                <th scope="col">Matri Id</th>
                <th scope="col">Unassign By</th>
                <th scope="col">Email</th>
                <th scope="col">Registered On</th>
                <th scope="col">Unassign Date</th>
                <th scope="col">Assign From</th>
                <th scope="col">Currently Assign</th>
            </tr>
            </tr>
        </thead>
        <tbody>
            @foreach ($resultArr as $key => $value)
                <tr class="table_data_val">
                    <th scope="row" class="text-center"><input type="checkbox" class="checkboxId"
                            id="ps<?php echo $value->id; ?>" name="id[]" value="<?php echo $value->id; ?>"></th>
                    <td>{{ _displayNotAvailable($value->register->fullname) }}</td>
                    <td>
                        @if (!blank($value->register->matri_id))
                            <a target="_blank" href="{{ route('admin.member.viewDetails', $value->member_id) }}">
                                {{ _displayNotAvailable($value->register->matri_id) }}
                            </a>
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ _displayNotAvailable($value->assign_by) }}</td>
                    @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                        <td>{{ _getConstant('DISABLE_IN_DEMO_LABEL') }}</td>
                    @else
                        <td>{{ _displayNotAvailable($value->register->email) }}</td>
                    @endif
                    <td>{{ _displayNotAvailable(_displayDate($value->register->created_at, 'j F, Y h:i A')) }}</td>
                    <td>{{ _displayNotAvailable(_displayDate($value->assign_date, 'j F, Y h:i A')) }}</td>
                    <td>{{ _displayNotAvailable($value->staff->username) }}</td>
                    <td>{{ _displayNotAvailable($value->action) }}</td>
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
