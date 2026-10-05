@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')
@section('admin_content')
    <div class="container-xxl flex-grow-1 container-p-y">
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
        @include('admin.actionButtons')
        @include('admin.message')

        <!-- Basic Layout -->
        <div class="card mt-3">
            <div class="row">
                <div class="col-lg-12 col-md-4 col-sm-12 mt-2 mt-lg-2 mb-1 px-md-4">
                    <div class="row">
                        <div class="col-lg-12 col-md-6 col-sm-12 mt-3 mt-lg-0 mt-md-0">
                            <div class="tabListing_topnav">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item" role="">
                                        <button class="tabClick nav-link active" data-conditionval=""
                                            data-conditioncolumn="" id="allData" type="button">
                                            All (<label class="countRecord">0</label>)
                                        </button>
                                    </li>
                                    <li class="nav-item" role="">
                                        <button class="tabClick nav-link" data-conditionval="APPROVED"
                                            data-conditioncolumn="status" id="approvedData" type="button">
                                            Approved (<label class="countRecord">0</label>)
                                        </button>
                                    </li>
                                    <li class="nav-item" role="">
                                        <button class="tabClick nav-link" data-conditionval="UNAPPROVED"
                                            data-conditioncolumn="status" id="unapprovedData" type="button">
                                            Unapproved (<label class="countRecord">0</label>)
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive text-nowrap" id="resultData">

            </div>
        </div>
    </div>
    @csrf
    <input type="hidden" id="ajaxRequestUrl" value="{{ $ajaxPaginationRequestUrl }}">
    <input type="hidden" id="changeStatusUrl" value="{{ $changeStatusUrl }}">
    <input type="hidden" name="page" id="page" value="1">
@endsection
