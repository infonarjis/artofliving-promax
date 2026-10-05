@php
    $authUserType = Auth::user()->type;
@endphp
<div class="members_brand-topMain">
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
    <div class="row">
        <div class="col-lg-6">
            @if (isset($actionBtnArr['add']) && $actionBtnArr['add'] == 1)
                <div class="add_details_icons d-flex align-items-center gap-2">
                    <a href="{{ route($actionButtonUrl['add']) }}" class="">
                        <button class="add_member_btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Add New">
                            <i class="bx bx-plus text-white fs-3"></i>Add New
                        </button>
                    </a>
                </div>
            @endif
            @if (isset($actionBtnArr['personalizeMeetingAdd']) && $actionBtnArr['personalizeMeetingAdd'] == 1)
                <div class="add_details_icons d-flex align-items-center gap-2">
                    <a href="{{ route('admin.personalizeMeeting.addForm',$personalizeMatchId) }}" class="">
                        <button class="add_member_btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Add New">
                            <i class="bx bx-plus text-white fs-3"></i>Add New
                        </button>
                    </a>
                </div>
            @endif
        </div>
        <div class="col-lg-6 position-relative text-end">
            @if (isset($actionBtnArr['importLead']) && $actionBtnArr['importLead'] == 1)
                <div class="btn-group">
                    <a href="{{ route('admin.leadGeneration.importLead') }}">
                        <button class="cmn_btnGroup import_lead_btn"><i class='bx bx-cloud-upload' ></i>Import Lead</button>
                    </a>
                </div>
            @endif
            @if (isset($actionBtnArr['downloadBtn']) && $actionBtnArr['downloadBtn'] == 1)
                <div class="btn-group">
                    <button type="button" class=" export-file btn btn-label-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class='bx bx-down-arrow-circle'></i>Downloads
                    </button>
                    <ul class="dropdown-menu wz" style="">
                        @foreach($downloadDropdownArr as $key=>$value)
                            <li><a class="dropdown-item" href="{{ $value['route'] }}"><i class='{{ $value['icon'] }}'></i>  {{ $value['label'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (isset($actionBtnArr['filter']) && $actionBtnArr['filter'] == 1)
            <div class="btn-group">
                <button class="cmn_btnGroup admin_filter_other getfilterModal" target-modal="#filterModal"
                data-action="{{ route($actionButtonUrl['filter']) }}"><i class='bx bxs-filter-alt'></i>
                Filter</button>
            </div>
            @endif
        </div>
    </div>
    @if($authUserType == 'Admin')
        @if (isset($actionBtnArr['staffAssign']) && $actionBtnArr['staffAssign'] == 1)
            <div class="right_top_buttonsGroups mt-2">
                <div class="row">
                    <div class="col-lg-2 col-md-6 col-sm-6 col-6 px-1">
                        <select class="select_staf_fran" id="staffAdminId">
                            <option value="">Select Staff</option>
                            @if (!empty($staffListArr))
                                @foreach ($staffListArr as $staffKey=>$staffValue)
                                    <option data-type="1" value="{{ $staffValue->id }}">{{ $staffValue->username }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6 col-sm-6 col-6 px-1">
                        <button class="cmn_btnGroup btn_assignstfr"
                            data-action="{{ route($actionButtonUrl['staffAssignbtn']) }}" id="assignStaffMember"
                            title="Assign Staff">Assign Staff</button>
                    </div>
                    <div class="col-lg-2 col-md-6 col-sm-6 col-6 px-1">
                        @if (isset($actionBtnArr['staffUnAssign']) && $actionBtnArr['staffUnAssign'] == 1)
                            <button class="cmn_btnGroup btn_unassignstfr w-px-170"
                            data-action="{{ route($actionButtonUrl['staffUnAssignbtn']) }}" id="unassignStaffMember"
                            title="Unassign Staff">Unassigned Staff</button>
                        @endif
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-6 px-1"></div>
                </div>
            </div>
        @endif
        @if (isset($actionBtnArr['franchiseAssign']) && $actionBtnArr['franchiseAssign'] == 1)
            <div class="right_top_buttonsGroups mt-2">
                <div class="row">
                    <div class="col-lg-2 col-md-6 col-sm-6 col-6 px-1">
                        <select class="select_staf_fran" id="franchiseSubAdminId">
                            <option value="">Select Franchise</option>
                            @if (!empty($franchiseListArr))
                            @foreach ($franchiseListArr as $key=>$valueArr)
                            <option data-type="1" value="{{ $valueArr->id }}">{{ $valueArr->username }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6 col-sm-6 col-6 px-1">
                        <button class="cmn_btnGroup btn_assignstfr"
                            data-action="{{ route($actionButtonUrl['franchiseAssignbtn']) }}" id="assignFranchise"
                            title="Assign Franchise">Assign Franchise</button>
                    </div>
                    <div class="col-lg-2 col-md-6 col-sm-6 col-6 px-1">
                        @if (isset($actionBtnArr['franchiseUnAssign']) && $actionBtnArr['franchiseUnAssign'] == 1)
                            <button class="cmn_btnGroup btn_unassignstfr w-px-170"
                            data-action="{{ route($actionButtonUrl['franchiseUnAssignbtn']) }}" id="unassignFranchise"
                            title="Unassign Franchise">Unassigned Franchise</button>
                        @endif
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-6 px-1"></div>
                </div>
            </div>
        @endif
    @endif
    @if (isset($actionBtnArr['changeInterest']) && $actionBtnArr['changeInterest'] == 1)
        <div class="right_top_buttonsGroups mt-2">
            <div class="row">
                <div class="col-lg-2 col-md-6 col-sm-6 col-6 px-1">
                    <select class="select_staf_fran" id="interestValue">
                        <option value="">Select Interest</option>
                        @if (!empty($interestList))
                            @foreach ($interestList as $interestKey=>$interestValue)
                            <option value="{{ $interestValue }}">{{ $interestValue }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6 col-6 px-1">
                    <button class="cmn_btnGroup btn_changeInterest"
                        data-action="{{ route($actionButtonUrl['changeInterestbtn']) }}" id="changeInterest"
                        title="Change Interest">Change Interest</button>
                </div>
                <div class="col-lg-8 col-md-6 col-sm-6 col-6 px-1"></div>
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-lg-4"></div>
        <div class="col-lg-8">
            @if (isset($actionBtnArr['isAssign']) && $actionBtnArr['isAssign'] == 1)
            <div class="right_top_buttonsGroups">
                <div class="row">
                    <div class="col-lg-1 col-md-6 col-sm-6 col-6 px-1"></div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-6 px-1">
                        <select class="select_staf_fran" id="subAdminFranchiseId">
                            <option value="">Select</option>
                            @if (!empty($franchiseListArr))
                            @foreach ($franchiseListArr as $key=>$valueArr)
                            <option data-type="1" value="{{ $valueArr->id }}">{{ $valueArr->username }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-6 px-1">
                        <button class="cmn_btnGroup btn_assignstfr"
                            data-action="{{ route('admin.member.assignMember') }}" id="assignFranchise"
                            title="Assign Franchise">Assign Franchise</button>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-6 px-1">
                        <button class="cmn_btnGroup btn_unassignstfr w-px-170"
                            data-action="{{ route('admin.member.unAssignMember') }}" id="unassignFranchise"
                            title="Unassign Franchise">Unassigned Franchise</button>
                    </div>
                    <div class="col-lg-2 col-md-6 col-sm-6 col-6 px-1"></div>
                </div>
            </div>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-lg-1 col-md-4 col-sm-6 mt-3 mt-lg-3">
            <div class="left_sel_optobsads">
                <select name="basic-datatables_length" id="recordLimit" class="number_slec">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="5">5</option>
                    <option selected value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
        <div class="col-lg-8 col-md-4 col-sm-6 mt-3 mt-lg-3">
            <div class="top_heading_btnGroups d-flex justify-content-center justify-content-lg-start gap-3">
                @if (isset($actionBtnArr['delete']) && $actionBtnArr['delete'] == 1)
                <button class="btn_delete actionBtn" isConfirm="1" data-column="is_deleted" data-value="Yes"
                    data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                    <i class="bx bxs-trash-alt text-white"></i>
                </button>
                @endif
                @if (isset($actionBtnArr['approve']) && $actionBtnArr['approve'] == 1)
                <button class="fw-bold mb-4 btn btn-success actionBtn" data-column="id_proof_status" data-value="APPROVED"
                    data-bs-toggle="tooltip" data-bs-placement="top" title="Approve">
                    <i class='bx bxs-like text-white'></i>
                </button>
                @endif
                @if (isset($actionBtnArr['unapprove']) && $actionBtnArr['unapprove'] == 1)
                <button class="btn_dislike actionBtn" data-column="id_proof_status" data-value="UNAPPROVED"
                    data-bs-toggle="tooltip" data-bs-placement="top" title="Unapprove">
                    <i class='bx bxs-dislike text-white'></i>
                </button>
                @endif
                @if (isset($actionBtnArr['suspend']) && $actionBtnArr['suspend'] == 1)
                <button class="btn_block actionBtn" data-column="status" data-value="Suspended" data-bs-toggle="tooltip"
                    data-bs-placement="top" title="Suspend">
                    <i class='bx bx-block text-white'></i>
                </button>
                @endif
                @if (isset($actionBtnArr['reportTicket']) && $actionBtnArr['reportTicket'] == 1)
                    <button class="ticket_reopen_btn actionBtn" data-column="status" data-value="Reopen" data-bs-toggle="tooltip"
                        data-bs-placement="top" title="Reopen">
                        <i class='bx bx-window-open'></i> Reopen
                    </button>
                @endif
                @if (isset($actionBtnArr['closeTicket']) && $actionBtnArr['closeTicket'] == 1)
                    <button class="ticket_close_btn actionBtn" data-column="status" data-value="Close" data-bs-toggle="tooltip"
                        data-bs-placement="top" title="Close">
                        <i class='bx bx-window-close'></i> Close
                    </button>
                @endif
                @if (isset($actionBtnArr['fstatus']) && $actionBtnArr['fstatus'] == 1)
                    <button class="btn_fstatus-sd actionBtn" data-column="fstatus" data-value="Featured"
                        data-bs-toggle="tooltip" data-bs-placement="top" title="Featured">
                        <i class='bx bxs-star text-white' ></i>
                    </button>
                    <button class="btn_unfstatus-sd actionBtn" data-column="fstatus" data-value="Unfeatured"
                        data-bs-toggle="tooltip" data-bs-placement="top" title="Remove Featured">
                        <i class='bx bx-star text-white' ></i>
                    </button>
                @endif
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-12 mt-3 mt-lg-3">
            @if (isset($actionBtnArr['isSearch']) && $actionBtnArr['isSearch'] == 1)
            <div class="result_searchdash position-relative">
                <input class="form-control me-2" id="searchText" type="search" placeholder="Search" aria-label="Search" />
                <button class="btn_search searchMainBtn" id="commonSearch" type="submit">
                    <img src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH').'/assets/img/images/h-searchIcon.svg' }}" alt="">
                </button>
            </div>
            @endif
        </div>
    </div>
</div>
