<!-- Member DataList -->
@if (isset($resultArr) && count($resultArr) > 0)
    @foreach($resultArr as $key => $paginationData)
        <div class="inner_adProfileMian {{ ($key > 0) ? 'mt-3' : '' }}">
            <div class="inner_flexDiv-membr d-block d-lg-flex gap-3">
                <div class="left_member_profilesDiv">
                    <div class="profile_userAdds position-relative">
                        @if (isset($resultArr->displayKeyArr) && $resultArr->displayKeyArr['photo'] != '')
                            @foreach ($resultArr->displayKeyArr['photo'] as $imgKey => $imgVal)
                                @if (!blank($paginationData->$imgKey) && _checkStorageFileExists($imgVal['imageDirPath'],$paginationData->$imgKey))
                                    <img src="{{ _assetUrl($imgVal['imageDirPath']).$paginationData->$imgKey }}" alt="" class="user_profiles">
                                @else
                                    @php $profileImage = _getMemberDefaultImage($paginationData->gender); @endphp
                                    <img src="{{ $profileImage }}" alt="" class="user_profiles">
                                @endif
                            @endforeach
                        @endif
                        <div class="check_Mprofiles">
                            <input type="checkbox" class="checkboxId form-check-input" id="ps<?php echo $paginationData->id; ?>" name="id[]" value="<?php echo $paginationData->id; ?>">
                        </div>
                    </div>
                    <div class="profile_btnGroups d-flex align-items-center justify-content-center gap-4 mt-2 pt-1 mb-2">
                        <a href="{{ route($resultArr->actionButtonUrl['view'],$paginationData->id) }}">
                            <button class="btn_seenProfile" data-bs-toggle="tooltip" data-bs-placement="bottom" title="View">
                                <img src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH').'/assets/img/images/eyesIcon_addm.svg' }}" alt="">
                            </button>
                        </a>
                        <a href="{{ route($resultArr->actionButtonUrl['edit'],$paginationData->id) }}">
                            <button class="btn_editprofile" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit">
                                <img src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH').'/assets/img/images/editIcon_addm.svg' }}" alt="">
                            </button>
                        </a>
                    </div>
                    <a href="{{ route($resultArr->actionButtonUrl['editPlan'],$paginationData->id) }}">
                        <?php
                            $planTitle = 'Active To Paid';
                            $class= 'active_to_paid_edit_btnUsers';
                            if ($paginationData->plan_status == 'Paid') {
                                $planTitle = 'Edit Plan';
                                $class= 'edit_btnUsersPlan';
                            }

                            if ($paginationData->plan_status == 'Expired') {
                                $planTitle = 'Renewal Plan';
                                $class= 'active_to_paid_edit_btnUsers';
                            }
                        ?>
                        <button class="{{$class}}">
                            <img src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH').'/assets/img/images/editIconmmbb.svg' }}" alt="">&nbsp; {{$planTitle}}
                        </button>
                    </a>
                </div>
                <div class="right_content_MDivd w-100">
                    <div class="top_usenr_btnsgroups d-block d-lg-flex align-items-center justify-content-between">
                        <div class="user_name_aprv d-flex align-items-center gap-2">
                            @if (isset($resultArr->displayKeyArr) && $resultArr->displayKeyArr['title'] != '')
                                @foreach ($resultArr->displayKeyArr['title'] as $titleKey =>$titleVal)
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
                                <button class="btn_aprunge btn_approved"><i class="bx bxs-like text-white"></i>Approved</button>
                            @elseif ($paginationData->status == 'UNAPPROVED')
                                <button class="btn_aprunge btn_unapproved"><i class="bx bxs-dislike text-white"></i>Unapproved</button>
                            @elseif ($paginationData->status == 'Suspended')
                                <button class="btn_aprunge btn_suspend"><i class="bx bx-block text-white"></i>Suspended</button>
                            @endif

                            @if ($paginationData->plan_status == 'Paid')
                                <button class="btn_aprunge btn_approved"><i class='bx bxs-credit-card text-white'></i>Paid</button>
                            @elseif ($paginationData->plan_status == 'Expired')
                                <button class="btn_aprunge btn_unapproved"><i class='bx bxs-credit-card text-white'></i>Expired</button>
                            @endif

                            @if ($paginationData->fstatus == 'Featured')
                                <button class="btn_AllGroup featured_btn " data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                    data-bs-original-title="Featured" aria-label="Featured">
                                    <i class='bx bxs-star text-white'></i>
                                </button>
                            @endif
                        </div>
                        <div class="right_dstGroup-btn d-block d-lg-flex d-md-flex d-sm-flex align-items-center gap-2 mt-2 mt-lg-0">
                            @if (isset($paginationData->email_verify_status) && $paginationData->email_verify_status != 'Verify')
                                @if (isset($resultArr->actionBtnArr['confirmEmail']) && $resultArr->actionBtnArr['confirmEmail'] == 1)
                                    <button class="btn_matchcount sendConfirmationEmail" data-id="{{ base64_encode($paginationData->id) }}" data-action="{{ route($resultArr->actionButtonUrl['confirmationEmail']) }}">
                                        <img src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH').'/assets/img/images/re_mailicn.svg' }}" alt="Confirmation Email">Send Confirmation Email
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div class="btm_users_details">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 border-light-dtl mt-2 pb-0 pb-lg-2 pb-md-2 pb-sm-2">
                                <div class="details_userviewas">
                                    @if (isset($resultArr->displayKeyArr) && $resultArr->displayKeyArr['field'] != '')
                                        @if ($resultArr->displayKeyArr['field']['left'] != '')
                                            @foreach ($resultArr->displayKeyArr['field']['left'] as $leftKey =>$leftVal)
                                                <h6>
                                                    <span>
                                                        @if (isset($leftVal['label']) && $leftVal['label'] !='')
                                                            {{ $leftVal['label'] }}
                                                        @else
                                                            {{ _createLabel($leftKey) }}
                                                        @endif
                                                    </span>:

                                                    @if ($leftVal['type'] == 'str')
                                                        @php
                                                            $leftKeyStr = Str::limit($paginationData->$leftKey, 22);
                                                        @endphp
                                                        {{ _displayNotAvailable(strip_tags($leftKeyStr)) }}
                                                    @elseif ($leftVal['type'] == 'date')
                                                        {{ _displayNotAvailable(_displayDate($paginationData->$leftKey, 'j F, Y')) }}
                                                    @elseif ($leftVal['type'] == 'birthdate')
                                                        {{ _displayNotAvailable(_displayDate($paginationData->$leftKey, 'j F, Y')) }} ({{ _displayNotAvailable(_birthdateDisplay($paginationData->birthdate,0)) }})
                                                    @elseif ($leftVal['type'] == 'age')
                                                        {{ _displayNotAvailable(_birthdateDisplay($paginationData->birthdate,0)) }}
                                                    @elseif ($leftVal['type'] == 'height')
                                                        {{ _displayNotAvailable(_displayHeight($paginationData->$leftKey)) }}
                                                    @endif
                                                </h6>
                                            @endforeach
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 sec mt-0 mt-lg-2 mt-md-2 mt-sm-2 pb-0 pb-lg-2 pb-md-2 pb-sm-2">
                                <div class="details_userviewas">
                                    @if (isset($resultArr->displayKeyArr) && $resultArr->displayKeyArr['field'] != '')
                                    <!-- ## Check Center Side Key :  -->
                                        @if ($resultArr->displayKeyArr['field']['center'] != '')
                                            @foreach ($resultArr->displayKeyArr['field']['center'] as $centerKey =>$centerVal)
                                                <h6>
                                                    <span>
                                                        @if (isset($centerVal['label']) && $centerVal['label'] !='')
                                                            {{ $centerVal['label'] }}
                                                        @else
                                                            {{ _createLabel($centerKey) }}
                                                        @endif
                                                    </span>:

                                                    @if ($centerVal['type'] == 'str')
                                                        @if(_getConstant('DISABLE_DEMO') ==  'Enabled' && $centerKey == 'email')
                                                            {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                                        @elseif(_getConstant('DISABLE_DEMO') ==  'Enabled' && $centerKey == 'mobile')
                                                            {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                                        @else
                                                            @php $centerKeyStr = Str::limit($paginationData->$centerKey, 22); @endphp
                                                            {{ _displayNotAvailable(strip_tags($centerKeyStr)) }}
                                                        @endif
                                                    @elseif ($centerVal['type'] == 'date')
                                                        {{ _displayNotAvailable(_displayDate($paginationData->$centerKey, 'j F, Y')) }}
                                                    @elseif ($centerVal['type'] == 'age')
                                                        {{ _birthdateDisplay($paginationData->birthdate,0) }}
                                                    @elseif ($centerVal['type'] == 'height')
                                                        {{ _displayNotAvailable(_displayHeight($paginationData->$centerKey)) }}
                                                    @elseif ($centerVal['type'] == 'user_type')
                                                        @if($paginationData->$centerKey == 0)
                                                            <span class="badge rounded-pill bg-dark w-20">Online</span>
                                                        @else
                                                            <span class="badge rounded-pill bg-info w-25">Personalize</span>
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
                        <div class="right_dstGroup-btn d-block d-lg-flex d-md-flex d-sm-flex align-items-center gap-2 mt-2 mt-lg-0">
                            <!-- Download Biodata Btn -->
                            @if (isset($resultArr->actionBtnArr['downloadBiodatabtn']) && $resultArr->actionBtnArr['downloadBiodatabtn'] == 1)
                                <a href="{{ route('admin.member.downloadBiodataPdf',base64_encode($paginationData->id)) }}">
                                    <button class="member_download_biodata_btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Download Biodata">
                                        <i class='bx bxs-cloud-download text-white fs-3'></i>Download Biodata
                                    </button>
                                </a>
                            @endif
                            <!-- Download Biodata Btn-->
                            <!-- Match Making Count -->
                            <div class="center_bx-line"></div>
                            <!-- Add Comment Here -->
                            @if ($resultArr->actionBtnArr['addComment'] == 1 || $resultArr->actionBtnArr['viewComment'] == 1)
                                <button class="member_comment_btn" data-bs-toggle="dropdown" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="top" title="Comments">
                                    <i class='bx bxs-chat text-white fs-3'></i>Comments
                                </button>
                                <ul class="dropdown-menu EmailsetTextd">
                                    @if (isset($resultArr->actionBtnArr['addComment']) && $resultArr->actionBtnArr['addComment'] == 1)
                                        <li>
                                            <a class="dropdown-item addComment" href="javascript:void(0)"
                                                data-id="{{ $paginationData->id }}"
                                                data-action="{{ route($resultArr->actionButtonUrl['addComment']) }}"
                                                target-modal="#add_commentModal">Add Comments
                                            </a>
                                        </li>
                                    @endif
                                    @if (isset($resultArr->actionBtnArr['viewComment']) && $resultArr->actionBtnArr['viewComment'] == 1)
                                        <li>
                                            <a class="dropdown-item viewComment" href="javascript:void(0)"
                                                data-id="{{ $paginationData->id }}"
                                                data-action="{{ route($resultArr->actionButtonUrl['viewComment']) }}"
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
@include('admin.commonPagination')

