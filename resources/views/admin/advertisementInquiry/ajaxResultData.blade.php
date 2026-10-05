@if (isset($resultArr) && count($resultArr) > 0)
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col" class="text-center"><input class="all_check pointer" type="checkbox"></th>
                <th scope="col">Banner</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Mobile</th>
                <th scope="col">Description</th>
                <th scope="col">Status</th>
                <th scope="col">Created On</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($resultArr as $key => $value)
                <tr class="table_data_val">
                    <th scope="row" class="text-center"><input type="checkbox" class="checkboxId"
                            id="ps<?php echo $value->id; ?>" name="id[]" value="<?php echo $value->id; ?>"></th>
                    <td>
                        @php
                            $imageURL = _assetUrl('upload_path.ADMIN_NO_IMAGE_FOUND');

                            if (
                                !blank($value->image) &&
                                _checkStorageFileExists('upload_path.ADVERTISE_IMAGE_URL', $value->image)
                            ) {
                                $imageURL = _assetUrl('upload_path.ADVERTISE_IMAGE_URL') . $value->image;
                            }
                        @endphp

                        <div class="d-flex flex-column align-items-center gap-2">
                            <img src="{{ $imageURL }}" alt="Advertisement" class="w-px-100 rounded">

                            @if (!blank($value->image) && _checkStorageFileExists('upload_path.ADVERTISE_IMAGE_URL', $value->image))
                                <a href="{{ $imageURL }}" download="{{ $value->image }}"
                                    class="btn btn-sm btn-primary p-1">
                                    <iconify-icon icon="solar:download-outline" class="me-1"></iconify-icon>
                                    Download
                                </a>
                            @endif
                        </div>
                    </td>
                    <td>{{ _displayNotAvailable($value->name) }}</td>
                    <td>{{ _displayNotAvailable($value->email) }}</td>
                    <td>{{ _displayNotAvailable($value->mobile) }}</td>
                    <td>
                        <span class="fw-semibold">Contact Person :</span> {!! _displayNotAvailable($value->contact_person ?? '') !!} <br>
                        <span class="fw-semibold">Link :</span> {!! _displayNotAvailable($value->link ?? '') !!} <br>
                        {{-- <span class="fw-semibold">Level :</span> {!! _displayNotAvailable($value->level ?? '') !!} --}}
                    </td>
                    @if ($value->status == 'PENDING')
                        <td><span class="badge bg-label-warning me-1">{{ $value->status }}</span></td>
                    @endif
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
