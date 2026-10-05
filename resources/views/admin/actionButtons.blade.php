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
            @if (isset($actionBtnArr['downloadBtn']) && $actionBtnArr['downloadBtn'] == 1 && $authUserType == 'Admin')
                <div class="btn-group">
                    <button type="button" class=" export-file btn btn-label-primary" data-bs-toggle="modal" data-bs-target="#downloadFormatModal" aria-expanded="false">
                        <i class='bx bx-down-arrow-circle'></i> Downloads
                    </button>
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
                    <option value="5">5</option>
                    <option selected value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="250">250</option>
                    <option value="500">500</option>
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
                <button class="fw-bold mb-4 btn btn-success actionBtn" data-column="status" data-value="APPROVED"
                    data-bs-toggle="tooltip" data-bs-placement="top" title="Approve">
                    <i class='bx bxs-like text-white'></i>
                </button>
                @endif
                @if (isset($actionBtnArr['unapprove']) && $actionBtnArr['unapprove'] == 1)
                <button class="btn_dislike actionBtn" data-column="status" data-value="UNAPPROVED"
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
                @if (isset($actionBtnArr['is_copy_access_link']) && $actionBtnArr['is_copy_access_link'] == 1)
                    <input type="hidden" id="copyText1" value="{{ $actionButtonUrl['copyaccessLinkVal'] }}">
                    <button class="add_member_btn w-25 gap-2" onclick="copyToClipboard();" data-bs-toggle="tooltip" data-bs-placement="top" title="Access Link">
                        <i class='bx bx-copy-alt' ></i>Copy Access Link
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
@if (isset($actionBtnArr['downloadBtn']) && $actionBtnArr['downloadBtn'] == 1 && $authUserType == 'Admin')
    <div class="modal fade" id="downloadFormatModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered download-file-modal">
            <div class="modal-content modelcont-ctms p-4 position-relative">
                <div class="heading-modal">
                    <h1 class="modal-title" id="exampleModalLabel">Download Reports</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class='bx bx-x'></i>
                    </button>
                </div>
                <div class="bottom_saveTimebsg mt-4">
                    <form id="authenticationForm" name="authenticationForm" action="{{ $downloadDropdownArr['route'] }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-lg-12 col-md-12">
                                <div class="edit_inputMain-sltr">
                                    <div class="mb-3 ">
                                        <label class="form-label" for="filedownloadDate">Select Date Range</label>
                                        <input type="text" id="filedownloadDate" class="form-control" name="filedownloadDate" value="" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <div class="edit_inputMain-sltr">
                                    <div class="mb-3 ">
                                        <label class="form-label" for="">Download Format </label>
                                        <div class="radio_mainDivGroups">
                                            @foreach($downloadDropdownArr['downloadType'] as $key => $value)
                                                <div class="form-check d-inline-block me-3 mt-2">
                                                    <input checked="" name="downloadFormat" id="{{$value}}" class="form-check-input" type="radio" value="{{$value}}">
                                                    <label class="form-label" for="{{$value}}">{{$value}}</label> &nbsp;
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4">
                            <button type="submit" class="submit-btn-mmbre mt-2 btn btn-primary exportFiles" id="exportFiles">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
