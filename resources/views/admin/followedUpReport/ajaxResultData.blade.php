<!-- Member DataList -->
@if (isset($resultArr) && count($resultArr) > 0)
    @foreach ($resultArr as $key => $paginationData)
        <div class="inner_adProfileMian {{ $key > 0 ? 'mt-3' : '' }}">
            <div class="inner_flexDiv-membr d-block d-lg-flex gap-3">
                <div class="left_member_profilesDiv">
                    <div class="profile_userAdds position-relative">
                        @if (
                            !blank($paginationData->register->photo1) &&
                                _checkStorageFileExists('upload_path.MEMBER_PHOTOS_URL', $paginationData->register->photo1))
                            <img src="{{ _assetUrl('upload_path.MEMBER_PHOTOS_URL') . $paginationData->register->photo1 }}"
                                alt="Profile Image" class="user_profiles" loading="lazy">
                        @else
                            @php $profileImage = _getMemberDefaultImage($paginationData->register->gender); @endphp
                            <img src="{{ $profileImage }}" alt="Profile Images" class="user_profiles" loading="lazy">
                        @endif
                        <div class="check_Mprofiles">
                            <input type="checkbox" class="checkboxId form-check-input" id="ps<?php echo $paginationData->id; ?>"
                                name="id[]" value="<?php echo $paginationData->id; ?>">
                        </div>
                    </div>
                    <div
                        class="profile_btnGroups d-flex align-items-center justify-content-center gap-4 mt-2 pt-1 mb-2">
                    </div>
                    <a target="_blank" href="{{ route('admin.member.viewDetails', $paginationData->id) }}">
                        <button class="active_to_paid_edit_btnUsers">
                            <img src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/img/images/eyesIcon_addm.svg' }}"
                                alt="">&nbsp; View Profile
                        </button>
                    </a>
                </div>
                <div class="right_content_MDivd w-100">
                    <div class="top_usenr_btnsgroups d-block d-lg-flex align-items-center justify-content-between">
                        <div class="user_name_aprv d-flex align-items-center gap-2">
                            <a target="_blank"
                                href="{{ route('admin.member.viewDetails', $paginationData->register->id) }}">
                                <h4>
                                    {{ _displayNotAvailable($paginationData->register->matri_id) }}
                                </h4>
                            </a>

                            @if ($paginationData->register->status == 'APPROVED')
                                <button class="btn_aprunge btn_approved"><i
                                        class="bx bxs-like text-white"></i>Approved</button>
                            @elseif ($paginationData->register->status == 'UNAPPROVED')
                                <button class="btn_aprunge btn_unapproved"><i
                                        class="bx bxs-dislike text-white"></i>Unapproved</button>
                            @elseif ($paginationData->register->status == 'Suspended')
                                <button class="btn_aprunge btn_suspend"><i
                                        class="bx bx-block text-white"></i>Suspended</button>
                            @endif

                            @if ($paginationData->register->plan_status == 'Paid')
                                <button class="btn_aprunge btn_approved"><i
                                        class='bx bxs-credit-card text-white'></i>Paid</button>
                            @elseif ($paginationData->register->plan_status == 'Expired')
                                <button class="btn_aprunge btn_unapproved"><i
                                        class='bx bxs-credit-card text-white'></i>Expired</button>
                            @endif

                            @if ($paginationData->register->fstatus == 'Featured')
                                <button class="btn_AllGroup featured_btn " data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="" data-bs-original-title="Featured"
                                    aria-label="Featured">
                                    <i class='bx bxs-star text-white'></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="btm_users_details">
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-lg-6 col-md-6 col-sm-6 border-light-dtl mt-2">
                                <div class="details_userviewas">
                                    <h6><span>Full Name</span>: {{ _displayNotAvailable($paginationData->register->fullname) }}</h6>
                                    <h6><span>Email</span>: {{ _displayNotAvailable($paginationData->register->email) }}</h6>
                                    <h6><span>Date of Birth</span>: {{ _displayNotAvailable(_displayDate($paginationData->register->birthdate, 'j F, Y')) }}</h6>
                                    <h6><span>Plan Name</span>: {{ _displayNotAvailable($paginationData->register->plan_name) }}</h6>
                                    <h6><span>Last Login</span>: {{ _displayNotAvailable(_displayDate($paginationData->register->last_login ?? '', 'j F, Y h:i A')) }}</h6>
                                    <h6><span>Assign to Staff</span>: {{ _displayNotAvailable($paginationData->register->staff->username ?? '') }}</h6>
                                    <h6><span>Followup Date</span>: {{ _displayNotAvailable(_displayDate($paginationData->next_followup_date ?? '', 'j F, Y h:i A')) }}</h6>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-lg-6 col-md-6 col-sm-6 mt-2">
                                <div class="details_userviewas">
                                    <h6><span>Gender</span>: {{ _displayNotAvailable($paginationData->register->gender) }}</h6>
                                    <h6><span>Mobile Number</span>: {{ _displayNotAvailable($paginationData->register->mobile) }}</h6>
                                    <h6><span>Country</span>: {{ _displayNotAvailable($paginationData->register->countryData->country_name ?? '') }}</h6>
                                    <h6><span>Plan Expired On</span>: {{ _displayNotAvailable($paginationData->register->maritalStatusData->marital_status_name) }}</h6>
                                    <h6><span>Registered On</span>: {{ _displayNotAvailable(_displayDate($paginationData->register->created_at ?? '', 'j F, Y h:i A')) }}</h6>
                                    <h6><span>Assign to Franchise</span>: {{ _displayNotAvailable($paginationData->register->franchise->username ?? '') }}</h6>
                                    <h6><span>Commented On</span>: {{ _displayNotAvailable(_displayDate($paginationData->created_at ?? '', 'j F, Y h:i A')) }}</h6>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 mt-2">
                                <div class="details_userviewas">
                                    <h6 style="text-wrap: auto;">
                                        <span class="w-20">Comment</span>: {{ _displayNotAvailable($paginationData->comment ?? '') }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Bottom Button Here -->
                    <div class="bottom_usenr_btnsgroups d-block d-lg-flex align-items-center justify-content-between">
                        <div
                            class="right_dstGroup-btn d-block d-lg-flex d-md-flex d-sm-flex align-items-center gap-2 mt-2 mt-lg-0">
                            <!-- Match Making Count -->
                            <!-- Add Comment Here -->
                            @if ($dataArr->actionBtnArr['addComment'] == 1 || $dataArr->actionBtnArr['viewComment'] == 1)
                                <button class="member_comment_btn" data-bs-toggle="dropdown" aria-expanded="false"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Comments">
                                    <i class='bx bxs-chat text-white fs-3'></i>Comments
                                </button>
                                <ul class="dropdown-menu EmailsetTextd">
                                    @if (isset($dataArr->actionBtnArr['addComment']) && $dataArr->actionBtnArr['addComment'] == 1)
                                        <li>
                                            <a class="dropdown-item addComment" href="javascript:void(0)"
                                                data-id="{{ $paginationData->register->id }}"
                                                data-action="{{ route($dataArr->actionButtonUrl['addComment']) }}"
                                                target-modal="#add_commentModal">Add Comments
                                            </a>
                                        </li>
                                    @endif
                                    @if (isset($dataArr->actionBtnArr['viewComment']) && $dataArr->actionBtnArr['viewComment'] == 1)
                                        <li>
                                            <a class="dropdown-item viewComment" href="javascript:void(0)"
                                                data-id="{{ $paginationData->register->id }}"
                                                data-action="{{ route($dataArr->actionButtonUrl['viewComment']) }}"
                                                target-modal="#view_commentModal">View Comments
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            @endif
                            <!-- Add Comment Here -->
                        </div>
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
@include('admin.commonPagination', ['dataArr' => $resultArr])
