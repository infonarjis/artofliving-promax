@if (isset($resultArr) && count($resultArr) > 0)
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col" class="text-center"><input class="all_check pointer" type="checkbox"></th>
                <th scope="col">Reply</th>
                <th scope="col">View</th>
                <th scope="col">Status</th>
                <th scope="col">Ticket Number</th>
                <th scope="col">Subject</th>
                <th scope="col">Priority</th>
                <th scope="col">Created On</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resultArr as $key => $value)
            <tr class="table_data_val">
                <th scope="row" class="text-center"><input type="checkbox" class="checkboxId" id="ps<?php echo $value->id; ?>" name="id[]" value="<?php echo $value->id; ?>"></th>
                <td>
                    <button type="button" class="btn btn-outline-primary dropdown-toggle px-1 py-1 fs-normal fs-12"
                        data-bs-toggle="dropdown" aria-expanded="false">Reply
                    </button>
                    <ul class="dropdown-menu EmailsetTextd">
                        <li><a class="dropdown-item addComment" href="javascript:void(0)"
                                data-id="{{ $value->id }}"
                                data-action="{{ route($dataArr->actionButtonUrl['addComment']) }}"
                                target-modal="#add_commentModal">Add Reply
                            </a>
                        </li>        
                        <li>
                            <a class="dropdown-item viewComment" href="javascript:void(0)"
                                data-id="{{ $value->id }}"
                                data-action="{{ route($dataArr->actionButtonUrl['viewComment']) }}"
                                target-modal="#view_commentModal">View Reply
                            </a>
                        </li>
                    </ul>
                </td>
                <td>
                    <a href="{{route($dataArr->actionButtonUrl['view'],$value->id) }}" class="action-button" data-bs-toggle="tooltip"  data-bs-placement="top" title="View"><i class="bx bx-show"></i></a>
                </td>
                @if ($value->status == 'Open')
                    <td><span class="badge bg-primary me-1">{{ $value->status }}</span></td>
                @elseif ($value->status == 'Resolve')
                    <td><span class="badge bg-info me-1">{{ $value->status }}</span></td>
                @elseif ($value->status == 'Reopen')
                    <td><span class="badge bg-warning me-1">{{ $value->status }}</span></td>
                @elseif ($value->status == 'Close')
                    <td><span class="badge bg-success me-1">{{ $value->status }}</span></td>
                @endif
                <td>{{ _displayNotAvailable($value->ticket_number) }}</td>
                <td>{{ _displayNotAvailable($value->subject) }}</td>
                @if ($value->priority == 'Low')
                    <td><span class="badge bg-label-dark me-1">{{ $value->priority }}</span></td>
                @elseif ($value->priority == 'Medium')
                    <td><span class="badge bg-label-warning me-1">{{ $value->priority }}</span></td>
                @elseif ($value->priority == 'High')
                    <td><span class="badge bg-label-danger me-1">{{ $value->priority }}</span></td>
                @endif
                <td>{{ _displayNotAvailable(_displayDate($value->created_at, 'j F, Y h:i A')) }}</td>
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
