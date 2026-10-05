@if (isset($resultArr) && count($resultArr) > 0)
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col" class="text-center"><input class="all_check pointer" type="checkbox"></th>
                <th scope="col">Action</th>
                <th scope="col">Staff</th>
                <th scope="col">Title</th>
                <th scope="col">Image</th>
                <th scope="col">Type</th>
                <th scope="col">Created On</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resultArr as $key => $value)
            <tr class="table_data_val">
                <th scope="row" class="text-center"><input type="checkbox" class="checkboxId" id="ps<?php echo $value->id; ?>" name="id[]"
                    value="<?php echo $value->id; ?>"></th>
                <td><a href="{{route('admin.ndaAndOtherDocs.editForm',$value->id) }}" class="action-button" data-bs-toggle="tooltip"  data-bs-placement="top" title="Edit"><i class="bx bxs-edit"></i></a></td>
                <td>{{ _displayNotAvailable($value->staff->username ?? null) }} ({{ _displayNotAvailable($value->staff->staff_prefix ?? null) }})</td>
                <td>{{ _displayNotAvailable($value->title) }}</td>
                <td>
                    @if ($value->doc_image)
                        <a href="{{ _assetUrl('upload_path.NDA_AND_OTHER_DOCS_RECEIPT_URL').$value->doc_image }}" target="_blank" class="btn btn-sm btn-primary">View File</a>
                    @else
                        {{ _displayNotAvailable(null) }}
                    @endif
                </td>
                <td>{{ _displayNotAvailable($value->type) }}</td>
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
