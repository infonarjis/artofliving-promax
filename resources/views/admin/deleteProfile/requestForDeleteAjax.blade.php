@if (isset($resultArr) && count($resultArr) > 0)
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col">Ignore Request</th>
                <th scope="col">Delete Profile</th>
                <th scope="col">Matri Id</th>
                <th scope="col">Mobile Number</th>
                <th scope="col">Reason</th>
                <th scope="col">Sent On</th>
            </tr>
        </thead>
        @foreach ($resultArr as $key =>$value)
        <tbody>
            <tr class="table_data_val">
                <td class="text-center">
                    <button type="button" class="btn rounded-pill btn-warning px-1 py-1 fs-normal fs-12" data-id="{{ $value->id }}"
                    data-matriId="{{ $value->member->matri_id }}" data-label="ignore_request" data-value="Yes"
                    id="ignoreDeleteRequest">Ignore Request</button>
                </td>
                <td class="text-center">
                    <button type="button" class="btn rounded-pill btn-danger px-1 py-1 fs-normal fs-12" data-id="{{ $value->id }}"
                    data-matriId="{{ $value->member->matri_id }}" data-label="is_deleted" data-value="Yes"
                    id="deleteProfile">Delete Profile</button>
                </td>
                <td>
                    @if(!blank($value->member->matri_id))
                        <a target="_blank" href="{{ route('admin.member.viewDetails',$value->member->id) }}">
                            {{ _displayNotAvailable($value->member->matri_id) }}
                        </a>
                    @else
                        N/A
                    @endif
                </td>
                <td>
                        {{ _displayNotAvailable($value->member->mobile) }}
                    </td>
                <td>
                    @php
                        $reason = _displayNotAvailable($value->reason);
                    @endphp
                    @if(strlen(strip_tags($reason)) > 30)
                        {{ \Illuminate\Support\Str::limit(strip_tags($reason), 30) }}
                        <a href="javascript:void(0)"
                        class="text-primary fw-semibold readMoreReason"
                        data-reason="{{ e($reason) }}">
                            Read more
                        </a>
                    @else
                        {{ $reason }}
                    @endif
                </td>
                <td>{{ _displayDate($value->sent_on, 'j F, Y h:i A') }}</td>
            </tr>
        </tbody>
        @endforeach
    </table>
    @include('admin.commonPagination')
@else
    <div class="No_dataFound text-center p-4">
        <img src="{{ _assetUrl('upload_path.ADMIN_NO_DATA_FOUND') }}" alt="No Data Found" class="Nodata-img">
    </div>
@endif

