<!-- Member DataList -->
@if (isset($resultArr) && count($resultArr) > 0)
    @foreach ($resultArr as $key => $dataValue)
        @php
            ## Auth User
            $authUser = Auth::user();
            $userType = _adminUserType($authUser->type);
            $roleId = _adminRoleId($authUser, $userType);
            ## Assigned Field
            $assignField = $userType === 'Staff' ? 'staff_assign_id' : 'franchise_assign_id';
            ## Check whether current lead is assigned to logged-in user
            $isOwnMember = ($dataValue->$assignField ?? null) == $authUser->id;
            $leadGeneratePermission = _checkPermission($userType, $roleId, 'view_lead_generation');

            ## View Comment Permission :
            $viewCommentBtnPermission = _checkPermission($userType, $roleId, 'lead_generation_view_comment');
            $viewCommentBtnAccess = false;
            if ($viewCommentBtnPermission !== 'No') {
                $viewCommentBtnAccess = true;
                if ($viewCommentBtnPermission === 'Own Members') {
                    $viewCommentBtnAccess = $isOwnMember;
                }
            }
            ## Add Comment Permission :
            $addCommentBtnPermission = _checkPermission($userType, $roleId, 'lead_generation_add_comment');
            $addCommentBtnAccess = false;
            if ($addCommentBtnPermission !== 'No') {
                $addCommentBtnAccess = true;
                if ($addCommentBtnPermission === 'Own Members') {
                    $addCommentBtnAccess = $isOwnMember;
                }
            }
            ## Add Lead Convert to Member Permission :
            $leadConvertMemberBtnPermission = _checkPermission(
                $userType,
                $roleId,
                'lead_generation_convert_member',
            );
            $leadConvertMemberBtnAccess = false;
            if ($leadConvertMemberBtnPermission !== 'No') {
                $leadConvertMemberBtnAccess = true;
                if ($leadConvertMemberBtnPermission === 'Own Members') {
                    $leadConvertMemberBtnAccess = $isOwnMember;
                }
            }
            ## Edit Lead generation Permission :
            $editLeadGenerationBtnPermission = _checkPermission($userType, $roleId, 'edit_lead_generation');
            $editLeadGenerationBtnAccess = false;
            if ($editLeadGenerationBtnPermission !== 'No') {
                $editLeadGenerationBtnAccess = true;
                if ($editLeadGenerationBtnPermission === 'Own Members') {
                    $editLeadGenerationBtnAccess = $isOwnMember;
                }
            }

            ## Lead Delete Option :
            $deleteBtnPermission = _checkPermission($userType, $roleId, 'delete_lead_generation');
            $deleteBtnAccess = $deleteBtnPermission !== 'No';
            if ($leadGeneratePermission === 'All Members') {
                if ($deleteBtnPermission === 'Own Members') {
                    $deleteBtnAccess = $isOwnMember;
                }
            }
        @endphp

        <div class="inner_adProfileMian {{ $key > 0 ? 'mt-3' : '' }}">
            <div class="inner_flexDiv-membr d-block d-lg-flex gap-3">
                <div class="right_content_MDivd w-100">
                    <div class="top_usenr_btnsgroups d-block d-lg-flex align-items-center justify-content-between">
                        <div class="user_name_aprv d-flex align-items-center gap-2">
                            <input type="checkbox" class="checkboxId form-check-input" id="ps<?php echo $dataValue->id; ?>"
                                name="id[]" value="<?php echo $dataValue->id; ?>">
                            <h4>{{ $dataValue->username }}</h4>
                            <div class="user_name_aprv d-flex align-items-center gap-2">
                            </div>
                        </div>
                    </div>

                    <div class="btm_users_details">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 border-light-dtl mt-2 pb-0 pb-lg-2 pb-md-2 pb-sm-2">
                                <div class="details_userviewas">
                                    <h6><span>Gender</span>: {{ _displayNotAvailable($dataValue->gender) }}</h6>
                                    <h6><span>Mobile Number</span>:
                                        @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                            {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                        @else
                                            {{ _displayNotAvailable($dataValue->phone_no_1) }}
                                        @endif
                                    </h6>
                                    <h6><span>Mobile Number 3</span>:
                                        @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                            {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                        @else
                                            {{ _displayNotAvailable($dataValue->phone_no_3) }}
                                        @endif
                                    </h6>
                                    <h6><span>Assign To Staff</span>:
                                        {{ _displayNotAvailable($dataValue->staff->username ?? null) }}</h6>
                                    <h6><span>Assign To Franchise</span>:
                                        {{ _displayNotAvailable($dataValue->franchise->username ?? null) }}</h6>
                                    <h6><span>Reg Date</span>:
                                        {{ _displayNotAvailable(_displayDate($dataValue->created_at, 'j F, Y h:i A')) }}
                                    </h6>
                                </div>
                            </div>
                            <div
                                class="col-lg-6 col-md-6 col-sm-6 sec mt-0 mt-lg-2 mt-md-2 mt-sm-2 pb-0 pb-lg-2 pb-md-2 pb-sm-2">
                                <div class="details_userviewas">
                                    <h6><span>Email</span>: {{ _displayNotAvailable($dataValue->email) }}</h6>
                                    <h6><span>Mobile Number 2</span>:
                                        @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                            {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                        @else
                                            {{ _displayNotAvailable($dataValue->phone_no_2) }}
                                        @endif
                                    </h6>
                                    <h6><span>Country Name</span>: {{ _displayNotAvailable($dataValue->country) }}</h6>
                                    <h6><span>Interest</span>: {{ _displayNotAvailable($dataValue->interest) }}</h6>
                                    <h6><span>Next Followup Date</span>:
                                        {{ _displayNotAvailable(_displayDate($dataValue->followup_date, 'j F, Y h:i A')) }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Bottom Button Here -->
                    <div class="bottom_usenr_btnsgroups d-block d-lg-flex align-items-center justify-content-between">
                        <div
                            class="right_dstGroup-btn d-block d-lg-flex d-md-flex d-sm-flex align-items-center gap-2 mt-2 mt-lg-0">
                            @if ($editLeadGenerationBtnAccess)
                                <a href="{{ route($dataArr->actionButtonUrl['edit'], $dataValue->id) }}">
                                    <button class="member_match_making_btn" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Edit Lead">
                                        <i class='bx bx-edit'></i>Edit Lead
                                    </button>
                                </a>
                            @endif
                            <div class="center_bx-line"></div>
                            <!-- Add Comment Here -->
                            @if ($addCommentBtnAccess || $viewCommentBtnAccess)
                                <button class="member_comment_btn" data-bs-toggle="dropdown" aria-expanded="false"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Comments">
                                    <i class='bx bxs-chat text-white fs-3'></i>Comments
                                </button>
                                <ul class="dropdown-menu EmailsetTextd">
                                    @if ($addCommentBtnAccess)
                                        <li>
                                            <a class="dropdown-item addComment" href="javascript:void(0)"
                                                data-id="{{ $dataValue->id }}"
                                                data-action="{{ route($dataArr->actionButtonUrl['addComment']) }}"
                                                target-modal="#add_commentModal">Add Comments
                                            </a>
                                        </li>
                                    @endif
                                    @if ($viewCommentBtnAccess)
                                        <li>
                                            <a class="dropdown-item viewComment" href="javascript:void(0)"
                                                data-id="{{ $dataValue->id }}"
                                                data-action="{{ route($dataArr->actionButtonUrl['viewComment']) }}"
                                                target-modal="#view_commentModal">View Comments
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            @endif
                            <!-- Add Comment Here -->
                            @if ($leadConvertMemberBtnAccess && $dataValue->is_registered != 'Yes')
                                <a href="{{ route($dataArr->actionButtonUrl['convertMember'], $dataValue->id) }}">
                                    <button class="member_match_making_btn" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Convert To Member">
                                        <i class='bx bx-plus'></i>Convert To Member
                                    </button>
                                </a>
                            @endif
                        </div>

                        {{--  New Staff Access Button --}}
                        @if ($userType != 'Admin')
                            <div class="member_status_actionGroup d-flex align-items-center gap-2 mt-2 mt-lg-0">
                                @if ($deleteBtnAccess)
                                    <button class="status_action_btn btn_delete_pill actionBtnNew" isConfirm="1"
                                        data-column="is_deleted" data-value="Yes" data-id="{{ $dataValue->id }}"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                        <i class='bx bxs-trash-alt'></i> Delete
                                    </button>
                                @endif
                            </div>
                        @endif
                        {{--  New Staff Access Button --}}
                    </div>
                    <!-- Bottom Button Here -->
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="No_dataFound text-center p-4">
        <img src="{{ _assetUrl('upload_path.ADMIN_NO_DATA_FOUND') }}" alt="No Data Found" class="Nodata-img">
    </div>
@endif
@include('admin.commonPagination')
