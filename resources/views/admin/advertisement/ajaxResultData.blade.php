@if (isset($resultArr) && count($resultArr) > 0)
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col" class="text-center"><input class="all_check pointer" type="checkbox"></th>
                <th scope="col">Action</th>
                <th scope="col">Level</th>
                <th scope="col">Type</th>
                <th scope="col">Link</th>
                <th scope="col">Google Adsence</th>
                <th scope="col">Status</th>
                <th scope="col">Created On</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resultArr as $key => $value)
            <tr class="table_data_val">
                <th scope="row" class="text-center"><input type="checkbox" class="checkboxId"
                    id="ps<?php echo $value->id; ?>" name="id[]"
                        value="<?php echo $value->id; ?>"></th>
                <td>
                    <a href="{{ route('admin.advertisement.editForm',$value->id) }}" class="action-button" data-bs-toggle="tooltip"  data-bs-placement="top" title="Edit"><i class="bx bxs-edit"></i></a>
                </td>
                <td>{{ _displayNotAvailable($value->level) }}</td>
                <td>{{ _displayNotAvailable(ucfirst($value->adv_type)) }}</td>
                <td>{{ _displayNotAvailable($value->link) }}</td>
                <td>{{ _displayNotAvailable($value->google_adsense) }}</td>
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
