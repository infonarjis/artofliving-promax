@if (isset($resultArr) && count($resultArr) > 0)
    @php
        ## Auth User
        $authUser = Auth::user();

        $userType = _adminUserType($authUser->type);
        $roleId = _adminRoleId($authUser, $userType);
    @endphp
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col" class="text-center"><input class="all_check pointer" type="checkbox"></th>
                <th scope="col">Status</th>
                <th scope="col">Matri Id</th>
                <th scope="col">Email</th>
                <th scope="col">ID Proof Uploaded On</th>
                <th scope="col">Id Proof Type</th>
                <th scope="col">Id Proof Front</th>
                <th scope="col">Id Proof Back</th>
                @if ($userType != 'Admin')
                    <th scope="col" class="text-center">Action</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($resultArr as $key => $value)
                <tr class="table_data_val">
                    <th scope="row" class="text-center"><input type="checkbox" class="checkboxId"
                            id="ps<?php echo $value->id; ?>" name="id[]" value="<?php echo $value->id; ?>"></th>
                    @if ($value->id_proof_status == 'APPROVED')
                        <td><span class="badge bg-label-success me-1">APPROVED</span></td>
                    @endif
                    @if ($value->id_proof_status == 'UNAPPROVED')
                        <td><span class="badge bg-label-danger me-1">UNAPPROVED</span></td>
                    @endif
                    <td>
                        @if (!blank($value->matri_id))
                            <a target="_blank" href="{{ route('admin.member.viewDetails', $value->id) }}">
                                {{ _displayNotAvailable($value->matri_id) }}
                            </a>
                        @else
                            N/A
                        @endif
                    </td>
                    @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                        <td>{{ _getConstant('DISABLE_IN_DEMO_LABEL') }}</td>
                    @else
                        <td>{{ _displayNotAvailable($value->email) }}</td>
                    @endif
                    <td>{{ _displayNotAvailable(_displayDate($value->id_proof_uploaded_on, 'j F, Y h:i A')) }}</td>
                    <td>{{ _displayNotAvailable($value->id_proof_type) }}</td>
                    <td>
                        @php
                            $idProofFrontUrl = _assetUrl('upload_path.IDPROOF_ADMIN_NO_IMAGE_FOUND');
                            if (
                                !blank($value->id_proof_front) &&
                                _checkStorageFileExists('upload_path.MEMBER_IDPROOF_URL', $value->id_proof_front)
                            ) {
                                $idProofFrontUrl = _assetUrl('upload_path.MEMBER_IDPROOF_URL') . $value->id_proof_front;
                            }
                        @endphp
                        <a target="_blank" href="{{ $idProofFrontUrl }}">
                            <img src="{{ $idProofFrontUrl }}" alt="ID Proof Front" class="w-px-100 h-px-100 rounded" />
                        </a>
                    </td>
                    <td>
                        @php
                            $idProofBackUrl = _assetUrl('upload_path.IDPROOF_ADMIN_NO_IMAGE_FOUND');
                            if (
                                !blank($value->id_proof_back) &&
                                _checkStorageFileExists('upload_path.MEMBER_IDPROOF_URL', $value->id_proof_back)
                            ) {
                                $idProofBackUrl = _assetUrl('upload_path.MEMBER_IDPROOF_URL') . $value->id_proof_back;
                            }
                        @endphp
                        <a target="_blank" href="{{ $idProofBackUrl }}">
                            <img src="{{ $idProofBackUrl }}" alt="ID Proof Back" class="w-px-100 h-px-100 rounded" />
                        </a>
                    </td>
                    @if ($userType != 'Admin')
                        @php
                            ## Member View Permission
                            $idProofApprovalPermission = _checkPermission($userType, $roleId, 'id_proof_approval');
                            ## Assigned Field
                            $assignField = $userType === 'Staff' ? 'staff_assign_id' : 'franchise_assign_id';

                            ## Check whether current member is assigned to logged-in user
                            $isOwnMember = ($value->$assignField ?? null) == $authUser->id;
                            ## Button Permissions
                            $deleteBtnPermission = _checkPermission($userType, $roleId, 'id_proof_delete');
                            ## Button Access
                            $deleteBtnAccess = $deleteBtnPermission !== 'No';
                            ## Own Members restriction :
                            if ($idProofApprovalPermission === 'All Members') {
                                if ($deleteBtnPermission === 'Own Members') {
                                    $deleteBtnAccess = $isOwnMember;
                                }
                            }
                        @endphp
                        <td class="text-center">
                            @if ($deleteBtnAccess)
                                <button class="status_action_btn btn_delete_pill actionBtnNew" isConfirm="1"
                                    data-column="is_deleted" data-value="Yes" data-id="{{ $value->id }}"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                    <i class='bx bxs-trash-alt'></i> Delete
                                </button>
                            @else
                            N/A
                            @endif
                        </td>
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
