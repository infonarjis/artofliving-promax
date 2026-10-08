<!-- Member DataList -->
@if (isset($resultArr) && count($resultArr) > 0)
    @foreach ($resultArr as $key => $paginationData)
        @php
            ## Auth User
            $authUser = Auth::user();

            $userType = _adminUserType($authUser->type);
            $roleId = _adminRoleId($authUser, $userType);

            ## Member View Permission
            $memberViewPermission = _checkPermission($userType, $roleId, 'view_member');
            ## Assigned Field
            $assignField = $userType === 'Staff' ? 'staff_assign_id' : 'franchise_assign_id';

            ## Check whether current member is assigned to logged-in user
            $isOwnMember = ($paginationData->$assignField ?? null) == $authUser->id;
            ## Button Permissions
            $deleteBtnPermission = _checkPermission($userType, $roleId, 'delete_member');
            $approveBtnPermission = _checkPermission($userType, $roleId, 'approve_member');
            $unApproveBtnPermission = _checkPermission($userType, $roleId, 'unapprove_member');
            $suspendBtnPermission = _checkPermission($userType, $roleId, 'suspend_member');
            $activeToPaidBtnPermission = _checkPermission($userType, $roleId, 'member_active_to_paid_button');
            $matchMakingBtnPermission = _checkPermission($userType, $roleId, 'match_making');
            $editMemberBtnPermission = _checkPermission($userType, $roleId, 'edit_member');
            $viewMemberBtnPermission = _checkPermission($userType, $roleId, 'view_profile');
            ## Button Access
            $deleteBtnAccess = $deleteBtnPermission !== 'No';
            $approveBtnAccess = $approveBtnPermission !== 'No';
            $unApproveBtnAccess = $unApproveBtnPermission !== 'No';
            $suspendBtnAccess = $suspendBtnPermission !== 'No';
            $activeToPaidBtnAccess = $activeToPaidBtnPermission !== 'No';
            $matchMakingBtnAccess = $matchMakingBtnPermission !== 'No';
            $editMemberBtnAccess = $editMemberBtnPermission !== 'No';
            $viewMemberBtnAccess = $viewMemberBtnPermission !== 'No';
            ## Own Members restriction
            if ($memberViewPermission === 'All Members') {
                if ($deleteBtnPermission === 'Own Members') {
                    $deleteBtnAccess = $isOwnMember;
                }
                if ($approveBtnPermission === 'Own Members') {
                    $approveBtnAccess = $isOwnMember;
                }
                if ($unApproveBtnPermission === 'Own Members') {
                    $unApproveBtnAccess = $isOwnMember;
                }
                if ($suspendBtnPermission === 'Own Members') {
                    $suspendBtnAccess = $isOwnMember;
                }
                if ($activeToPaidBtnPermission === 'Own Members') {
                    $activeToPaidBtnAccess = $isOwnMember;
                }
                if ($matchMakingBtnPermission === 'Own Members') {
                    $matchMakingBtnAccess = $isOwnMember;
                }
                if ($editMemberBtnPermission === 'Own Members') {
                    $editMemberBtnAccess = $isOwnMember;
                }
                if ($viewMemberBtnPermission === 'Own Members') {
                    $viewMemberBtnAccess = $isOwnMember;
                }
            }

            ## View Comment Permission :
            $viewCommentBtnPermission = _checkPermission($userType, $roleId, 'view_comment');
            $viewCommentBtnAccess = false;
            if ($viewCommentBtnPermission !== 'No') {
                $viewCommentBtnAccess = true;
                if ($viewCommentBtnPermission === 'Own Members') {
                    $viewCommentBtnAccess = $isOwnMember;
                }
            }
            ## Add Comment Permission :
            $addCommentBtnPermission = _checkPermission($userType, $roleId, 'add_comment');
            $addCommentBtnAccess = false;
            if ($addCommentBtnPermission !== 'No') {
                $addCommentBtnAccess = true;
                if ($addCommentBtnPermission === 'Own Members') {
                    $addCommentBtnAccess = $isOwnMember;
                }
            }
        @endphp
        <div class="inner_adProfileMian {{ $key > 0 ? 'mt-3' : '' }}">
            <div class="inner_flexDiv-membr d-block d-lg-flex gap-3">
                <div class="left_member_profilesDiv">
                    <div class="profile_userAdds position-relative">
                        @if (isset($dataArr->displayKeyArr) && $dataArr->displayKeyArr['photo'] != '')
                            @foreach ($dataArr->displayKeyArr['photo'] as $imgKey => $imgVal)
                                @if (!blank($paginationData->$imgKey) && _checkStorageFileExists($imgVal['imageDirPath'], $paginationData->$imgKey))
                                    <img src="{{ _assetUrl($imgVal['imageDirPath']) . $paginationData->$imgKey }}"
                                        alt="Profile Image" class="user_profiles" loading="lazy">
                                @else
                                    @php $profileImage = _getMemberDefaultImage($paginationData->gender); @endphp
                                    <img src="{{ $profileImage }}" alt="Profile Images" class="user_profiles"
                                        loading="lazy">
                                @endif
                            @endforeach
                        @endif
                        <div class="check_Mprofiles">
                            <input type="checkbox" class="checkboxId form-check-input" id="ps<?php echo $paginationData->id; ?>"
                                name="id[]" value="<?php echo $paginationData->id; ?>">
                        </div>
                    </div>
                    <div
                        class="profile_btnGroups d-flex align-items-center justify-content-center gap-4 mt-2 pt-1 mb-2">
                        @if ($viewMemberBtnAccess)
                            <a href="{{ route($dataArr->actionButtonUrl['view'], $paginationData->id) }}">
                                <button class="btn_seenProfile" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                    title="View">
                                    <img src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/img/images/eyesIcon_addm.svg' }}"
                                        alt="">
                                </button>
                            </a>
                        @endif
                        @if ($editMemberBtnAccess)
                            <a href="{{ route($dataArr->actionButtonUrl['edit'], $paginationData->id) }}">
                                <button class="btn_editprofile" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                    title="Edit">
                                    <img src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/img/images/editIcon_addm.svg' }}"
                                        alt="">
                                </button>
                            </a>
                            <button type="button" class="btn_more_details" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="icon-base bx bx-dots-vertical-rounded text-white"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-start" style="">
                                <li>
                                    <a class="dropdown-item text-dark verifyMobileEmail"
                                        data-id="{{ base64_encode($paginationData->id) }}"
                                        data-action="{{ route('admin.member.verifyMobileEmail') }}" data-type="email"
                                        href="javascript:void(0);">
                                        <i class='bx bxs-check-circle'></i> Verify Email
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-dark verifyMobileEmail"
                                        data-id="{{ base64_encode($paginationData->id) }}"
                                        data-action="{{ route('admin.member.verifyMobileEmail') }}" data-type="mobile"
                                        href="javascript:void(0);">
                                        <i class='bx bxs-check-circle'></i> Verify Mobile Number
                                    </a>
                                </li>
                            </ul>
                        @endif
                    </div>
                    @if ($activeToPaidBtnAccess)
                        <a href="{{ route($dataArr->actionButtonUrl['editPlan'], $paginationData->id) }}">
                            @php
                                $planTitle = 'Active To Paid';
                                $class = 'active_to_paid_edit_btnUsers';
                                if ($paginationData->plan_status == 'Paid') {
                                    $planTitle = 'Edit Plan';
                                    $class = 'edit_btnUsersPlan';
                                }

                                if ($paginationData->plan_status == 'Expired') {
                                    $planTitle = 'Renewal Plan';
                                    $class = 'active_to_paid_edit_btnUsers';
                                }
                            @endphp
                            @if (isset($paginationData->status) && $paginationData->status != 'Suspended')
                                <button class="{{ $class }}">
                                    <img src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/img/images/editIconmmbb.svg' }}"
                                        alt="">&nbsp; {{ $planTitle }}
                                </button>
                            @endif
                        </a>
                    @endif
                </div>
                <div class="right_content_MDivd w-100">
                    <div class="top_usenr_btnsgroups d-block d-lg-flex align-items-center justify-content-between">
                        <div class="user_name_aprv d-flex align-items-center gap-2">
                            @if (isset($dataArr->displayKeyArr) && $dataArr->displayKeyArr['title'] != '')
                                @foreach ($dataArr->displayKeyArr['title'] as $titleKey => $titleVal)
                                    <h4>
                                        @if ($titleVal['type'] == 'str')
                                            {{ _displayNotAvailable($paginationData->$titleKey) }}
                                        @elseif ($titleVal['type'] == 'date')
                                            @php
                                                $date = _displayDate($paginationData->$titleKey, 'j F, Y');
                                            @endphp
                                            {{ _displayNotAvailable($dat) }}
                                        @elseif ($titleVal['type'] == 'height')
                                            {{ _displayHeight($paginationData->$titleKey) }}
                                        @endif
                                    </h4>
                                @endforeach
                            @endif

                            @if ($paginationData->status == 'APPROVED')
                                <button class="btn_aprunge btn_approved"><i
                                        class="bx bxs-like text-white"></i>Approved</button>
                            @elseif ($paginationData->status == 'UNAPPROVED')
                                <button class="btn_aprunge btn_unapproved"><i
                                        class="bx bxs-dislike text-white"></i>Unapproved</button>
                            @elseif ($paginationData->status == 'Suspended')
                                <button class="btn_aprunge btn_suspend"><i
                                        class="bx bx-block text-white"></i>Suspended</button>
                            @endif

                            @if ($paginationData->plan_status == 'Paid')
                                <button class="btn_aprunge btn_approved"><i
                                        class='bx bxs-credit-card text-white'></i>Paid</button>
                            @elseif ($paginationData->plan_status == 'Expired')
                                <button class="btn_aprunge btn_unapproved"><i
                                        class='bx bxs-credit-card text-white'></i>Expired</button>
                            @endif
                            @if ($paginationData->is_verify == 'Yes')
                                <button class="btn_aprunge btn_approved"><i
                                        class='bx bxs-check-shield text-white'></i>Verified</button>
                            @endif

                            @if ($paginationData->fstatus == 'Featured')
                                <button class="btn_AllGroup featured_btn " data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="" data-bs-original-title="Featured"
                                    aria-label="Featured">
                                    <i class='bx bxs-star text-white'></i>
                                </button>
                            @endif
                        </div>
                        <div
                            class="right_dstGroup-btn d-block d-lg-flex d-md-flex d-sm-flex align-items-center gap-2 mt-2 mt-lg-0">
                            @if (isset($paginationData->email_verify_status) && $paginationData->email_verify_status != 'Verify')
                                @if (isset($dataArr->actionBtnArr['confirmEmail']) && $dataArr->actionBtnArr['confirmEmail'] == 1)
                                    <button
                                        class="btn_matchcount sendConfirmationEmail confirmEmail{{ $paginationData->id }}"
                                        data-id="{{ base64_encode($paginationData->id) }}"
                                        data-action="{{ route($dataArr->actionButtonUrl['confirmationEmail']) }}">
                                        <img src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/img/images/re_mailicn.svg' }}"
                                            alt="Confirmation Email">Send Confirmation Email
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div class="btm_users_details">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 border-light-dtl mt-2 pb-0 pb-lg-2 pb-md-2 pb-sm-2">
                                <div class="details_userviewas">
                                    @if (isset($dataArr->displayKeyArr) && $dataArr->displayKeyArr['field'] != '')
                                        @if ($dataArr->displayKeyArr['field']['left'] != '')
                                            @foreach ($dataArr->displayKeyArr['field']['left'] as $leftKey => $leftVal)
                                                <h6>
                                                    <span>
                                                        @if (isset($leftVal['label']) && $leftVal['label'] != '')
                                                            {{ $leftVal['label'] }}
                                                        @else
                                                            {{ _createLabel($leftKey) }}
                                                        @endif
                                                    </span>:

                                                    @if ($leftVal['type'] == 'str')
                                                        @if (_getConstant('DISABLE_DEMO') == 'Enabled' && $leftKey == 'email')
                                                            {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                                        @elseif(_getConstant('DISABLE_DEMO') == 'Enabled' && $leftKey == 'mobile')
                                                            {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                                        @else
                                                            @php $leftKeyStr = Str::limit($paginationData->$leftKey, 22); @endphp
                                                            @if ($leftKey == 'email')
                                                                @if ($paginationData->email_verify_status == 'Verify')
                                                                    <button type="button"
                                                                        class="border-0 bg-transparent p-0"
                                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                                        title="Email Verified">
                                                                        <i class='bx bx-check-circle text-success'></i>
                                                                    </button>
                                                                @else
                                                                    <button type="button"
                                                                        class="border-0 bg-transparent p-0"
                                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                                        title="Email Not Verify">
                                                                        <i
                                                                            class='verifyEmailIcon{{ $paginationData->id }} bx bx-x-circle text-danger'></i>
                                                                    </button>
                                                                @endif
                                                            @endif
                                                            {{ _displayNotAvailable(strip_tags($leftKeyStr)) }}
                                                        @endif
                                                    @elseif ($leftVal['type'] == 'date')
                                                        {{ _displayNotAvailable(_displayDate($paginationData->$leftKey, 'j F, Y')) }}
                                                    @elseif ($leftVal['type'] == 'birthdate')
                                                        {{ _displayNotAvailable(_displayDate($paginationData->$leftKey, 'j F, Y')) }}
                                                        ({{ _displayNotAvailable(_birthdateDisplay($paginationData->birthdate, 0)) }})
                                                    @elseif ($leftVal['type'] == 'age')
                                                        {{ _displayNotAvailable(_birthdateDisplay($paginationData->birthdate, 0)) }}
                                                    @elseif ($leftVal['type'] == 'height')
                                                        {{ _displayNotAvailable(_displayHeight($paginationData->$leftKey)) }}
                                                    @elseif ($leftVal['type'] == 'master')
                                                        @php
                                                            $value = optional($paginationData->{$leftVal['relation']})
                                                                ->{$leftKey};
                                                            $leftKeyStr = $value ? Str::limit($value, 22) : 'N/A';
                                                        @endphp
                                                        {{ strip_tags($leftKeyStr) }}
                                                    @elseif ($leftVal['type'] == 'user_type')
                                                        @if ($paginationData->$leftKey == 0)
                                                            <span class="badge rounded-pill bg-dark w-20">Online</span>
                                                        @else
                                                            <span
                                                                class="badge rounded-pill bg-info w-25">Personalize</span>
                                                        @endif
                                                    @elseif ($leftVal['type'] == 'verified_member')
                                                        @if ($paginationData->$leftKey == 0)
                                                            <span class="badge rounded-pill bg-dark w-20">Not Verified</span>
                                                        @else
                                                            <span class="badge rounded-pill bg-success w-25">Verified</span>
                                                        @endif
                                                    @endif
                                                </h6>
                                            @endforeach
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <div
                                class="col-lg-6 col-md-6 col-sm-6 sec mt-0 mt-lg-2 mt-md-2 mt-sm-2 pb-0 pb-lg-2 pb-md-2 pb-sm-2">
                                <div class="details_userviewas">
                                    @if (isset($dataArr->displayKeyArr) && $dataArr->displayKeyArr['field'] != '')
                                        <!-- ## Check Center Side Key :  -->
                                        @if ($dataArr->displayKeyArr['field']['center'] != '')
                                            @foreach ($dataArr->displayKeyArr['field']['center'] as $centerKey => $centerVal)
                                                <h6>
                                                    <span>
                                                        @if (isset($centerVal['label']) && $centerVal['label'] != '')
                                                            {{ $centerVal['label'] }}
                                                        @else
                                                            {{ _createLabel($centerKey) }}
                                                        @endif
                                                    </span>:

                                                    @if ($centerVal['type'] == 'str')
                                                        @if (_getConstant('DISABLE_DEMO') == 'Enabled' && $centerKey == 'email')
                                                            {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                                        @elseif(_getConstant('DISABLE_DEMO') == 'Enabled' && $centerKey == 'mobile')
                                                            {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                                        @else
                                                            @php $centerKeyStr = Str::limit($paginationData->$centerKey, 22); @endphp
                                                            @if ($centerKey == 'mobile')
                                                                @if ($paginationData->mobile_verify_status == 'Yes')
                                                                    <button type="button"
                                                                        class="border-0 bg-transparent p-0"
                                                                        data-bs-toggle="tooltip"
                                                                        data-bs-placement="top"
                                                                        title="Mobile Verified">
                                                                        <i class='bx bx-check-circle text-success'></i>
                                                                    </button>
                                                                @else
                                                                    <button type="button"
                                                                        class="border-0 bg-transparent p-0"
                                                                        data-bs-toggle="tooltip"
                                                                        data-bs-placement="top"
                                                                        title="Mobile Not Verify">
                                                                        <i
                                                                            class='verifyMobileIcon{{ $paginationData->id }} bx bx-x-circle text-danger'></i>
                                                                    </button>
                                                                @endif
                                                            @endif
                                                            {{ _displayNotAvailable(strip_tags($centerKeyStr)) }}
                                                        @endif
                                                    @elseif ($centerVal['type'] == 'date')
                                                        {{ _displayNotAvailable(_displayDate($paginationData->$centerKey, 'j F, Y')) }}
                                                    @elseif ($centerVal['type'] == 'birthdate')
                                                        {{ _displayNotAvailable(_displayDate($paginationData->$centerKey, 'j F, Y')) }}
                                                        ({{ _displayNotAvailable(_birthdateDisplay($paginationData->birthdate, 0)) }})
                                                    @elseif ($centerVal['type'] == 'age')
                                                        {{ _birthdateDisplay($paginationData->birthdate, 0) }}
                                                    @elseif ($centerVal['type'] == 'height')
                                                        {{ _displayNotAvailable(_displayHeight($paginationData->$centerKey)) }}
                                                    @elseif ($centerVal['type'] == 'master')
                                                        @php
                                                            $value = optional($paginationData->{$centerVal['relation']})
                                                                ->{$centerKey};
                                                            $centerKeyStr = $value ? Str::limit($value, 22) : 'N/A';
                                                        @endphp
                                                        {{ strip_tags($centerKeyStr) }}
                                                    @elseif ($centerVal['type'] == 'user_type')
                                                        @if ($paginationData->$centerKey == 0)
                                                            <span class="badge rounded-pill bg-dark w-20">Online</span>
                                                        @else
                                                            <span
                                                                class="badge rounded-pill bg-info w-25">Personalize</span>
                                                        @endif
                                                    @elseif ($centerVal['type'] == 'verified_member')
                                                        @if ($paginationData->$centerKey == 0)
                                                            <span class="badge rounded-pill bg-dark w-20">Not Verified</span>
                                                        @else
                                                            <span class="badge rounded-pill bg-success w-25">Verified</span>
                                                        @endif
                                                    @endif
                                                </h6>
                                            @endforeach
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Bottom Button Here -->
                    <div class="bottom_usenr_btnsgroups d-block d-lg-flex align-items-center justify-content-between">
                        <div
                            class="right_dstGroup-btn d-block d-lg-flex d-md-flex d-sm-flex align-items-center gap-2 mt-2 mt-lg-0">
                            <!-- Download Biodata Btn -->
                            @if (isset($dataArr->actionBtnArr['downloadBiodatabtn']) && $dataArr->actionBtnArr['downloadBiodatabtn'] == 1)
                                <a
                                    href="{{ route('admin.member.downloadBiodataPdf', base64_encode($paginationData->id)) }}">
                                    <button class="member_download_biodata_btn" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Download Biodata">
                                        <i class='bx bxs-cloud-download text-white fs-3'></i>Download Biodata
                                    </button>
                                </a>
                            @endif
                            <!-- Download Biodata Btn-->
                            <!-- Match Making Count -->
                            @if ($matchMakingBtnAccess)
                                <a
                                    href="{{ route('admin.matchMakingMember.index', base64_encode($paginationData->id)) }}">
                                    <button class="member_match_making_btn" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Match">
                                        <i class='bx bxs-heart-circle text-white fs-3'></i>Match
                                        ({{ $paginationData->matchMakingCount }})
                                    </button>
                                </a>
                            @endif
                            <!-- Match Making Count -->
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
                                                data-id="{{ $paginationData->id }}"
                                                data-action="{{ route($dataArr->actionButtonUrl['addComment']) }}"
                                                target-modal="#add_commentModal">Add Comments
                                            </a>
                                        </li>
                                    @endif
                                    @if ($viewCommentBtnAccess)
                                        <li>
                                            <a class="dropdown-item viewComment" href="javascript:void(0)"
                                                data-id="{{ $paginationData->id }}"
                                                data-action="{{ route($dataArr->actionButtonUrl['viewComment']) }}"
                                                target-modal="#view_commentModal">View Comments
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            @endif
                            <!-- Add Comment Here -->
                        </div>

                        {{--  New Staff Access Button --}}
                        @if ($userType != 'Admin')
                            <div class="member_status_actionGroup d-flex align-items-center gap-2 mt-2 mt-lg-0">
                                @if ($deleteBtnAccess)
                                    <button class="status_action_btn btn_delete_pill actionBtnNew" isConfirm="1"
                                        data-column="is_deleted" data-value="Yes"
                                        data-id="{{ $paginationData->id }}" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Delete">
                                        <i class='bx bxs-trash-alt'></i> Delete
                                    </button>
                                @endif
                                @if ($approveBtnAccess)
                                    <button class="status_action_btn btn_approve_pill actionBtnNew"
                                        data-column="status" data-value="APPROVED"
                                        data-id="{{ $paginationData->id }}" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Approve">
                                        <i class='bx bxs-like'></i> Approve
                                    </button>
                                @endif
                                @if ($unApproveBtnAccess)
                                    <button class="status_action_btn btn_unapprove_pill actionBtnNew"
                                        data-column="status" data-value="UNAPPROVED"
                                        data-id="{{ $paginationData->id }}" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Unapprove">
                                        <i class='bx bxs-dislike'></i> Unapprove
                                    </button>
                                @endif
                                @if ($suspendBtnAccess)
                                    <button class="status_action_btn btn_suspend_pill actionBtnNew"
                                        data-column="status" data-value="Suspended"
                                        data-id="{{ $paginationData->id }}" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Suspend">
                                        <i class='bx bx-block'></i> Suspend
                                    </button>
                                @endif
                            </div>
                        @endif
                        {{--  New Staff Access Button --}}
                    </div>
                    <!-- Bottom Button Here -->

                    {{-- Member Suspened Reason --}}
                    @if ($paginationData->status == 'Suspended' && optional($paginationData->riskScore)->suspend_reason)
                        <div class="alert alert-danger mt-2 p-2 rounded">
                            <strong><i class="bx bx-error-circle"></i> Suspension Reason:</strong><br>

                            <span class="text-dark">
                                {{ $paginationData->riskScore->suspend_reason }}
                            </span>

                            @if ($paginationData->riskScore->suspended_at)
                                <div class="small text-muted mt-1">
                                    Suspended On:
                                    {{ \Carbon\Carbon::parse($paginationData->riskScore->suspended_at)->format('d M Y h:i A') }}
                                </div>
                            @endif

                            <div class="small mt-1">
                                Risk Score:
                                <span class="badge bg-dark">
                                    {{ $paginationData->riskScore->risk_score }}
                                </span>
                            </div>
                        </div>
                    @endif
                    {{-- Member Suspened Reason --}}
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="No_dataFound text-center p-4">
        <img src="{{ _assetUrl('upload_path.ADMIN_NO_DATA_FOUND') }}" alt="No Data Found" class="Nodata-img">
    </div>
@endif
@include('admin.commonPagination', ['dataArr' => $resultArr])
