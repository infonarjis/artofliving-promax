@if (isset($resultArr) && count($resultArr) > 0)
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col">Reported Profile</th>
                <th scope="col">Report By</th>
                <th scope="col">Reason Type</th>
                <th scope="col">Reason</th>
                <th scope="col">Created On</th>
            </tr>
        </thead>
        @foreach ($resultArr as $key => $value)
            <tbody>
                <tr class="table_data_val">
                    <td>
                        @if (!blank($value->report_matri_id))
                            <a target="_blank" href="{{ route('admin.member.viewDetails', $value->report_id) }}">
                                {{ _displayNotAvailable($value->report_matri_id) }}
                            </a>
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if (!blank($value->report_by_matri_id))
                            <a target="_blank" href="{{ route('admin.member.viewDetails', $value->report_by) }}">
                                {{ _displayNotAvailable($value->report_by_matri_id) }}
                            </a>
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ _displayNotAvailable(_getStaticArr('reportTypes', $value->report_type)) }}</td>
                    <td>
                        @php
                            $reason = _displayNotAvailable($value->reason);
                        @endphp
                        @if (strlen(strip_tags($reason)) > 30)
                            {{ \Illuminate\Support\Str::limit(strip_tags($reason), 30) }}
                            <a href="javascript:void(0)" class="text-primary fw-semibold readMoreLink"
                                data-model-label="Reason"
                                data-description="{{ e($reason) }}">
                                Read more
                            </a>
                        @else
                            {{ $reason }}
                        @endif
                    </td>

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
