@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')
@section('admin_content')
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
        $isOwnMember = ($resultArr->$assignField ?? null) == $authUser->id;
        ## Button Permissions :
        $approveBtnPermission = _checkPermission($userType, $roleId, 'approve_member');
        $unApproveBtnPermission = _checkPermission($userType, $roleId, 'unapprove_member');
        $suspendBtnPermission = _checkPermission($userType, $roleId, 'suspend_member');
        $matchMakingBtnPermission = _checkPermission($userType, $roleId, 'match_making');
        $editMemberBtnPermission = _checkPermission($userType, $roleId, 'edit_member');
        ## Button Access
        $approveBtnAccess = $approveBtnPermission !== 'No';
        $unApproveBtnAccess = $unApproveBtnPermission !== 'No';
        $suspendBtnAccess = $suspendBtnPermission !== 'No';
        $matchMakingBtnAccess = $matchMakingBtnPermission !== 'No';
        $editMemberBtnAccess = $editMemberBtnPermission !== 'No';
        ## Own Members restriction
        if ($memberViewPermission === 'All Members') {
            if ($approveBtnPermission === 'Own Members') {
                $approveBtnAccess = $isOwnMember;
            }
            if ($unApproveBtnPermission === 'Own Members') {
                $unApproveBtnAccess = $isOwnMember;
            }
            if ($suspendBtnPermission === 'Own Members') {
                $suspendBtnAccess = $isOwnMember;
            }
            if ($matchMakingBtnPermission === 'Own Members') {
                $matchMakingBtnAccess = $isOwnMember;
            }
            if ($editMemberBtnPermission === 'Own Members') {
                $editMemberBtnAccess = $isOwnMember;
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
    <div class="modal fade" id="add_commentModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    </div>
    <div class="modal fade" id="view_commentModal" tabindex="-1" aria-labelledby="exampleModalLabel123" aria-hidden="true">
    </div>

    <div class="content-wrapper">
        <!-- Toast with Placements -->
        <div class="bs-toast toast toast-placement-ex m-2" role="alert" aria-live="assertive" aria-atomic="true"
            data-delay="2000">
            <div class="toast-header">
                <i class="bx bx-bell me-2"></i>
                <div class="me-auto fw-semibold toast-title">Bootstrap</div>
                <small>Now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">Fruitcake chocolate bar tootsie roll gummies gummies jelly beans cake.</div>
        </div>
        <!-- Toast with Placements -->
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="inner_adProfileMian">
                <div class="inner_flexDiv-membr d-block d-lg-flex gap-3">
                    <div class="left_member_profilesDiv">
                        @php
                            ## Image Arr :
                            $imgArr = _getMemberDefaultImage($resultArr->gender);
                            if (
                                !blank($resultArr->photo1) &&
                                _checkStorageFileExists('upload_path.MEMBER_PHOTOS_URL', $resultArr->photo1)
                            ) {
                                $imgArr = _assetUrl('upload_path.MEMBER_PHOTOS_URL') . $resultArr->photo1;
                            }
                        @endphp
                        <div class="profileView_userAdds position-relative">
                            <img src="{{ $imgArr }}" alt="" class="user_profiles" id="viewProfile">
                        </div>
                        <button class="member_groupPhotots" type="button" data-bs-toggle="collapse"
                            data-bs-target="#multie_photoUpload" aria-expanded="false" aria-controls="multie_photoUpload">
                            <img src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '//assets/img/images/AllPhotoIcons.svg' }}"
                                alt=""> All Photos
                        </button>
                    </div>
                    <div class="right_content_MDivd bg_member w-100 mt-3 mt-lg-0">
                        <div class="top_usenr_btnsgroups">
                            <div class="row">
                                <div class="col-lg-8 mt-3">
                                    <div class="user_name_aprv d-flex align-items-center gap-2">
                                        <h4>{{ _displayNotAvailable($resultArr->fullname) }} -
                                            {{ _displayNotAvailable($resultArr->matri_id) }}</h4>
                                        @if ($resultArr->status == 'APPROVED')
                                            <button class="btn_aprunge btn_approved"><i
                                                    class="bx bxs-like text-white"></i>Approved</button>
                                        @elseif ($resultArr->status == 'UNAPPROVED')
                                            <button class="btn_aprunge btn_unapproved"><i
                                                    class="bx bxs-dislike text-white"></i>Unapproved</button>
                                        @elseif ($resultArr->status == 'Suspended')
                                            <button class="btn_aprunge btn_suspend"><i
                                                    class="bx bx-block text-white"></i>Suspended</button>
                                        @endif

                                        @if ($resultArr->plan_status == 'Paid')
                                            <button class="btn_aprunge btn_approved"><i
                                                    class='bx bxs-credit-card text-white'></i>Paid</button>
                                        @elseif ($resultArr->plan_status == 'Expired')
                                            <button class="btn_aprunge btn_unapproved"><i
                                                    class='bx bxs-credit-card text-white'></i>Expired</button>
                                        @endif
                                        @if ($resultArr->fstatus == 'Featured')
                                            <button class="btn_AllGroup featured_btn " data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="" data-bs-original-title="Featured"
                                                aria-label="Featured">
                                                <i class='bx bxs-star text-white'></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="user_name_aprv_new">
                                        <h6 class="text-dark"><span>Profile Completed
                                                ({{ $resultArr->completeProfile }}%)</span>
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                                    role="progressbar" style="width: <?php echo $resultArr->completeProfile; ?>%"
                                                    aria-valuenow="{{ $resultArr->completeProfile }}" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                    {{ $resultArr->completeProfile }} %
                                                </div>
                                            </div>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="btm_users_details">
                            <div class="row">
                                <div class="col-lg-10">
                                    <div class="row">
                                        <div class="col-lg-4 col-md-6 col-sm-6 mt-3">
                                            <div class="MDatails_views">
                                                <h5><span>Full Name :</span>{{ _displayNotAvailable($resultArr->fullname) }}
                                                </h5>
                                                <h5><span>Email @if ($resultArr->email_verify_status == 'Verify')
                                                            <button class="border-0 bg-transparent p-0"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="" data-bs-original-title="Verified"
                                                                aria-label="Verified">
                                                                <i class='bx bx-check-circle text-success'></i>
                                                            </button>
                                                        @else
                                                            <button class="border-0 bg-transparent p-0"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="" data-bs-original-title="Not Verify"
                                                                aria-label="Not Verify">
                                                                <i class='bx bx-x-circle text-danger'></i>
                                                            </button>
                                                        @endif:</span>
                                                    @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                                        {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                                    @else
                                                        {{ _displayNotAvailable($resultArr->email) }}
                                                    @endif
                                                </h5>
                                                <h5><span>Religion
                                                        :</span>{{ _displayNotAvailable($resultArr->religionData->religion_name ?? '') }}
                                                </h5>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 mt-0 mt-lg-3 mt-md-3 mt-sm-3">
                                            <div class="MDatails_views">
                                                <h5><span>Gender :</span>{{ _displayNotAvailable($resultArr->gender) }}
                                                </h5>
                                                <h5><span>Mobile Number @if ($resultArr->mobile_verify_status == 'Yes')
                                                            <button class="border-0 bg-transparent p-0"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="" data-bs-original-title="Verified"
                                                                aria-label="Verified">
                                                                <i class='bx bx-check-circle text-success'></i>
                                                            </button>
                                                        @else
                                                            <button class="border-0 bg-transparent p-0"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="" data-bs-original-title="Not Verify"
                                                                aria-label="Not Verify">
                                                                <i class='bx bx-x-circle text-danger'></i>
                                                            </button>
                                                        @endif:</span>
                                                    @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                                        {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                                    @else
                                                        {{ _displayNotAvailable($resultArr->mobile) }}
                                                    @endif
                                                </h5>
                                                <h5><span>Caste
                                                        :</span>{{ _displayNotAvailable($resultArr->casteData->caste_name ?? '') }}
                                                </h5>
                                            </div>
                                        </div>
                                        <div
                                            class="col-lg-4 col-md-6 col-sm-6 mt-0 mt-lg-3 mt-md-3 mt-sm-3 right_bordermv">
                                            <div class="MDatails_views">
                                                <h5><span>Marital status
                                                        :</span>{{ _displayNotAvailable($resultArr->maritalStatusData->marital_status_name ?? '') }}
                                                </h5>
                                                <h5><span>Date of Birth
                                                        :</span>
                                                    {{ _displayNotAvailable(_displayDate($resultArr->birthdate, 'j F, Y')) }}
                                                    ({{ _displayNotAvailable(_birthdateDisplay($resultArr->birthdate, 0)) }})
                                                </h5>
                                                <h5><span>Plan Status
                                                        :</span>{{ _displayNotAvailable($resultArr->plan_status) }}</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 pt-2 pt-lg-2 pt-md-2 pt-sm-2">
                                    <div class="view_profilebtn_sink">
                                        <div class="row">
                                            @if ($userType == 'Admin')
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-12 mt-2 mb-3">
                                                    <div class="side_viewsetOcpn">
                                                        <div class="icon_boxviewsdsd" data-bs-toggle="collapse"
                                                            data-bs-target="#connectActivity" aria-expanded="false"
                                                            aria-controls="connectActivity" data-bs-placement="top"
                                                            title="" data-bs-original-title="Activity"
                                                            aria-label="Activity">
                                                            <i class="bx bxs-factory text-danger"></i>
                                                        </div>
                                                        <h5>Activity</h5>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-12 mt-2 mb-3">
                                                @if ($editMemberBtnAccess)
                                                    <div class="side_viewsetOcpn">
                                                        <a href="{{ route($actionButtonUrl['edit'], $resultArr->id) }}">
                                                            <div class="icon_boxviewsdsd" data-bs-toggle="tooltip"
                                                                data-bs-placement="top" title=""
                                                                data-bs-original-title="Edit Profile"
                                                                aria-label="Edit Profile">
                                                                <i class='bx bxs-edit-alt text-primary fs-4'></i>
                                                            </div>
                                                            <h5>Edit Profile</h5>
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- photo multie profile  -->
            <div class="collapse" id="multie_photoUpload">
                <div class="pg-wrap mt-2">

                    @php
                        $profileImgArr = _getMemberDefaultImage($resultArr->gender);
                        $idProofTypeLabel = _displayNotAvailable($resultArr->id_proof_type ?? '');

                        $profileCards = [];
                        foreach (['photo1', 'photo2', 'photo3', 'photo4'] as $idx => $field) {
                            $img = $profileImgArr;
                            if (
                                !blank($resultArr->$field) &&
                                _checkStorageFileExists('upload_path.MEMBER_PHOTOS_URL', $resultArr->$field)
                            ) {
                                $img = _assetUrl('upload_path.MEMBER_PHOTOS_URL') . $resultArr->$field;
                            }
                            $profileCards[] = [
                                'field' => $field,
                                'label' => 'Photo ' . ($idx + 1),
                                'img' => $img,
                                'statusField' => $field . '_status',
                                'hasFile' => !empty($resultArr->$field),
                                'deleteAllowed' => ($actionBtnArr['photoDelete'] ?? 0) == 1,
                            ];
                        }
                    @endphp

                    {{-- ======================= SECTION 1 : PROFILE PHOTOS ======================= --}}
                    <div class="pg-section">
                        <div class="pg-section-head">
                            <h6 class="pg-section-title">
                                <span class="pg-ico"><i class='bx bxs-image'></i></span>
                                Profile Photos
                            </h6>
                        </div>
                        <div class="pg-grid">
                            @foreach ($profileCards as $i => $c)
                                @php $isApproved = ($resultArr->{$c['statusField']} ?? null) == 'APPROVED'; @endphp
                                <div class="pg-card" id="imageDiv{{ $i + 1 }}">
                                    <div class="pg-card-img">
                                        @if ($c['hasFile'])
                                            @if ($c['deleteAllowed'])
                                                <a href="javascript:void(0)"
                                                    class="pg-delete-btn remove_profile deleteProfileImage"
                                                    data-img="{{ $c['img'] }}"
                                                    data-key="imageDiv{{ $i + 1 }}"
                                                    data-label="{{ $c['field'] }}" data-id="{{ $resultArr->id }}"
                                                    data-value="">
                                                    <i class='bx bxs-trash'></i>
                                                </a>
                                            @endif
                                            @if (($actionBtnArr['photoApproval'] ?? 0) == 1)
                                                <div class="pg-approve-row">
                                                    <label class="pg-pill pg-pill-approve">
                                                        <input type="radio" name="{{ $c['statusField'] }}"
                                                            value="APPROVED" class="form-check-input approvedImage"
                                                            data-id="{{ $resultArr->id }}"
                                                            data-label="{{ $c['statusField'] }}"
                                                            {{ $isApproved ? 'checked' : '' }}>
                                                        <span class="pg-radio-dot"></span>
                                                        <i class='bx bxs-like'></i>
                                                    </label>
                                                    <label class="pg-pill pg-pill-reject">
                                                        <input type="radio" name="{{ $c['statusField'] }}"
                                                            value="UNAPPROVED" class="form-check-input approvedImage"
                                                            data-id="{{ $resultArr->id }}"
                                                            data-label="{{ $c['statusField'] }}"
                                                            {{ !$isApproved ? 'checked' : '' }}>
                                                        <span class="pg-radio-dot"></span>
                                                        <i class='bx bxs-dislike'></i>
                                                    </label>
                                                </div>
                                            @endif
                                        @endif
                                        <img src="{{ $c['img'] }}" alt="{{ $c['label'] }}"
                                            data-bs-toggle="tooltip" data-bs-placement="bottom">
                                    </div>
                                    <div class="pg-card-label">{{ $c['label'] }}</div>
                                </div>
                            @endforeach
                            {{-- Selfie --}}
                            @php
                                $img = $profileImgArr;
                                if (
                                    !blank($resultArr->selfie_photo) &&
                                    _checkStorageFileExists('upload_path.SELFIE_PHOTOS_URL', $resultArr->selfie_photo)
                                ) {
                                    $img = _assetUrl('upload_path.SELFIE_PHOTOS_URL') . $resultArr->selfie_photo;
                                }
                                $isApproved = ($resultArr->selfie_photo_status ?? null) == 'APPROVED';
                                $hasFile = !empty($resultArr->selfie_photo);
                            @endphp
                            <div class="pg-card" id="imageDiv201">
                                <div class="pg-card-img">
                                    @if ($hasFile)
                                        @if (($actionBtnArr['photoDelete'] ?? 0) == 1)
                                            <a href="javascript:void(0)"
                                                class="pg-delete-btn remove_profile deleteProfileImage"
                                                data-img="{{ $img }}" data-key="imageDiv201"
                                                data-label="selfie_photo" data-id="{{ $resultArr->id }}" data-value="">
                                                <i class='bx bxs-trash'></i>
                                            </a>
                                        @endif
                                        @if (($actionBtnArr['photoApproval'] ?? 0) == 1)
                                            <div class="pg-approve-row">
                                                <label class="pg-pill pg-pill-approve">
                                                    <input type="radio" name="selfie_photo_status" value="APPROVED"
                                                        class="form-check-input approvedImage"
                                                        data-id="{{ $resultArr->id }}" data-label="selfie_photo_status"
                                                        {{ $isApproved ? 'checked' : '' }}>
                                                    <span class="pg-radio-dot"></span>
                                                    <i class='bx bxs-like'></i>
                                                </label>
                                                <label class="pg-pill pg-pill-reject">
                                                    <input type="radio" name="selfie_photo_status" value="UNAPPROVED"
                                                        class="form-check-input approvedImage"
                                                        data-id="{{ $resultArr->id }}" data-label="selfie_photo_status"
                                                        {{ !$isApproved ? 'checked' : '' }}>
                                                    <span class="pg-radio-dot"></span>
                                                    <i class='bx bxs-dislike'></i>
                                                </label>
                                            </div>
                                        @endif
                                    @endif
                                    <img src="{{ $img }}" alt="Selfie Photo" data-bs-toggle="tooltip"
                                        data-bs-placement="bottom">
                                </div>
                                <div class="pg-card-label">Selfie Photo</div>
                            </div>
                            {{-- Horoscope --}}
                            @php
                                $img = _assetUrl('upload_path.HOROSCOPE_ADMIN_NO_IMAGE_FOUND');
                                if (
                                    !blank($resultArr->horoscope_file) &&
                                    _checkStorageFileExists('upload_path.MEMBER_HOROSCOPE_URL', $resultArr->horoscope_file)
                                ) {
                                    $img = _assetUrl('upload_path.MEMBER_HOROSCOPE_URL') . $resultArr->horoscope_file;
                                }
                                $isApproved = ($resultArr->horoscope_status ?? null) == 'APPROVED';
                                $hasFile = !empty($resultArr->horoscope_file);
                                $isVideo =
                                    $hasFile &&
                                    \Illuminate\Support\Str::endsWith(strtolower($resultArr->horoscope_file), [
                                        '.mp4',
                                        '.mov',
                                        '.webm',
                                    ]);
                            @endphp
                            <div class="pg-card" id="imageDiv401">
                                <div class="pg-card-img">
                                    @if ($hasFile)
                                        @if (($actionBtnArr['photoDelete'] ?? 0) == 1)
                                            <a href="javascript:void(0)"
                                                class="pg-delete-btn remove_profile deleteProfileImage"
                                                data-img="{{ $img }}" data-key="imageDiv401"
                                                data-label="horoscope_file" data-id="{{ $resultArr->id }}" data-value="">
                                                <i class='bx bxs-trash'></i>
                                            </a>
                                        @endif
                                        @if (($actionBtnArr['photoApproval'] ?? 0) == 1)
                                            <div class="pg-approve-row">
                                                <label class="pg-pill pg-pill-approve">
                                                    <input type="radio" name="horoscope_status" value="APPROVED"
                                                        class="form-check-input approvedImage"
                                                        data-id="{{ $resultArr->id }}" data-label="horoscope_status"
                                                        {{ $isApproved ? 'checked' : '' }}>
                                                    <span class="pg-radio-dot"></span>
                                                    <i class='bx bxs-like'></i>
                                                </label>
                                                <label class="pg-pill pg-pill-reject">
                                                    <input type="radio" name="horoscope_status" value="UNAPPROVED"
                                                        class="form-check-input approvedImage"
                                                        data-id="{{ $resultArr->id }}" data-label="horoscope_status"
                                                        {{ !$isApproved ? 'checked' : '' }}>
                                                    <span class="pg-radio-dot"></span>
                                                    <i class='bx bxs-dislike'></i>
                                                </label>
                                            </div>
                                        @endif
                                    @endif
                                    @if ($isVideo)
                                        <i class='bx bx-play-circle'
                                            style="position:absolute; inset:0; margin:auto; width:34px; height:34px; color:#fff; z-index:3; text-shadow:0 2px 6px rgba(0,0,0,.4);"></i>
                                    @endif
                                    <img src="{{ $img }}" alt="Horoscope" data-bs-toggle="tooltip"
                                        data-bs-placement="bottom">
                                </div>
                                <div class="pg-card-label">Horoscope</div>
                            </div>
                        </div>
                    </div>

                    {{-- ======================= SECTION 2 : VERIFICATION DOCUMENTS ======================= --}}
                    <div class="pg-section">
                        <div class="pg-section-head">
                            <h6 class="pg-section-title">
                                <span class="pg-ico"><i class='bx bxs-id-card'></i></span>
                                Verification Documents
                            </h6>
                            <span class="pg-type-badge mb-2">
                                <i class='bx bx-shield-quarter'></i> ID Proof Type: {{ $idProofTypeLabel }}
                            </span>
                        </div>
                        <div class="pg-grid">
                            {{-- ID Proof Front / Back --}}
                            @php
                                $idApproved = ($resultArr->id_proof_status ?? null) == 'APPROVED';
                                $idFields = [
                                    'id_proof_front' => ['label' => 'ID Proof Front', 'key' => 301],
                                    'id_proof_back' => ['label' => 'ID Proof Back', 'key' => 302],
                                ];
                            @endphp
                            @foreach ($idFields as $field => $meta)
                                @php
                                    $img = _assetUrl('upload_path.IDPROOF_ADMIN_NO_IMAGE_FOUND');
                                    if (
                                        !blank($resultArr->$field) &&
                                        _checkStorageFileExists('upload_path.MEMBER_IDPROOF_URL', $resultArr->$field)
                                    ) {
                                        $img = _assetUrl('upload_path.MEMBER_IDPROOF_URL') . $resultArr->$field;
                                    }
                                    $hasFile = !empty($resultArr->$field);
                                @endphp
                                <div class="pg-card" id="imageDiv{{ $meta['key'] }}">
                                    <div class="pg-card-img">
                                        @if ($hasFile)
                                            @if (($actionBtnArr['idProofDeletebtn'] ?? 0) == 1)
                                                <a href="javascript:void(0)"
                                                    class="pg-delete-btn remove_profile deleteProfileImage"
                                                    data-img="{{ $img }}"
                                                    data-key="imageDiv{{ $meta['key'] }}"
                                                    data-label="{{ $field }}" data-id="{{ $resultArr->id }}"
                                                    data-value="">
                                                    <i class='bx bxs-trash'></i>
                                                </a>
                                            @endif
                                            @if ($field == 'id_proof_front' && ($actionBtnArr['photoApproval'] ?? 0) == 1)
                                                <div class="pg-approve-row">
                                                    <label class="pg-pill pg-pill-approve">
                                                        <input type="radio" name="id_proof_status" value="APPROVED"
                                                            class="form-check-input approvedImage"
                                                            data-id="{{ $resultArr->id }}" data-label="id_proof_status"
                                                            {{ $idApproved ? 'checked' : '' }}>
                                                        <span class="pg-radio-dot"></span>
                                                        <i class='bx bxs-like'></i>
                                                    </label>
                                                    <label class="pg-pill pg-pill-reject">
                                                        <input type="radio" name="id_proof_status" value="UNAPPROVED"
                                                            class="form-check-input approvedImage"
                                                            data-id="{{ $resultArr->id }}" data-label="id_proof_status"
                                                            {{ !$idApproved ? 'checked' : '' }}>
                                                        <span class="pg-radio-dot"></span>
                                                        <i class='bx bxs-dislike'></i>
                                                    </label>
                                                </div>
                                            @endif
                                        @else
                                            <div class="pg-empty-note">Not uploaded</div>
                                        @endif
                                        <img src="{{ $img }}" alt="{{ $meta['label'] }}"
                                            data-bs-toggle="tooltip" data-bs-placement="bottom">
                                    </div>
                                    <div class="pg-card-label">{{ $meta['label'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>

            <div class="main_secPUtabs_btns mt-3 collapse" id="connectActivity">
                <div class="row">
                    <div class="col-lg-12 mt-12 pe-0 d-none d-lg-block">
                        <div class="tab_btncontrolPartners">
                            <ul class="nav nav-pills mb-3 gap-2 w-100 justify-content-center justify-content-lg-start"
                                id="pills-tab" role="tablist">
                                <li class="nav-item w-75" role="presentation">
                                    <a target="_blank"
                                        href="{{ route('admin.photoRequest.memberIndex', $resultArr->matri_id) }}"
                                        rel="noopener">
                                        <button class="nav-link" id="pills-photo_req-tab" data-bs-toggle="pill"
                                            data-bs-target="#pills-photo_req" type="button" role="tab"
                                            aria-controls="pills-photo_req" aria-selected="true">
                                            <img src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/img/images/use_detailsIcon.svg' }}"
                                                alt="">Photo Request ({{ $photoRequestCount }})
                                        </button>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a target="_blank" rel="noopener"
                                        href="{{ route('admin.viewContact.memberIndex', $resultArr->matri_id) }}">
                                        <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill"
                                            data-bs-target="#pills-contact" type="button" role="tab"
                                            aria-controls="pills-contact" aria-selected="false">
                                            <img src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/img/images/partner_prefIcon.png' }}"
                                                alt="">View Contact List ({{ $viewContactCount }})
                                        </button>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a target="_blank" rel="noopener"
                                        href="{{ route('admin.expressInterest.memberIndex', $resultArr->matri_id) }}">
                                        <button class="nav-link" id="pills-connects-tab" data-bs-toggle="pill"
                                            data-bs-target="#pills-connects" type="button" role="tab"
                                            aria-controls="pills-connects" aria-selected="false">
                                            <img src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/img/images/partner_prefIcon.png' }}"
                                                alt="">View Interest List ({{ $viewInterestCount }})
                                        </button>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 mt-4 pe-0 d-block d-lg-none">
                        <div class="tab_btncontrolPartners">
                            <ul class="nav nav-pills mb-3 gap-2 w-100 justify-content-center justify-content-lg-start"
                                id="pills-tab" role="tablist">
                                <li class="nav-item w-75" role="presentation">
                                    <a target="_blank"
                                        href="{{ route('admin.photoRequest.memberIndex', $resultArr->matri_id) }}"
                                        rel="noopener">
                                        <button class="nav-link" id="pills-photo_req-tab" data-bs-toggle="pill"
                                            data-bs-target="#pills-photo_req" type="button" role="tab"
                                            aria-controls="pills-photo_req" aria-selected="true">
                                            <img src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/img/images/use_detailsIcon.svg' }}"
                                                alt="">Photo Request
                                        </button>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a target="_blank"
                                        href="{{ route('admin.viewContact.memberIndex', $resultArr->matri_id) }}"
                                        rel="noopener">
                                        <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill"
                                            data-bs-target="#pills-contact" type="button" role="tab"
                                            aria-controls="pills-contact" aria-selected="false">
                                            <img src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/img/images/partner_prefIcon.png' }}"
                                                alt="">View Contact List
                                        </button>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a target="_blank"
                                        href="{{ route('admin.expressInterest.memberIndex', $resultArr->matri_id) }}"
                                        rel="noopener">
                                        <button class="nav-link" id="pills-connects-tab" data-bs-toggle="pill"
                                            data-bs-target="#pills-connects" type="button" role="tab"
                                            aria-controls="pills-connects" aria-selected="false">
                                            <img src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/img/images/partner_prefIcon.png' }}"
                                                alt="">Connect List
                                        </button>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="main_secPUtabs_btns">
                <div class="row">
                    <div class="col-lg-4 mt-4 pe-0 d-none d-lg-block">
                        <div class="tab_btncontrolPartners">
                            <ul class="nav nav-pills mb-3 gap-2 w-100 justify-content-center justify-content-lg-start"
                                id="pills-tab" role="tablist">
                                <li class="nav-item w-75" role="presentation">
                                    <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-home" type="button" role="tab"
                                        aria-controls="pills-home" aria-selected="true">
                                        <img src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/img/images/use_detailsIcon.svg' }}"
                                            alt="">User Detail
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-profile" type="button" role="tab"
                                        aria-controls="pills-profile" aria-selected="false">
                                        <img src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/img/images/partner_prefIcon.png' }}"
                                            alt="">Partner Preferences
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-8 mt-4">
                        <div class="e_member_BtnsGroup_deskty text-center text-lg-end">
                            @if ($approveBtnAccess)
                                <button class="btn_AllGroup like_btn actionBtnList" data-bs-toggle="tooltip"
                                    data-column="status" data-value="APPROVED" data-id="{{ $resultArr->id }}"
                                    data-bs-placement="top" title="Approve" data-bs-original-title="Approve"
                                    aria-label="Approve">
                                    <i class="bx bxs-like text-white"></i>
                                </button>
                            @endif
                            @if ($unApproveBtnAccess)
                                <button class="btn_AllGroup dislike_btn actionBtnList" data-bs-toggle="tooltip"
                                    data-column="status" data-value="UNAPPROVED" data-id="{{ $resultArr->id }}"
                                    data-bs-placement="top" title="" data-bs-original-title="Unapprove"
                                    aria-label="Unapprove">
                                    <i class="bx bxs-dislike text-white"></i>
                                </button>
                            @endif
                            @if ($matchMakingBtnAccess)
                                <a href="{{ route('admin.matchMakingMember.index', base64_encode($resultArr->id)) }}">
                                    <button class="btn_AllGroup match_btn" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title=""
                                        data-bs-original-title="Matches ({{ $resultArr->matchMakingCount }})"
                                        aria-label="Matches">
                                        <i class='bx bxs-heart-circle text-white fs-3'></i>
                                    </button>
                                </a>
                            @endif
                            @if ($addCommentBtnAccess || $viewCommentBtnAccess)
                                <button class="btn_AllGroup comment_btn" data-bs-toggle="dropdown" aria-expanded="false"
                                    title="Comments">
                                    <i class='bx bxs-chat text-white fs-4'></i>
                                </button>
                                <ul class="dropdown-menu EmailsetTextd">
                                    @if ($addCommentBtnAccess)
                                        <li>
                                            <a class="dropdown-item addComment" href="javascript:void(0)"
                                                data-id="{{ $resultArr->id }}"
                                                data-action="{{ route($actionButtonUrl['addComment']) }}"
                                                target-modal="#add_commentModal">Add Comments
                                            </a>
                                        </li>
                                    @endif
                                    @if ($viewCommentBtnAccess)
                                        <li>
                                            <a class="dropdown-item viewComment" href="javascript:void(0)"
                                                data-id="{{ $resultArr->id }}"
                                                data-action="{{ route($actionButtonUrl['viewComment']) }}"
                                                target-modal="#view_commentModal">View Comments
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            @endif
                            @if ($suspendBtnAccess)
                                <button class="btn_AllGroup block_btn actionBtnList" data-bs-toggle="tooltip"
                                    data-column="status" data-value="Suspended" data-id="{{ $resultArr->id }}"
                                    data-bs-placement="top" title="" data-bs-original-title="Suspended"
                                    aria-label="Suspended">
                                    <i class='bx bx-block text-white'></i>
                                </button>
                            @endif
                            @if ($resultArr->plan_status == 'Paid')
                                <button class="btn_AllGroup featured_btn actionBtnList" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="" data-bs-original-title="Featured"
                                    data-column="fstatus" data-value="Featured" data-id="{{ $resultArr->id }}"
                                    aria-label="Unfeatured">
                                    <i class='bx bxs-star text-white'></i>
                                </button>
                            @endif
                            @if (isset($resultArr) && $resultArr->plan_status == 'Paid')
                                <a href="{{ route($actionButtonUrl['currentPlan'], $resultArr->id) }}">
                                    <button class="btn_AllGroup plan_btn" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="" data-bs-original-title="Current Plan"
                                        aria-label="Current Plan">
                                        <i class='bx bxs-credit-card text-white'></i>
                                    </button>
                                </a>
                                <a href="{{ route($actionButtonUrl['editPlan'], $resultArr->id) }}">
                                    <button class="edit_btnUsers">
                                        <i class="bx bxs-edit-alt text-white fs-4"></i>&nbsp; Edit Plan
                                    </button>
                                </a>
                            @endif
                            @if (isset($resultArr) && $resultArr->plan_status == 'Expired')
                                <a href="{{ route($actionButtonUrl['currentPlan'], $resultArr->id) }}">
                                    <button class="btn_AllGroup plan_btn" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="" data-bs-original-title="Recent Plan"
                                        aria-label="Recent Plan">
                                        <i class='bx bxs-credit-card text-white'></i>
                                    </button>
                                </a>
                                <a href="{{ route($actionButtonUrl['editPlan'], $resultArr->id) }}">
                                    <button class="edit_btnUsers">
                                        <i class="bx bxs-edit-alt text-white fs-4"></i>&nbsp; Edit Plan
                                    </button>
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-4 mt-4 pe-0 d-block d-lg-none">
                        <div class="tab_btncontrolPartners">
                            <ul class="nav nav-pills mb-3 gap-2 w-100 justify-content-center justify-content-lg-start"
                                id="pills-tab" role="tablist">
                                <li class="nav-item w-75" role="presentation">
                                    <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-home" type="button" role="tab"
                                        aria-controls="pills-home" aria-selected="true">
                                        <img src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/img/images/use_detailsIcon.svg' }}"
                                            alt="">User Detail
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-profile" type="button" role="tab"
                                        aria-controls="pills-profile" aria-selected="false">
                                        <img src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/img/images/partner_prefIcon.png' }}"
                                            alt="">Partner Preferences
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- tabs content partner & usaer  -->
            <div class="partner_user_Tabmain">
                <div class="tab-content p-0" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                        aria-labelledby="pills-home-tab" tabindex="0">
                        @if (isset($memberFields) && $memberFields != '')
                            @php $i = 0; @endphp
                            @foreach ($memberFields as $fieldKey => $fieldData)
                                @php
                                    $i = $i + 1;
                                    if ($i == 1) {
                                        $iconImg = 'TB1.svg';
                                    } elseif ($i == 2) {
                                        $iconImg = 'TB2.svg';
                                    } elseif ($i == 3) {
                                        $iconImg = 'TB3.svg';
                                    } elseif ($i == 4) {
                                        $iconImg = 'TB4.svg';
                                    } elseif ($i == 5) {
                                        $iconImg = 'TB5.svg';
                                    } elseif ($i == 6) {
                                        $iconImg = 'TB6.svg';
                                    } elseif ($i == 7) {
                                        $iconImg = 'TB7.svg';
                                    }
                                @endphp
                                <div class="inner_collapse_userPartner mb-4">
                                    <button class="collapbtn" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#U_detailsDiv{{ $i }}" aria-expanded="false"
                                        aria-controls="U_detailsDiv{{ $i }}">
                                        <div class="icon_usBox">
                                            <div class="text_icons-vb">
                                                <img src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/img/images/' . $iconImg }}"
                                                    alt="">
                                            </div>
                                            {{ $fieldKey }}
                                        </div>
                                    </button>
                                    <div class="collapse show" id="U_detailsDiv{{ $i }}">
                                        <div class="inner_views_detailsFGH">
                                            <div class="row">
                                                @foreach ($fieldData as $key => $dataValue)
                                                    @if ($dataValue == 'family_details')
                                                        <div
                                                            class="col-lg-12 mb-lg-4 col-md-3 mb-md-4 col-sm-6 mb-sm-3 col-6 mb-3">
                                                            <div class="detalistsleg">
                                                                <h5><span>{{ $dataValue['label'] }} :</span>
                                                                    {{ _displayNotAvailable($dataValue['value']) }}</h5>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div
                                                            class="col-lg-3 mb-lg-4 col-md-3 mb-md-4 col-sm-6 mb-sm-3 col-6 mb-3">
                                                            <div class="detalistsleg">
                                                                <h5><span>{{ $dataValue['label'] }} :</span>
                                                                    {{ _displayNotAvailable($dataValue['value']) }}
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab"
                        tabindex="0">
                        @foreach ($partnerFields as $fieldKey => $fieldData)
                            <div class="inner_collapse_userPartner mb-4">
                                <button class="collapbtn" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#U_detailsDiv1" aria-expanded="false" aria-controls="U_detailsDiv1">
                                    <div class="icon_usBox">
                                        <div class="text_icons-vb">
                                            <img src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/img/images/TB1.svg' }}"
                                                alt="">
                                        </div>
                                        {{ $fieldKey }}
                                    </div>
                                </button>
                                <div class="collapse show" id="U_detailsDiv1">
                                    <div class="inner_views_detailsFGH">
                                        <div class="row">
                                            @foreach ($fieldData as $key => $dataValue)
                                                <div class="col-lg-3 mb-lg-4 col-md-3 mb-md-4 col-sm-6 mb-sm-3 col-6 mb-3">
                                                    <div class="detalistsleg">
                                                        @if (!blank($dataValue['value']))
                                                            @if ($dataValue['key'] == 'part_height')
                                                                <h5><span>{{ $dataValue['label'] }} :</span>
                                                                    {{ _displayHeight($dataValue['value']) }}</h5>
                                                            @elseif ($dataValue['key'] == 'part_height_to')
                                                                <h5><span>{{ $dataValue['label'] }} :</span>
                                                                    {{ _displayHeight($dataValue['value']) }}</h5>
                                                            @elseif ($dataValue['key'] == 'part_frm_age')
                                                                <h5><span>{{ $dataValue['label'] }} :</span>
                                                                    {{ _displayNotAvailable($dataValue['value']) }}
                                                                    Years
                                                                </h5>
                                                            @elseif ($dataValue['key'] == 'part_to_age')
                                                                <h5><span>{{ $dataValue['label'] }} :</span>
                                                                    {{ _displayNotAvailable($dataValue['value']) }}
                                                                    Years
                                                                </h5>
                                                            @else
                                                                <h5><span>{{ $dataValue['label'] }} :</span>
                                                                    {{ _displayNotAvailable($dataValue['value']) }}
                                                                </h5>
                                                            @endif
                                                        @else
                                                            <h5><span>{{ $dataValue['label'] }} :</span> N/A</h5>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <!-- / Content -->
        <div class="content-backdrop fade"></div>
    </div>
    @csrf
    <input type="hidden" name="changeStatusUrl" id="changeStatusUrl" value="{{ route($changeStatusUrl) }}">
    <input type="hidden" name="redirectUrl" id="redirectUrl" value="{{ route($redirectUrl) }}">
@endsection
