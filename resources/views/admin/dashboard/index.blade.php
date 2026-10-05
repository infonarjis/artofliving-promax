@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')
@section('admin_content')
    @php
        ## Check Roles Permission:
        $authUser = Auth::user();
        $userType = _adminUserType($authUser->type);
        $roleId = _adminRoleId($authUser, $userType);

        $viewMemberPermission = _checkPermission($userType, $roleId, 'view_member');
        $viewMember = 0;
        if ($viewMemberPermission != 'No') {
            $viewMember = 1;
        }

        $photoApprovalPermission = _checkPermission($userType, $roleId, 'photo_approval');
        $approvalMember = 0;
        if ($photoApprovalPermission != 'No') {
            $approvalMember = 1;
        }
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
        @if ($userType == 'Staff')
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bx bx-time-five me-1"></i>
                                Staff Attendance
                            </h5>
                            <a href="{{ route('admin.staffAttendance.index') }}" class="btn btn-sm btn-outline-primary">
                                <i class="bx bx-history"></i>
                                View History
                            </a>
                        </div>
                        <div class="card-body">
                            @if (session('success_msg'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success_msg') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert">
                                    </button>
                                </div>
                            @endif
                            @if (!empty($todayPunch))
                                <div class="alert alert-info">
                                    <strong>Punch In On :</strong>
                                    {{ \Carbon\Carbon::parse($todayPunch['punch_in'])->format('d-m-Y h:i A') }}
                                </div>
                            @endif
                            <div class="row align-items-center">
                                @if (!empty($todayPunch))
                                    <div class="col-md-4 mb-3">
                                        <div class="border rounded p-3 text-center bg-light">
                                            <div class="fs-6 text-muted mb-1">
                                                Working Time
                                            </div>
                                            <div class="fs-3 fw-bold text-primary">
                                                <i class="bx bx-timer"></i>
                                                <span id="liveTimer">0:00:00</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-8">
                                    <form method="POST" action="{{ route('admin.staffAttendance.punchInOut') }}">
                                        @csrf
                                        <div class="row">
                                            @if (empty($todayPunch))
                                                <div class="col-md-8 mb-3">
                                                    <input type="text" name="punch_in_remarks" class="form-control"
                                                        placeholder="Enter punch in remarks">
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <button type="submit" class="btn btn-success w-100">
                                                        <i class="bx bx-log-in-circle"></i>
                                                        Punch In
                                                    </button>
                                                </div>
                                            @else
                                                <input type="hidden" name="id" value="{{ $todayPunch['id'] }}">
                                                <div class="col-md-8 mb-3">
                                                    <input type="text" name="punch_out_remarks" class="form-control"
                                                        placeholder="Enter punch out remarks">
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <button type="submit" class="btn btn-danger w-100">
                                                        <i class="bx bx-log-out-circle"></i>
                                                        Punch Out
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if ($viewMember)
            <div class="dashboard-card {{ $userType == 'Staff' ? 'mt-3' : '' }}">
                <div class="row">
                    <div class="col-md-6 col-lg-4 px-2">
                        <a href="{{ route('admin.member.index') }}"
                            class="dashboard_animation dashboard_animation__new mb-3">
                            <div class="card w-100 dashboard-box-color">
                                <div class="admin-card-box p-3 d-flex justify-content-between w-100">
                                    <ul class="p-0 m-0 w-100">
                                        <li class="d-flex">
                                            <div class="avatar flex-shrink-0 me-3">
                                                <span class="avatar-initial rounded bg-label-primary">
                                                    <i class="bx bxs-user"></i>
                                                </span>
                                            </div>
                                            <div
                                                class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                <div class="me-2">
                                                    <h6 class="mb-0 text-white">Total Member</h6>
                                                </div>
                                                <div class="user-progress d-flex align-items-center gap-1">
                                                    <span class="text-muted badge bg-label-primary rounded-pill"
                                                        id="allMemberCount"></span>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-4 px-2">
                        <a href="javascript:void(0);" class="dashboard_animation dashboard_animation__new mb-3"
                            onclick="getDashboardData('gender','Male')">
                            <div class="card w-100 dashboard-box-color">
                                <div class="admin-card-box p-3 d-flex justify-content-between w-100">
                                    <ul class="p-0 m-0 w-100">
                                        <li class="d-flex">
                                            <div class="avatar flex-shrink-0 me-3">
                                                <span class="avatar-initial rounded bg-label-info">
                                                    <i class='bx bx-male-sign'></i>
                                                </span>
                                            </div>
                                            <div
                                                class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                <div class="me-2">
                                                    <h6 class="mb-0 text-white">Male Member</h6>
                                                </div>
                                                <div class="user-progress d-flex align-items-center gap-1">
                                                    <span class="text-muted badge bg-label-primary rounded-pill"
                                                        id="maleMemberCount"></span>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-4 px-2">
                        <a href="javascript:void(0);" class="dashboard_animation dashboard_animation__new mb-3"
                            onclick="getDashboardData('gender','Female')">
                            <div class="card w-100 dashboard-box-color">
                                <div class="admin-card-box p-3 d-flex justify-content-between w-100">
                                    <ul class="p-0 m-0 w-100">
                                        <li class="d-flex">
                                            <div class="avatar flex-shrink-0 me-3">
                                                <span class="avatar-initial rounded bg-label-info">
                                                    <i class='bx bx-female-sign'></i>
                                                </span>
                                            </div>
                                            <div
                                                class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                <div class="me-2">
                                                    <h6 class="mb-0 text-white">Female Member</h6>
                                                </div>
                                                <div class="user-progress d-flex align-items-center gap-1">
                                                    <span class="text-muted badge bg-label-primary rounded-pill"
                                                        id="femaleMemberCount"></span>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-4 px-2">
                        <a href="javascript:void(0);" class="dashboard_animation dashboard_animation__new mb-3"
                            onclick="getDashboardData('plan_status','Paid')">
                            <div class="card w-100 dashboard-box-color">
                                <div class="admin-card-box p-3 d-flex justify-content-between w-100">
                                    <ul class="p-0 m-0 w-100">
                                        <li class="d-flex">
                                            <div class="avatar flex-shrink-0 me-3">
                                                <span class="avatar-initial rounded bg-label-success">
                                                    <i class='bx bx-credit-card'></i>
                                                </span>
                                            </div>
                                            <div
                                                class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                <div class="me-2">
                                                    <h6 class="mb-0 text-white">Paid Member</h6>
                                                </div>
                                                <div class="user-progress d-flex align-items-center gap-1">
                                                    <span class="text-muted badge bg-label-primary rounded-pill"
                                                        id="paidMemberCount"></span>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-4 px-2">
                        <a href="javascript:void(0);" class="dashboard_animation dashboard_animation__new mb-3"
                            onclick="getDashboardData('plan_status','Not Paid')">
                            <div class="card w-100 dashboard-box-color">
                                <div class="admin-card-box p-3 d-flex justify-content-between w-100">
                                    <ul class="p-0 m-0 w-100">
                                        <li class="d-flex">
                                            <div class="avatar flex-shrink-0 me-3">
                                                <span class="avatar-initial rounded bg-label-warning">
                                                    <i class='bx bx-credit-card'></i>
                                                </span>
                                            </div>
                                            <div
                                                class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                <div class="me-2">
                                                    <h6 class="mb-0 text-white">Not Paid Member</h6>
                                                </div>
                                                <div class="user-progress d-flex align-items-center gap-1">
                                                    <span class="text-muted badge bg-label-primary rounded-pill"
                                                        id="notPaidMemberCount"></span>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-4 px-2">
                        <a href="javascript:void(0);" onclick="getDashboardData('plan_status','Expired')"
                            class="dashboard_animation dashboard_animation__new mb-3">
                            <div class="card w-100 dashboard-box-color">
                                <div class="admin-card-box p-3 d-flex justify-content-between w-100">
                                    <ul class="p-0 m-0 w-100">
                                        <li class="d-flex">
                                            <div class="avatar flex-shrink-0 me-3">
                                                <span class="avatar-initial rounded bg-label-danger">
                                                    <i class='bx bx-credit-card'></i>
                                                </span>
                                            </div>
                                            <div
                                                class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                <div class="me-2">
                                                    <h6 class="mb-0 text-white">Expired Member</h6>
                                                </div>
                                                <div class="user-progress d-flex align-items-center gap-1">
                                                    <span class="text-muted badge bg-label-primary rounded-pill"
                                                        id="expiredMemberCount"></span>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-4 px-2">
                        <a href="javascript:void(0);" class="dashboard_animation dashboard_animation__new mb-3"
                            onclick="getDashboardData('status','APPROVED')">
                            <div class="card w-100 dashboard-box-color">
                                <div class="admin-card-box p-3 d-flex justify-content-between w-100">
                                    <ul class="p-0 m-0 w-100">
                                        <li class="d-flex">
                                            <div class="avatar flex-shrink-0 me-3">
                                                <span class="avatar-initial rounded bg-label-success">
                                                    <i class='bx bxs-like'></i>
                                                </span>
                                            </div>
                                            <div
                                                class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                <div class="me-2">
                                                    <h6 class="mb-0 text-white">Approved Member</h6>
                                                </div>
                                                <div class="user-progress d-flex align-items-center gap-1">
                                                    <span class="text-muted badge bg-label-primary rounded-pill"
                                                        id="approvedMemberCount"></span>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-4 px-2">
                        <a href="javascript:void(0);" class="dashboard_animation dashboard_animation__new mb-3"
                            onclick="getDashboardData('status','UNAPPROVED')">
                            <div class="card w-100 dashboard-box-color">
                                <div class="admin-card-box p-3 d-flex justify-content-between w-100">
                                    <ul class="p-0 m-0 w-100">
                                        <li class="d-flex">
                                            <div class="avatar flex-shrink-0 me-3">
                                                <span class="avatar-initial rounded bg-label-dark">
                                                    <i class='bx bxs-dislike'></i>
                                                </span>
                                            </div>
                                            <div
                                                class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                <div class="me-2">
                                                    <h6 class="mb-0 text-white">Unapproved Member</h6>
                                                </div>
                                                <div class="user-progress d-flex align-items-center gap-1">
                                                    <span class="text-muted badge bg-label-primary rounded-pill"
                                                        id="unapprovedMemberCount"></span>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-4 px-2">
                        <a href="javascript:void(0);" class="dashboard_animation dashboard_animation__new mb-3"
                            onclick="getDashboardData('status','Suspended')">
                            <div class="card w-100 dashboard-box-color">
                                <div class="admin-card-box p-3 d-flex justify-content-between w-100">
                                    <ul class="p-0 m-0 w-100">
                                        <li class="d-flex">
                                            <div class="avatar flex-shrink-0 me-3">
                                                <span class="avatar-initial rounded bg-label-danger">
                                                    <i class='bx bx-block'></i>
                                                </span>
                                            </div>
                                            <div
                                                class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                <div class="me-2">
                                                    <h6 class="mb-0 text-white">Suspended Member</h6>
                                                </div>
                                                <div class="user-progress d-flex align-items-center gap-1">
                                                    <span class="text-muted badge bg-label-primary rounded-pill"
                                                        id="suspendedMemberCount"></span>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                @if ($userType == 'Admin')
                    <div class="row">
                        <!--Staff & Franchise-->
                        <div class="col-md-2 col-lg-2 order-2"></div>
                        <div class="col-md-4 col-lg-4 order-2">
                            <a target="_blank" href="{{ route('admin.staffDashboard') }}"
                                class="dashboard_animation dashboard_animation__new mb-3">
                                <div class="card w-100 dashboard-box-color">
                                    <div class="admin-card-box p-3 d-flex justify-content-between w-100">
                                        <ul class="p-0 m-0 w-100">
                                            <li class="d-flex">
                                                <div class="avatar flex-shrink-0 me-3">
                                                    <span class="avatar-initial rounded bg-label-danger">
                                                        <i class='bx bx-desktop fs-25 text-warning'></i>
                                                    </span>
                                                </div>
                                                <div
                                                    class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                    <div class="me-2">
                                                        <h6 class="mb-0 text-white">Staff Dashboard</h6>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4 col-lg-4 order-2">
                            <a href="{{ route('admin.franchiseDashboard') }}"
                                class="dashboard_animation dashboard_animation__new mb-3">
                                <div class="card w-100 dashboard-box-color">
                                    <div class="admin-card-box p-3 d-flex justify-content-between w-100">
                                        <ul class="p-0 m-0 w-100">
                                            <li class="d-flex">
                                                <div class="avatar flex-shrink-0 me-3">
                                                    <span class="avatar-initial rounded bg-label-danger">
                                                        <i class='bx bx-desktop fs-25 text-warning'></i>
                                                    </span>
                                                </div>
                                                <div
                                                    class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                    <div class="me-2">
                                                        <h6 class="mb-0 text-white">Franchise Dashboard</h6>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-2 col-lg-2 order-2"></div>
                        <!--Staff & Franchise-->
                    </div>
                @endif
            </div>
        @endif
        @if (isset($userType) && $userType == 'Franchise')
            @php
                $refferalCode = $authUser->referral_code;
                $refferalLink = route('web.register.referral', [
                    'type' => 'franchise',
                    'code' => $refferalCode,
                ]);

            @endphp
            <div class="row mt-3">
                <div class="col-md-12 col-lg-12 order-2">
                    <a href="#" class="dashboard_animation dashboard_animation__new mb-3">
                        <div class="card0 w-100">
                            <div class="admin-card-box p-3 d-flex justify-content-between w-100 bg-white">
                                <ul class="p-0 m-0 w-100">
                                    <li class="d-flex">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-info">
                                                <i class='bx bxs-share-alt'></i>
                                            </span>
                                        </div>
                                        <div
                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0 text-dark"> Your Referral Link</h6>
                                                <p class="text-dark">Share your referral link to your recommended members!
                                                </p>
                                            </div>
                                            <div class="input-group w-75">
                                                <input type="text" class="form-control" id="copyText1" readonly
                                                    value="{{ $refferalLink }}">
                                                <button class="btn btn-outline-primary" type="button" id="button-addon2"
                                                    onclick="copyToClipboard();">Copy Link</button>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endif

        <div class="row mt-3">
            @if ($viewMember)
                <div class="col-md-6 col-lg-6 order-2 mb-4">
                    <div class="card h-100">
                        <h5 class="card-header m-0 me-2 pb-3">Members Reports</h5>
                        <div id="chart"></div>
                    </div>
                </div>
                @if (isset($userType) && $userType == 'Admin')
                    <div class="col-md-6 col-lg-6 order-2 mb-4">
                        <div class="card h-100">
                            <h5 class="card-header m-0 me-2 pb-3">Total Earnings
                                <span class="badge bg-label-info rounded-pill">{{ $totalPayment }}</span>
                            </h5>
                            <div id="chart2"></div>
                        </div>
                    </div>
                @endif
                <div class="col-md-6 col-lg-6 order-2 mb-4">
                    <div class="card h-100">
                        <h5 class="card-header m-0 me-2 pb-3">Members Gender Reports</h5>
                        <div id="chartGender"></div>
                    </div>
                </div>
            @endif
            @if ($approvalMember)
                <div class="col-md-6 col-lg-6 order-2 mb-4">
                    <div class="card h-100">
                        <h5 class="card-header m-0 me-2 pb-3">Photo Approval Pending</h5>
                        <div id="chart4"></div>
                    </div>
                </div>
            @endif
            @if ($viewMember)
                <div class="col-md-12 col-lg-12 order-2">
                    <div class="card h-100">
                        <h5 class="card-header m-0 me-2 pb-3">Members Yearly Reports</h5>
                        <div id="chart5"></div>
                    </div>
                </div>
                <div class="col-md-12 col-lg-12 order-2 mt-4">
                    <div class="card h-100">
                        <h5 class="card-header m-0 me-2 pb-3">Earning Reports</h5>
                        <div id="chart3"></div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @csrf
    @if ($todayPunch)
        @push('scripts')
            <script>
                // Start datetime — convert to JS format (YYYY-MM-DD HH:MM:SS)
                var startTime = new Date("{{ $todayPunch['punch_in'] }}");
    
                function updateTimer() {
                    var now = new Date();
                    var diff = now - startTime; // milliseconds
    
                    if (diff < 0) diff = 0; // If time not reached
    
                    var hours = Math.floor(diff / (1000 * 60 * 60));
                    var minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((diff % (1000 * 60)) / 1000);
    
                    // Format HH:MM:SS
                    minutes = minutes < 10 ? "0" + minutes : minutes;
                    seconds = seconds < 10 ? "0" + seconds : seconds;
    
                    document.getElementById("liveTimer").innerHTML =
                        hours + ":" + minutes + ":" + seconds;
                }
    
                setInterval(updateTimer, 1000);
                updateTimer(); // first call
            </script>
        @endpush
    @endif

@endsection
