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
    @include('admin.actionButtons')
    @include('admin.message')
    <!-- Language Change Popup -->
    <div class="modal fade" id="languageChangeModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    </div>
    <!-- Language Change Popup -->

    <!-- Basic Layout -->
    <div class="card mt-3">
        <h6 class="card-header"></h6>
        <div class="table-responsive text-nowrap" id="resultData">
            
        </div>
    </div>
</div>
@csrf
<input type="hidden" name="ajaxRequestUrl" id="ajaxRequestUrl" value="{{ route($ajaxPaginationRequestUrl) }}">
<input type="hidden" name="changeStatusUrl" id="changeStatusUrl" value="{{ route($changeStatusUrl) }}">
<input type="hidden" name="page" id="page" value="1">
<input type="hidden" name="languageId" id="languageId" value="{{ $languageId }}">
@endsection
