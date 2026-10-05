@if (isset($resultArr) && count($resultArr) > 0)
    @foreach ($resultArr as $key => $value)
        @php
            ## Auth User
            $authUser = Auth::user();

            $userType = _adminUserType($authUser->type);
            $roleId = _adminRoleId($authUser, $userType);

            ## Member View Permission
            $photoApprovalPermission = _checkPermission($userType, $roleId, 'photo_approval');
            ## Assigned Field
            $assignField = $userType === 'Staff' ? 'staff_assign_id' : 'franchise_assign_id';

            ## Check whether current member is assigned to logged-in user
            $isOwnMember = ($value->$assignField ?? null) == $authUser->id;
            ## Button Permissions
            $deleteBtnPermission = _checkPermission($userType, $roleId, 'photo_delete');
            ## Button Access
            $deleteBtnAccess = $deleteBtnPermission !== 'No';
            ## Own Members restriction
            if ($photoApprovalPermission === 'All Members') {
                if ($deleteBtnPermission === 'Own Members') {
                    $deleteBtnAccess = $isOwnMember;
                }
            }
        @endphp
        <div class="inner_adProfileMian mt-2">
            <div class="inner_flexDiv-membr d-block d-lg-flex gap-3">
                <div class="left_member_profilesDiv"></div>
                <div class="right_content_MDivd w-100">
                    <div class="top_usenr_btnsgroups d-block d-lg-flex align-items-center justify-content-between">
                        <div class="user_name_aprv d-flex align-items-center gap-2">
                            <input type="checkbox" class="checkboxId form-check-input mt-0" id="ps<?php echo $value->id; ?>" name="id[]" value="<?php echo $value->id; ?>">
                            <a target="_blank" href="{{ route('admin.member.viewDetails',$value->id) }}">
                                <h4> {{ $value->fullname }} ({{ $value->matri_id }})</h4>
                            </a>
                        </div>
                    </div>
                    <div class="btm_users_details">
                        <div class="row">
                            @for($i = 1; $i <= 4; $i++)
                                @php
                                    $photo = "photo$i";
                                    $status = $value->{$photo."_status"};
                                    $uploadedOn = $value->{$photo."_uploaded_on"};
                                    $imgArr = _getMemberDefaultImage($value->gender);
                                    if (!blank($value->$photo) && _checkStorageFileExists('upload_path.MEMBER_PHOTOS_URL',$value->$photo)) {
                                        $imgArr = _assetUrl('upload_path.MEMBER_PHOTOS_URL').$value->$photo;
                                    }
                                @endphp
                                <div class="col-lg-3 col-md-3 col-sm-6 my-3">
                                    <div class="single_imgProfiels-wb">
                                        <div class="position-relative approval-photos">
                                            <a target="_blank" href="{{ $imgArr }}">
                                                <img src="{{ $imgArr }}" alt="{{ $value->matri_id }}" class="uproval-photos-image">
                                            </a>
                                            @if (!blank($value->$photo))
                                                @php
                                                    $checkedYes = $status == 'APPROVED' ? 'checked' : '';
                                                    $checkedNo = $status == 'UNAPPROVED' ? 'checked' : '';
                                                @endphp
                                                @if ($deleteBtnAccess)
                                                    <button class="remove_profile deleteProfileImage"
                                                        data-label="{{ $photo }}" data-id="{{ $value->id }}">
                                                        <img src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH').'/assets/img/images/delete_profile_icon.png' }}" alt="Delete">
                                                    </button>
                                                @endif
                                                <div class="radio_likeBtn-sp mb-5">
                                                    <label for="{{ $photo }}_status_like_{{ $value->id }}" class="like_lk">
                                                        <input name="{{ $photo }}_status_{{ $value->id }}" {{ $checkedYes }} class="form-check-input approvedImage" data-id="{{ $value->id }}" data-label="{{ $photo }}_status" type="radio" value="APPROVED" id="{{ $photo }}_status_like_{{ $value->id }}">
                                                        <img src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH').'/assets/img/images/like_radioicn.svg' }}" alt="APPROVE">
                                                    </label>
                                                </div>
                                                <div class="radio_DislikeBtn-sp mb-5">
                                                    <label for="{{ $photo }}_status_dislike_{{ $value->id }}" class="Dislike_lk">
                                                        <input name="{{ $photo }}_status_{{ $value->id }}" {{ $checkedNo }} class="form-check-input approvedImage" data-id="{{ $value->id }}" data-label="{{ $photo }}_status" type="radio" value="UNAPPROVED" id="{{ $photo }}_status_dislike_{{ $value->id }}">
                                                        <img src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH').'/assets/img/images/dislike_radioicon.svg' }}" alt="UNAPPROVE">
                                                    </label>
                                                </div>
                                            @endif
                                            <div class="text-center mt-1">
                                                @if (blank($value->$photo))
                                                    <span class="badge bg-label-dark me-1 mt-1">Not Uploaded</span>
                                                @else
                                                    @if ($status == 'APPROVED')
                                                        <span class="badge bg-label-success me-1 mt-1">APPROVED</span>
                                                    @endif
                                                    @if ($status == 'UNAPPROVED')
                                                        <span class="badge bg-label-danger me-1 mt-1">UNAPPROVED</span>
                                                    @endif
                                                    <p class="mt-1 fs-12"><span class="font-semiBold">Uploaded On :</span>{{ _displayNotAvailable(_displayDate($uploadedOn, 'j F, Y h:i A')) }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
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