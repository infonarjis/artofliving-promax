<!-- Member DataList -->
@if (isset($resultArr) && count($resultArr) > 0)
    @foreach ($resultArr as $key => $dataValue)
        <div class="inner_adProfileMian {{ $key > 0 ? 'mt-3' : '' }}">
            <div class="inner_flexDiv-membr d-block d-lg-flex gap-3">
                <div class="right_content_MDivd w-100">
                    <div class="top_usenr_btnsgroups d-block d-lg-flex align-items-center justify-content-between">
                        <div class="user_name_aprv d-flex align-items-center gap-2">
                            <input type="checkbox" class="checkboxId form-check-input" id="ps<?php echo $dataValue->id; ?>"
                                name="id[]" value="<?php echo $dataValue->id; ?>">
                            <h4>{{ _displayNotAvailable($dataValue->leadGeneration->username)  }}</h4>
                            <div class="user_name_aprv d-flex align-items-center gap-2">
                                {{-- <button class="btn_aprunge btn_approved"> {{ $dataValue->interest }}</button> --}}
                            </div>
                        </div>
                    </div>

                    <div class="btm_users_details">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 border-light-dtl mt-2 pb-0 pb-lg-2 pb-md-2 pb-sm-2">
                                <div class="details_userviewas">
                                    <h6><span>Gender</span>: {{ _displayNotAvailable($dataValue->leadGeneration->gender) }}</h6>
                                    <h6><span>Mobile Number</span>:
                                        @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                            {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                        @else
                                            {{ _displayNotAvailable($dataValue->leadGeneration->phone_no_1) }}
                                        @endif
                                    </h6>
                                    <h6><span>Assign To Staff</span>:
                                        {{ _displayNotAvailable(optional($dataValue->leadGeneration->staff)->username) }}    
                                    </h6>
                                    
                                    <h6><span>Created On</span>:
                                        {{ _displayNotAvailable(_displayDate($dataValue->leadGeneration->created_at, 'j F, Y h:i A')) }}
                                    </h6>
                                    <h6><span>Comment</span>:
                                        {{ _displayNotAvailable($dataValue->comment) }}
                                    </h6>
                                </div>
                            </div>
                            <div
                                class="col-lg-6 col-md-6 col-sm-6 sec mt-0 mt-lg-2 mt-md-2 mt-sm-2 pb-0 pb-lg-2 pb-md-2 pb-sm-2">
                                <div class="details_userviewas">
                                    <h6><span>Email</span>: {{ _displayNotAvailable($dataValue->leadGeneration->email) }}</h6>
                                    <h6><span>Country Name</span>: {{ _displayNotAvailable($dataValue->leadGeneration->country) }}</h6>
                                    <h6><span>Assign To Franchise</span>:
                                        {{ _displayNotAvailable(optional($dataValue->leadGeneration->franchise)->username) }}
                                    </h6>
                                    <h6><span>Followup Date</span>:
                                        {{ _displayNotAvailable(_displayDate($dataValue->next_followup_date, 'j F, Y h:i A')) }}
                                    </h6>
                                    <h6><span>Commented Date</span>:
                                        {{ _displayNotAvailable(_displayDate($dataValue->created_at, 'j F, Y h:i A')) }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Bottom Button Here -->
                    <div class="bottom_usenr_btnsgroups d-block d-lg-flex align-items-center justify-content-between">
                        <div
                            class="right_dstGroup-btn d-block d-lg-flex d-md-flex d-sm-flex align-items-center gap-2 mt-2 mt-lg-0">
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
                                                data-id="{{ $dataValue->leadGeneration->id }}"
                                                data-action="{{ route($dataArr->actionButtonUrl['addComment']) }}"
                                                target-modal="#add_commentModal">Add Comments
                                            </a>
                                        </li>
                                    @endif
                                    @if (isset($dataArr->actionBtnArr['viewComment']) && $dataArr->actionBtnArr['viewComment'] == 1)
                                        <li>
                                            <a class="dropdown-item viewComment" href="javascript:void(0)"
                                                data-id="{{ $dataValue->leadGeneration->id }}"
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
@include('admin.commonPagination')
