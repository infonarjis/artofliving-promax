@if (isset($resultArr) && count($resultArr) > 0)
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col" class="text-center"><input class="all_check pointer" type="checkbox"></th>
                <th scope="col">Action</th>
                <th scope="col">Category Name</th>
                <th scope="col">Category Image</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($resultArr as $key => $value)
                <tr class="table_data_val">
                    <th scope="row" class="text-center"><input type="checkbox" class="checkboxId"
                            id="ps<?php echo $value->id; ?>" name="id[]" value="<?php echo $value->id; ?>"></th>
                    <td><a href="{{ route($dataArr->actionButtonUrl['edit'], $value->id) }}" class="action-button"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i
                                class="bx bxs-edit"></i></a></td>
                    <td>{{ $value->category_name }}</td>
                    <td>
                        @php
                            $imageURL = _assetUrl('upload_path.ADMIN_NO_IMAGE_FOUND');
                            if (!blank($value->image) && _checkStorageFileExists('upload_path.WEDDING_PLANNER_IMAGE_URL', $value->image)) {
                                $imageURL = _assetUrl('upload_path.WEDDING_PLANNER_IMAGE_URL') . $value->image;
                            }
                        @endphp
                        <img src="{{ $imageURL }}" alt class="w-px-100 rounded" />
                    </td>
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
