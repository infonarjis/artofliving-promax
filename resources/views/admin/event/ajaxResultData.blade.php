@if (isset($resultArr) && count($resultArr) > 0)
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col" class="text-center"><input class="all_check pointer" type="checkbox"></th>
                <th scope="col">Action</th>
                <th scope="col">Event Title</th>
                <th scope="col">Total Event Registered</th>
                <th scope="col">Ticket Sold</th>
                <th scope="col">Event Date</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($resultArr as $key => $value)
                <tr class="table_data_val">
                    <th scope="row" class="text-center">
                        <input type="checkbox" class="checkboxId" id="ps<?php echo $value->id; ?>" name="id[]"
                            value="<?php echo $value->id; ?>">
                    </th>
                    <td>
                        <a href="{{ route('admin.event.editForm', $value->id) }}" class="action-button"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i
                                class="bx bxs-edit"></i></a>
                        <a href="{{ route('admin.event.viewDetails', $value->id) }}" class="action-button"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="View"><i
                                class="bx bx-show"></i></a>
                    </td>
                    <td>{{ _displayNotAvailable($value->title) }}</td>
                    @php
                        $totalEventCount = $value->totalEventCount;
                        $label = $totalEventCount > 1 ? 'Members' : 'Member';
                    @endphp
                    <td><a target="_blank"
                            href="{{ route('admin.eventReport.memberIndex', base64_encode($value->id)) }}">
                            ({{ $totalEventCount }})
                            {{ $label }}</a>
                    </td>
                    <td>
                        {{ $value->calculated_sold_tickets }} / {{ $value->total_tickets ?? 0 }}
                        <br>
                        <small class="text-muted">
                            Remaining:
                            {{ max(0, ($value->total_tickets ?? 0) - $value->calculated_sold_tickets) }}
                        </small>
                    </td>
                    <td>{{ _displayNotAvailable(_displayDate($value->event_date, 'j F, Y h:i A')) }}</td>
                    @if ($value->status == 'APPROVED')
                        <td><span class="badge bg-label-success me-1">{{ $value->status }}</span></td>
                    @endif
                    @if ($value->status == 'UNAPPROVED')
                        <td><span class="badge bg-label-danger me-1">{{ $value->status }}</span></td>
                    @endif
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
