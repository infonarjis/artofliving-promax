@if (isset($resultArr) && count($resultArr) > 0)
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col" class="text-center"><input class="all_check pointer" type="checkbox"></th>
                <th scope="col">Status</th>
                <th scope="col">Action</th>
                <th scope="col">Language Name</th>
                <th scope="col">Language Code</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($resultArr as $key => $value)
                <tr class="table_data_val">
                    @if ($value->is_default == 'Yes')
                        <th class="text-center"><span class="badge rounded-pill bg-label-primary">Default</span></th>
                    @else
                        <th scope="row" class="text-center"><input type="checkbox" class="checkboxId"
                                id="ps<?php echo $value->id; ?>" name="id[]" value="<?php echo $value->id; ?>">
                        </th>
                    @endif
                    @if ($value->status == 'APPROVED')
                        <td><span class="badge bg-label-success me-1">{{ $value->status }}</span></td>
                    @endif
                    @if ($value->status == 'UNAPPROVED')
                        <td><span class="badge bg-label-danger me-1">{{ $value->status }}</span></td>
                    @endif
                    <td>
                        <a href="{{ route($dataArr->actionButtonUrl['edit'], $value->id) }}">
                            <i class="bx bxs-edit"></i> Edit
                        </a>
                        <a href="{{ route($dataArr->actionButtonUrl['languageEdit'], base64_encode($value->id)) }}">
                            <i class="bx bx-world"></i> Translations
                        </a>
                    </td>
                    <td>{{ _displayNotAvailable($value->lang_name) }}</td>
                    <td>{{ _displayNotAvailable($value->lang_code) }}</td>
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
