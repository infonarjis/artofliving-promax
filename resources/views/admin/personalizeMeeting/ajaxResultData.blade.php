@if (isset($resultArr) && count($resultArr) > 0)
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col" class="text-center"><input class="all_check pointer" type="checkbox"></th>
                <th scope="col">View</th>
                <th scope="col">Member 1 Matri Id</th>
                <th scope="col">Member 1 Status</th>
                <th scope="col">Member 2 Matri Id</th>
                <th scope="col">Member 2 Status</th>
                <th scope="col">Meeting Date</th>
                <th scope="col">Meeting Status</th>
                <th scope="col">Created On</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resultArr as $key => $value)
            <tr class="table_data_val">
                <th scope="row" class="text-center"><input type="checkbox" class="checkboxId" id="ps<?php echo $value->id; ?>" name="id[]"
                    value="<?php echo $value->id; ?>">
                </th>
                <td>
                    <a href="{{route($dataArr->actionButtonUrl['view'],$value->id) }}" class="action-button" data-bs-toggle="tooltip"  data-bs-placement="top" title="View"><i class="bx bx-show"></i></a>
                </td>
                <td>{{ $value->member1_matri_id }}</td>
                @if ($value->member1_status == 0)
                <td><span class="badge bg-label-warning me-1">Pending</span></td>
                @endif
                @if ($value->member1_status == 1)
                <td><span class="badge bg-label-success me-1">Accept</span></td>
                @endif
                @if ($value->member1_status == 2)
                <td><span class="badge bg-label-danger me-1">Reject</span></td>
                @endif
                <td>{{ $value->member2_matri_id }}</td>
                @if ($value->member2_status == 0)
                <td><span class="badge bg-label-warning me-1">Pending</span></td>
                @endif
                @if ($value->member2_status == 1)
                <td><span class="badge bg-label-success me-1">Accept</span></td>
                @endif
                @if ($value->member2_status == 2)
                <td><span class="badge bg-label-danger me-1">Reject</span></td>
                @endif
                <td>{{ _displayDate($value->date_time, 'j F, Y') }}</td>
                @if ($value->meeting_status == 0)
                <td><span class="badge bg-label-warning me-1">Pending</span></td>
                @endif
                @if ($value->meeting_status == 1)
                <td><span class="badge bg-label-success me-1">Completed</span></td>
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
