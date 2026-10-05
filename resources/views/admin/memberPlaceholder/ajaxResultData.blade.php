@if (isset($resultArr) && count($resultArr) > 0)
<table class="table">
    <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
    <thead>
        <tr class="text-nowrap table_header">
            <th scope="col" class="text-center"></th>
            <th scope="col">Action</th>
            <th scope="col">Status</th>
            <th scope="col">Male Image</th>
            <th scope="col">Female Image</th>
            <th scope="col">Male Protected Image</th>
            <th scope="col">Female Protected Image</th>
        </tr>
    </thead>
    <tbody>
        @foreach($resultArr as $key => $value)
        <tr class="table_data_val">
            <th scope="row" class="text-center"><input type="radio" class="checkboxId" id="ps<?php echo $value->id; ?>" name="id[]"
                    value="<?php echo $value->id; ?>"></th>
            <td><a href="{{ route($resultArr->actionButtonUrl['edit'],$value->id) }}" class="action-button" data-bs-toggle="tooltip"  data-bs-placement="top" title="Edit"><i class="bx bxs-edit"></i></a></td>
            @if ($value->status == 'APPROVED')
            <td><span class="badge bg-label-success me-1">{{ $value->status }}</span></td>
            @endif
            @if ($value->status == 'UNAPPROVED')
            <td><span class="badge bg-label-danger me-1">{{ $value->status }}</span></td>
            @endif
            <td>
                @php
                $imageURL = _assetUrl('upload_path.ADMIN_NO_IMAGE_FOUND');
                if (!blank($value->male_public_image) && _checkStorageFileExists('upload_path.PLACEHOLDERS_IMAGE',$value->male_public_image)) {
                    $imageURL = _assetUrl('upload_path.PLACEHOLDERS_IMAGE').$value->male_public_image;
                }
                @endphp
                <img src="{{ $imageURL }}" alt="Banner" class="w-px-100 rounded" />
            </td>
            <td>
                @php
                $imageURL = _assetUrl('upload_path.ADMIN_NO_IMAGE_FOUND');
                if (!blank($value->female_public_image) && _checkStorageFileExists('upload_path.PLACEHOLDERS_IMAGE',$value->female_public_image)) {
                    $imageURL = _assetUrl('upload_path.PLACEHOLDERS_IMAGE').$value->female_public_image;
                }
                @endphp
                <img src="{{ $imageURL }}" alt="Banner" class="w-px-100 rounded" />
            </td>
            <td>
                @php
                $imageURL = _assetUrl('upload_path.ADMIN_NO_IMAGE_FOUND');
                if (!blank($value->male_protected_image) && _checkStorageFileExists('upload_path.PLACEHOLDERS_IMAGE',$value->male_protected_image)) {
                    $imageURL = _assetUrl('upload_path.PLACEHOLDERS_IMAGE').$value->male_protected_image;
                }
                @endphp
                <img src="{{ $imageURL }}" alt="Banner" class="w-px-100 rounded" />
            </td>
            <td>
                @php
                $imageURL = _assetUrl('upload_path.ADMIN_NO_IMAGE_FOUND');
                if (!blank($value->female_protected_image) && _checkStorageFileExists('upload_path.PLACEHOLDERS_IMAGE',$value->female_protected_image)) {
                    $imageURL = _assetUrl('upload_path.PLACEHOLDERS_IMAGE').$value->female_protected_image;
                }
                @endphp
                <img src="{{ $imageURL }}" alt="Banner" class="w-px-100 rounded" />
            </td>
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
