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
        @include('admin.message')
        <!-- Basic Layout -->
        {{-- Date Range Filter Section --}}
        @php
            $callFromDb = !empty($getDateRange['call_from'])
                ? \Carbon\Carbon::parse($getDateRange['call_from'])->format('Y-m-d H:i:s')
                : '';
            $callToDb = !empty($getDateRange['call_to'])
                ? \Carbon\Carbon::parse($getDateRange['call_to'])->format('Y-m-d H:i:s')
                : '';
            $callFromDisplay = $callFromDb ? \Carbon\Carbon::parse($callFromDb)->format('d/m/Y H:i') : '';
            $callToDisplay = $callToDb ? \Carbon\Carbon::parse($callToDb)->format('d/m/Y H:i') : '';
        @endphp
        <div class="mt-3">
            <div class="row align-items-end">
                {{-- Left spacer --}}
                <div class="col-lg-8 col-md-8 col-sm-8 mt-2 mb-1 px-md-4"></div>
                {{-- Date Range Picker --}}
                <div class="col-lg-4 col-md-4 col-sm-4 mt-2 mb-1 px-md-4">
                    <div class="left_select_drpstrings">
                        <div class="edit_inputMain-sltr">
                            <div class="mb-3">
                                <label class="form-label" for="daterange">Call From – Call To</label>
                                <div class="input-group">
                                    <button type="button" class="btn btn-icon btn-primary" title="Clear date filter">
                                        <span class="icon-base bx bxs-calendar icon-md"></span>
                                    </button>
                                    <input id="daterange" class="form-control bg-white" name="daterange" type="text"
                                        autocomplete="off" placeholder="dd/mm/yyyy hh:mm – dd/mm/yyyy hh:mm"
                                        value="{{ $callFromDisplay && $callToDisplay ? $callFromDisplay . ' - ' . $callToDisplay : '' }}"
                                        readonly>
                                    <button type="button" id="clearDateRange" class="btn btn-icon btn-outline-primary"
                                        title="Clear date filter" style="display:none;">
                                        <span class="icon-base bx bx-x icon-md"></span>
                                    </button>
                                </div>

                                {{-- Hidden DB-safe values sent with AJAX --}}
                                <input type="hidden" id="callFrom" value="{{ $callFromDb }}">
                                <input type="hidden" id="callTo" value="{{ $callToDb }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="" id="resultData">

        </div>
    </div>
    @csrf
    <input type="hidden" name="ajaxRequestUrl" id="ajaxRequestUrl" value="{{ route($ajaxPaginationRequestUrl) }}">
    <input type="hidden" name="page" id="page" value="1">
@endsection
