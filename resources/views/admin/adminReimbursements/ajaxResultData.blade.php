@if (isset($resultArr) && count($resultArr) > 0)
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col" class="text-center"><input class="all_check pointer" type="checkbox"></th>
                <th scope="col">Staff Name</th>
                <th scope="col">Title</th>
                <th scope="col">Description</th>
                <th scope="col">Receipt</th>
                <th scope="col">Status</th>
                <th scope="col">Created On</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resultArr as $key => $value)
            <tr class="table_data_val">
                <th scope="row" class="text-center"><input type="checkbox" class="checkboxId" id="ps<?php echo $value->id; ?>" name="id[]"
                        value="<?php echo $value->id; ?>"></th>
                <td>{{ _displayNotAvailable($value->staff->username ?? null) }} ({{ _displayNotAvailable($value->staff->staff_prefix ?? null) }})</td>
                <td>{{ _displayNotAvailable($value->title) }}</td>
                <td>
                    @php
                        $description = _displayNotAvailable($value->description);
                    @endphp
                    @if(strlen(strip_tags($description)) > 30)
                        {{ \Illuminate\Support\Str::limit(strip_tags($description), 30) }}
                        <a href="javascript:void(0)"
                        class="text-primary fw-semibold readMoreLink"
                        data-model-label="Description"
                        data-description="{{ e($description) }}">
                            Read more
                        </a>
                    @else
                        {{ $description }}
                    @endif
                </td>
                <td>
                    @if ($value->receipt)
                        <a href="{{ _assetUrl('upload_path.REIMBURSEMENTS_RECEIPT_URL').$value->receipt }}" target="_blank" class="btn btn-sm btn-primary">View Receipt</a>
                    @else
                        {{ _displayNotAvailable(null) }}
                    @endif
                </td>
                @if ($value->status == 'APPROVED')
                <td><span class="badge bg-label-success me-1">{{ $value->status }}</span></td>
                @endif
                @if ($value->status == 'UNAPPROVED')
                <td><span class="badge bg-label-danger me-1">{{ $value->status }}</span></td>
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
