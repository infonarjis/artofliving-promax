@if (isset($resultArr) && count($resultArr) > 0)
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col" class="text-center"><input class="all_check pointer" type="checkbox"></th>
                <th scope="col">Action</th>
                <th scope="col">Staff ID</th>
                <th scope="col">Profile Image</th>
                <th scope="col">Username</th>
                <th scope="col">Email</th>
                <th scope="col">Password</th>
                <th scope="col">Created Date</th>
                <th scope="col">Staff Role</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($resultArr as $key => $value)
                <tr class="table_data_val">
                    <th scope="row" class="text-center"><input type="checkbox" class="checkboxId"
                            id="ps<?php echo $value->id; ?>" name="id[]" value="<?php echo $value->id; ?>"></th>
                    <td>
                        <a href="{{ route($dataArr->actionButtonUrl['edit'], $value->id) }}" class="action-button"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i
                                class="bx bxs-edit"></i></a>
                        <a href="{{ route($dataArr->actionButtonUrl['view'], $value->id) }}" class="action-button"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="View"><i
                                class="bx bx-show"></i></a>
                        <a href="{{ route($dataArr->actionButtonUrl['paySlip'], $value->id) }}" class="action-button"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Pay Salary Slip" target="_blank"><i
                                class="bx bx-money"></i></a>
                    </td>
                    <td>{{ _displayNotAvailable($value->staff_prefix) }}</td>
                    <td>
                        @php
                            $profileImageUrl = _assetUrl('upload_path.IDPROOF_ADMIN_NO_IMAGE_FOUND');
                            if (
                                !blank($value->profile_image) &&
                                _checkStorageFileExists('upload_path.STAFF_IMAGE_URL', $value->profile_image)
                            ) {
                                $profileImageUrl = _assetUrl('upload_path.STAFF_IMAGE_URL') . $value->profile_image;
                            }
                        @endphp
                        <a target="_blank" href="{{ $profileImageUrl }}">
                            <img src="{{ $profileImageUrl }}" alt="Profile Image" class="w-px-75 h-px-75 rounded" />
                        </a>
                    </td>
                    <td>{{ _displayNotAvailable($value->username) }}</td>
                    @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                        <td>{{ _getConstant('DISABLE_IN_DEMO_LABEL') }}</td>
                    @else
                        <td>{{ _displayNotAvailable($value->email) }}</td>
                    @endif
                    <td>{{ _displayNotAvailable($value->password_decrypted) }}</td>
                    <td>{{ _displayNotAvailable(_displayDate($value->created_at, 'j F, Y')) }}</td>
                    <td>
                        {{ _displayNotAvailable(optional($value->staffRole)->role_name) }}
                    </td>
                    <td>
                        @if ($value->status == 'APPROVED')
                            <span class="badge bg-label-success me-1">{{ $value->status }}</span>
                        @endif
                        @if ($value->status == 'UNAPPROVED')
                            <span class="badge bg-label-danger me-1">{{ $value->status }}</span>
                        @endif
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
