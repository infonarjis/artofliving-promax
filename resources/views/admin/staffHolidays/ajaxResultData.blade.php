@if (isset($resultArr) && count($resultArr) > 0)
<table class="table">
    <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
    <thead>
        <tr class="text-nowrap table_header">
            <th scope="col" class="text-center"><input class="all_check pointer" type="checkbox"></th>
            @if (isset($dataArr->actionBtnArr['edit']) && $dataArr->actionBtnArr['edit'] == 1)
                <th scope="col">Action</th>
            @endif
            <th scope="col">Status</th>
            <th scope="col">Title</th>
            <th scope="col">Description</th>
            <th scope="col">Holiday Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($resultArr as $key => $value)
        <tr class="table_data_val">
            <th scope="row" class="text-center"><input type="checkbox" class="checkboxId" id="ps<?php echo $value->id; ?>" name="id[]"
                    value="<?php echo $value->id; ?>"></th>
            @if (isset($dataArr->actionBtnArr['edit']) && $dataArr->actionBtnArr['edit'] == 1)
                <td><a href="{{route('admin.staffHolidays.editForm',$value->id) }}" class="action-button" data-bs-toggle="tooltip"  data-bs-placement="top" title="Edit"><i class="bx bxs-edit"></i></a></td>
            @endif
            @if ($value->status == 'APPROVED')
            <td><span class="badge bg-label-success me-1">{{ $value->status }}</span></td>
            @endif
            @if ($value->status == 'UNAPPROVED')
            <td><span class="badge bg-label-danger me-1">{{ $value->status }}</span></td>
            @endif
            <td>{{ _displayNotAvailable($value->title) }}</td>
            <td>{{ _displayNotAvailable($value->description) }}</td>
            <td>{{ _displayNotAvailable(_displayDate($value->holiday_date,'Y-m-d')) }}</td>
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
