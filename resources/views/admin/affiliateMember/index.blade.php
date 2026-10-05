@extends(_getConstant('dir_path.ADMIN_DIR_PATH').'.admin_layout')
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
@csrf
<input type="hidden" name="ajaxRequestUrl" id="ajaxRequestUrl" value="{{ route($ajaxPaginationRequestUrl) }}">
<input type="hidden" name="changeStatusUrl" id="changeStatusUrl" value="{{ route($changeStatusUrl) }}">
<input type="hidden" name="page" id="page" value="1">
@endsection
