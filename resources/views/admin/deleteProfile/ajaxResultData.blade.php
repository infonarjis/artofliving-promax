@if (isset($resultArr) && count($resultArr) > 0)
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col">Recover Profile</th>
                <th scope="col">Matri Id</th>
                <th scope="col">Mobile No.</th>
                <th scope="col">Reason</th>
                <th scope="col">Deleted On</th>
            </tr>
        </thead>
        @foreach ($resultArr as $key => $value)
            <tbody>
                <tr class="table_data_val">
                    <td class="text-center">
                        <button type="button" class="btn rounded-pill btn-danger px-1 py-1 fs-normal fs-12"
                            data-id="{{ $value->id }}" data-matriId="{{ $value->matri_id }}" data-label="is_deleted"
                            data-value="No" id="recoverProfile">Recover Profile</button>
                    </td>
                    <td>
                        @if (!blank($value->matri_id))
                            <a target="_blank" href="{{ route('admin.member.viewDetails',$value->id) }}">
                                {{ _displayNotAvailable($value->matri_id) }}
                            </a>
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        {{ _displayNotAvailable($value->mobile) }}
                    </td>
                    <td>
                        @php
                            $deleteReq = $value->latestDeleteRequest;
                            $reason = $deleteReq->reason ?? null;
                        @endphp

                        @if ($reason)
                            @if (strlen(strip_tags($reason)) > 30)
                                {{ \Illuminate\Support\Str::limit(strip_tags($reason), 30) }}
                                <a href="javascript:void(0)" class="text-primary fw-semibold readMoreReason"
                                    data-reason="{{ e($reason) }}">
                                    Read more
                                </a>
                            @else
                                {{ $reason }}
                            @endif
                        @else
                            Deleted By Admin Or Staff
                        @endif
                    </td>

                    <td>
                        @if ($deleteReq && $deleteReq->deleted_on)
                            {{ _displayDate($deleteReq->deleted_on, 'j F, Y h:i A') }}
                        @else
                            {{ _displayDate($value->deleted_at, 'j F, Y h:i A') }}
                        @endif
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
