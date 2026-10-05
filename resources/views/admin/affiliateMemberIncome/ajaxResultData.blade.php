@if (isset($resultArr) && count($resultArr) > 0)
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col">Matri id</th>
                <th scope="col">Affiliate Member</th>
                <th scope="col">Affiliate Email</th>
                <th scope="col">Affiliate Mobile</th>
                <th scope="col">Income type</th>
                <th scope="col">Amount</th>
                <th scope="col">Created at</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resultArr as $key => $value)
            <tr class="table_data_val">
                <td>
                    @if(!blank($value->member->matri_id))
                        <a target="_blank" href="{{ route('admin.member.viewDetails',$value->member_id) }}">                       
                            {{ _displayNotAvailable($value->member->matri_id) }}
                        </a>
                    @else
                        N/A
                    @endif
                </td>
                <td>{{ _displayNotAvailable($value->member->fullname) }}</td>
                @if(_getConstant('DISABLE_DEMO') ==  'Enabled')
                    <td>{{ _getConstant('DISABLE_IN_DEMO_LABEL') }}</td>
                @else
                    <td>{{ _displayNotAvailable($value->member->email) }}</td>
                @endif
                @if(_getConstant('DISABLE_DEMO') ==  'Enabled')
                    <td>{{ _getConstant('DISABLE_IN_DEMO_LABEL') }}</td>
                @else
                    <td>{{ _displayNotAvailable($value->member->mobile) }}</td>
                @endif
                <td>{{ _displayNotAvailable($value->income_type) }}</td>
                <td>{{ _displayNotAvailable($value->amount) }}</td>
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
