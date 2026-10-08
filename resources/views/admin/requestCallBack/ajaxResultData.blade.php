@if (isset($resultArr) && count($resultArr) > 0)
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col">Matri Id</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Mobile</th>
                <th scope="col">Created At</th>
            </tr>
        </thead>
        @foreach ($resultArr as $key => $value)
            <tbody>
                <tr class="table_data_val">
                    <td>
                        @if (!blank($value->member_id))
                            <a target="_blank" href="{{ route('admin.member.viewDetails', $value->member_id) }}">
                                {{ _displayNotAvailable($value->matri_id) }}
                            </a>
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ _displayNotAvailable($value->name) }}</td>
                    <td>{{ _displayNotAvailable($value->email) }}</td>
                    <td>{{ _displayNotAvailable($value->mobile) }}</td>
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
