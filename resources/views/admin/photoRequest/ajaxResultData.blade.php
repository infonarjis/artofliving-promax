@if (isset($resultArr) && count($resultArr) > 0)
<table class="table">
    <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
    <thead>
        <tr class="text-nowrap table_header">
            <th scope="col">Sender Matri Id</th>
            <th scope="col">Receiver Matri Id</th>
            <th scope="col">Receiver Response</th>
            <th scope="col">Sent Date</th>
        </tr>
    </thead>
    @foreach ($resultArr as $key =>$value)
    <tbody>
        <tr class="table_data_val">
            <td>
                @if(!blank($value->sender_matri_id))
                    <a target="_blank" href="{{ route('admin.member.viewDetails',$value->sender_member_id) }}">
                        {{ _displayNotAvailable($value->sender_matri_id) }}
                    </a>
                @else
                    N/A
                @endif
            </td>
            <td>
                @if(!blank($value->receiver_matri_id))
                    <a target="_blank" href="{{ route('admin.member.viewDetails',$value->receiver_member_id) }}">
                        {{ _displayNotAvailable($value->receiver_matri_id) }}
                    </a>
                @else
                    N/A
                @endif
            </td>
            <td>{{ _displayNotAvailable($value->receiver_response) }}</td>
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
