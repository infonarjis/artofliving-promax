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
                            @include('admin.commonTab')
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive text-nowrap" id="resultData">

            </div>
        </div>
    </div>

    <!-- Add Admin Remarks modal popup section start  -->
    <div class="modal fade" id="addAdminRemarksModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered cstm-withset">
            <div class="modal-content modelcont-ctms p-4 position-relative">
                <div class="heading-modal">
                    <h1 class="modal-title" id="exampleModalLabel">Add Remarks</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i
                            class='bx bx-x'></i></button>
                </div>
                <form id="addMemberCommentForm" method="POST"
                    action="{{ route('admin.affiliateMemberPayment.addAdminRemarks') }}">
                    <div class="ckediterbox my-3">
                        <textarea required name="admin_remark" id="admin_remark" class="form-control form-control-1" rows="2"
                            placeholder="Admin Remarks"></textarea>
                    </div>
                    <div class="bottom_saveTimebsg mt-4">
                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-8"></div>
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                @csrf
                                <input type="hidden" name="followup_id" id="followup_id" value="">
                                <button type="button" id="addMemberCommentSubmit"
                                    class="submit-btn-mmbre mt-4 d-flex">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @csrf
    <input type="hidden" name="ajaxRequestUrl" id="ajaxRequestUrl" value="{{ route($ajaxPaginationRequestUrl) }}">
    <input type="hidden" name="page" id="page" value="1">
@endsection
