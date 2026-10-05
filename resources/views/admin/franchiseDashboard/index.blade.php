@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')
@section('admin_content')
    @php
        $authUser = Auth::user();
        $userType = _adminUserType($authUser->type);
    @endphp
    <!-- Toast with Placements -->
    <div class="bs-toast toast toast-placement-ex m-2" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2000">
        <div class="toast-header">
            <i class="bx bx-bell me-2"></i>
            <div class="me-auto fw-semibold toast-title">Bootstrap</div>
            <small>Now</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">Fruitcake chocolate bar tootsie roll gummies gummies jelly beans cake.</div>
    </div>
    <!-- Toast with Placements -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="dashboard-card mb-2">
            <div class="row">
                <div class="col-md-12 col-lg-3 order-2">
                    <div class="">
                        <select id="franchise_id" name="franchise_id" class="form-select">
                            <option value="">Select Franchise</option>
                            @foreach ($franchiseListArr as $value)
                                <option value="{{ $value->id }}">{{ $value->username }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-12 col-lg-3 order-2">
                    <div class="">
                        <input class="form-control required" name="daterange" type="text"
                            placeholder="dd/mm/yyyy - dd/mm/yyyy">
                    </div>
                </div>
            </div>
        </div>
        <div class="dashboard-card">
            <div class="row">
                <div class="col-md-12 col-lg-12 order-2 d-none" id="showfranchiseDetails">
                    <div class="report-list-item rounded-2 mb-4">
                        <div class="d-flex align-items-center">
                            <div class="report-list-icon bg-label-info shadow-xs me-4">
                                <i class='bx bxs-user-circle fs-30 text-info'></i>
                            </div>
                            <div class="staff-list-view">
                                <h6 class="text-white;">Franchise Name : <span class="fs-14;" id="franchiseName"></span>
                                </h6>
                                <h6 class="text-white;">Last Login : <span class="fs-14;" id="franchiseLastLogin"></span>
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 px-2 order-2">
                    <a href="#" class="dashboard_animation dashboard_animation__new mb-3"
                        onclick="getDashboardEvent('plan_updates')">
                        <div class="card w-100 dashboard-box-color">
                            <div class="admin-card-box p-3 d-flex justify-content-between w-100">
                                <ul class="p-0 m-0 w-100">
                                    <li class="d-flex">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-danger">
                                                <i class='bx bx-money fs-24 text-warning'></i>
                                            </span>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-2">
                                            <div class="d-flex flex-column">
                                                <h6 class="text-white mb-1">Members Plan Update</h6>
                                                <h5 class="mb-0 text-warning plan_updates" id="memberPaymentCount"></h5>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4 px-2 order-2">
                    <a href="#" class="dashboard_animation dashboard_animation__new mb-3"
                        onclick="getDashboardEvent('view_profiles')">
                        <div class="card w-100 dashboard-box-color">
                            <div class="admin-card-box p-3 d-flex justify-content-between w-100">
                                <ul class="p-0 m-0 w-100">
                                    <li class="d-flex">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-danger">
                                                <i class='bx bxs-bullseye fs-24 text-warning'></i>
                                            </span>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-2">
                                            <div class="d-flex flex-column">
                                                <h6 class="text-white mb-1">Members View Profiles</h6>
                                                <h5 class="mb-0 text-warning view_profiles" id="viewProfileCount"></h5>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4 px-2 order-2">
                    <a href="#" class="dashboard_animation dashboard_animation__new mb-3"
                        onclick="getDashboardEvent('edit_profiles')">
                        <div class="card w-100 dashboard-box-color">
                            <div class="admin-card-box p-3 d-flex justify-content-between w-100">
                                <ul class="p-0 m-0 w-100">
                                    <li class="d-flex">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-danger">
                                                <i class='bx bxs-user-pin fs-24 text-warning'></i>
                                            </span>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-2">
                                            <div class="d-flex flex-column">
                                                <h6 class="text-white mb-1">Members Edit Profiles</h6>
                                                <h5 class="mb-0 text-warning edit_profiles" id="editprofileCount"></h5>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4 px-2 order-2">
                    <a href="#" class="dashboard_animation dashboard_animation__new mb-3"
                        onclick="getDashboardEvent('assigned_member')">
                        <div class="card w-100 dashboard-box-color">
                            <div class="admin-card-box p-3 d-flex justify-content-between w-100">
                                <ul class="p-0 m-0 w-100">
                                    <li class="d-flex">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-danger">
                                                <i class='bx bxs-user-detail fs-24 text-warning'></i>
                                            </span>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-2">
                                            <div class="d-flex flex-column">
                                                <h6 class="text-white mb-1">Assign Member</h6>
                                                <h5 class="mb-0 text-warning assigned_member" id="assignedMemberCount">
                                                </h5>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4 px-2 order-2">
                    <a href="#" class="dashboard_animation dashboard_animation__new mb-3"
                        onclick="getDashboardEvent('unassigned_member')">
                        <div class="card w-100 dashboard-box-color">
                            <div class="admin-card-box p-3 d-flex justify-content-between w-100">
                                <ul class="p-0 m-0 w-100">
                                    <li class="d-flex">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-danger">
                                                <i class='bx bxs-error-circle fs-24 text-warning'></i>
                                            </span>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-2">
                                            <div class="d-flex flex-column">
                                                <h6 class="text-white mb-1">Pending Member Assign</h6>
                                                <h5 class="mb-0 text-warning unassigned_member"
                                                    id="pendingAssignMemberCount"></h5>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4 px-2 order-2">
                    <a href="#" class="dashboard_animation dashboard_animation__new mb-3"
                        onclick="getDashboardEvent('upload_photos')">
                        <div class="card w-100 dashboard-box-color">
                            <div class="admin-card-box p-3 d-flex justify-content-between w-100">
                                <ul class="p-0 m-0 w-100">
                                    <li class="d-flex">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-danger">
                                                <i class='bx bxs-user-account fs-24 text-warning'></i>
                                            </span>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-2">
                                            <div class="d-flex flex-column">
                                                <h6 class="text-white mb-1">Members Upload Photos</h6>
                                                <h5 class="mb-0 text-warning upload_photos" id="uploadPhotoCount"></h5>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4 px-2 order-2">
                    <a href="#" class="dashboard_animation dashboard_animation__new mb-3"
                        onclick="getDashboardEvent('add_comments')">
                        <div class="card w-100 dashboard-box-color">
                            <div class="admin-card-box p-3 d-flex justify-content-between w-100">
                                <ul class="p-0 m-0 w-100">
                                    <li class="d-flex">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-danger">
                                                <i class='bx bxs-chat fs-24 text-warning'></i>
                                            </span>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-2">
                                            <div class="d-flex flex-column">
                                                <h6 class="text-white mb-1">Comments On Members</h6>
                                                <h5 class="mb-0 text-warning add_comments" id="addCommentCount"></h5>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4 px-2 order-2">
                    <a href="{{ route('admin.franchiseLoginHistory.index') }}" id="franchiseLoginLink"
                        class="dashboard_animation dashboard_animation__new mb-3">
                        <div class="card w-100 dashboard-box-color">
                            <div class="admin-card-box p-3 d-flex justify-content-between w-100">
                                <ul class="p-0 m-0 w-100">
                                    <li class="d-flex">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-danger">
                                                <i class='bx bxs-chat fs-24 text-warning'></i>
                                            </span>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-2">
                                            <div class="d-flex flex-column">
                                                <h6 class="text-white mb-1">Franchise Login History</h6>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4 px-2 order-2 totalTimeSpend">
                    <a href="#" class="dashboard_animation dashboard_animation__new mb-3">
                        <div class="card w-100 dashboard-box-color">
                            <div class="admin-card-box p-3 d-flex justify-content-between w-100">
                                <ul class="p-0 m-0 w-100">
                                    <li class="d-flex">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-danger">
                                                <i class='bx bxs-chat fs-24 text-warning'></i>
                                            </span>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-2">
                                            <div class="d-flex flex-column">
                                                <h6 class="text-white mb-1">Total Time Spendings</h6>
                                                <h5 class="mb-0 text-warning" id="totalTimeSpend"></h5>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-12 mb-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="report-list-icon bg-label-info shadow-xs me-4">
                                <i class="bx bxs-credit-card rounded fs-30 text-info"></i>
                            </div>
                            <div class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-2">
                                <div class="d-flex flex-column">
                                    <h5 class="text-nowrap mb-1 text-dark">Franchise Total Commission</h5>
                                    <span class="badge bg-label-primary w-max-content"
                                        id="totalFranchiseCommAmount"></span>
                                </div>
                            </div>
                        </div>
                        <div id="franchiseCommissionReports" class="mt-4"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @csrf
@endsection
