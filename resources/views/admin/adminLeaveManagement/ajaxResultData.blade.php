@if (isset($resultArr) && count($resultArr) > 0)
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col" class="text-center"><input class="all_check pointer" type="checkbox"></th>
                <th scope="col">Staff Name</th>
                <th scope="col">Leave Type</th>
                <th scope="col">Subject</th>
                <th scope="col">Message</th>
                <th scope="col">Total Leave</th>
                <th scope="col">Leave Start Date</th>
                <th scope="col">Leave End Date</th>
                <th scope="col">Status</th>
                <th scope="col">Created On</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($resultArr as $key => $value)
                <tr class="table_data_val">
                    <th scope="row" class="text-center"><input type="checkbox" class="checkboxId"
                            id="ps<?php echo $value->id; ?>" name="id[]" value="<?php echo $value->id; ?>"></th>
                    <td>{{ _displayNotAvailable($value->staff->username ?? null) }}
                        ({{ _displayNotAvailable($value->staff->staff_prefix ?? null) }})
                    </td>
                    <td>{{ _displayNotAvailable($value->leave_type) }}</td>
                    <td>{{ _displayNotAvailable($value->subject) }}</td>
                    <td>
                        @php
                            $message = _displayNotAvailable($value->message);
                        @endphp
                        @if (strlen(strip_tags($message)) > 30)
                            {{ \Illuminate\Support\Str::limit(strip_tags($message), 30) }}
                            <a href="javascript:void(0)" class="text-primary fw-semibold readMoreLink"
                                data-model-label="Message" data-description="{{ e($message) }}">
                                Read more
                            </a>
                        @else
                            {{ $message }}
                        @endif
                    </td>
                    <td>{{ _displayNotAvailable($value->total_leave) }}</td>
                    <td>{{ _displayDate($value->leave_start_date, 'j F, Y') }}</td>
                    <td>{{ _displayDate($value->leave_end_date, 'j F, Y') }}</td>
                    @if ($value->status == 'APPROVED')
                        <td><span class="badge bg-label-success me-1">{{ $value->status }}</span></td>
                    @endif
                    @if ($value->status == 'REJECTED')
                        <td><span class="badge bg-label-danger me-1">{{ $value->status }}</span></td>
                    @endif
                    @if ($value->status == 'PENDING')
                        <td><span class="badge bg-label-warning me-1">{{ $value->status }}</span></td>
                    @endif
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
