@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')

@section('admin_content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            @include('admin.message')

            {{-- HEADER CARD --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                        <h4 class="mb-0">Personal Meeting Details</h4>

                        <span class="badge bg-label-primary px-3 py-2 rounded-pill">
                            {{ _displayDate($resultArr->date_time, 'j F, Y h:i A') }}
                        </span>

                    </div>

                </div>
            </div>

            <div class="row">

                {{-- MAIN CONTENT --}}
                <div class="col-lg-8 mb-4">

                    <div class="card shadow-sm">

                        <div class="card-header bg-light fw-semibold">
                            Meeting Overview
                        </div>

                        <div class="card-body">

                            {{-- MEMBER 1 --}}
                            <h6 class="text-muted mb-3">Member 1</h6>

                            <div class="row g-3 mb-4">

                                <div class="col-md-6">
                                    <strong>Matri ID:</strong><br>
                                    {{ _displayNotAvailable($resultArr->member1_matri_id) }}
                                </div>

                                <div class="col-md-6">
                                    <strong>Status:</strong><br>
                                    @if ($resultArr->member1_status == 0)
                                        <span class="badge bg-label-warning">Pending</span>
                                    @elseif ($resultArr->member1_status == 1)
                                        <span class="badge bg-label-success">Accepted</span>
                                    @else
                                        <span class="badge bg-label-danger">Rejected</span>
                                    @endif
                                </div>

                                <div class="col-md-12">
                                    <strong>Reject Remark:</strong><br>
                                    {{ _displayNotAvailable($resultArr->member1_reject_remark) }}
                                </div>

                            </div>

                            <hr>

                            {{-- MEMBER 2 --}}
                            <h6 class="text-muted mb-3">Member 2</h6>

                            <div class="row g-3 mb-4">

                                <div class="col-md-6">
                                    <strong>Matri ID:</strong><br>
                                    {{ _displayNotAvailable($resultArr->member2_matri_id) }}
                                </div>

                                <div class="col-md-6">
                                    <strong>Status:</strong><br>
                                    @if ($resultArr->member2_status == 0)
                                        <span class="badge bg-label-warning">Pending</span>
                                    @elseif ($resultArr->member2_status == 1)
                                        <span class="badge bg-label-success">Accepted</span>
                                    @else
                                        <span class="badge bg-label-danger">Rejected</span>
                                    @endif
                                </div>

                                <div class="col-md-12">
                                    <strong>Reject Remark:</strong><br>
                                    {{ _displayNotAvailable($resultArr->member2_reject_remark) }}
                                </div>

                            </div>

                            <hr>

                            {{-- MEETING DETAILS --}}
                            <h6 class="text-muted mb-3">Meeting Details</h6>

                            <div class="row g-3">

                                <div class="col-md-6">
                                    <strong>Meeting Status:</strong><br>
                                    @if ($resultArr->meeting_status == 0)
                                        <span class="badge bg-label-warning">Pending</span>
                                    @else
                                        <span class="badge bg-label-success">Completed</span>
                                    @endif
                                </div>

                                <div class="col-md-6">
                                    <strong>Meeting Remark:</strong><br>
                                    {{ _displayNotAvailable($resultArr->meeting_remark) }}
                                </div>

                                <div class="col-md-6">
                                    <strong>Address:</strong><br>
                                    {{ _displayNotAvailable($resultArr->address) }}
                                </div>

                                <div class="col-md-6">
                                    <strong>Description:</strong><br>
                                    {{ _displayNotAvailable($resultArr->description) }}
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                {{-- SIDEBAR --}}
                <div class="col-lg-4 mb-4">

                    <div class="card shadow-sm h-100">

                        <div class="card-header bg-light fw-semibold">
                            Meta Info
                        </div>

                        <div class="card-body">

                            <div class="mb-3">
                                <div class="text-muted small">Created On</div>
                                <div class="fw-semibold">
                                    {{ _displayDate($resultArr->created_at, 'j F, Y h:i A') }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Member 1 Status</div>
                                <div>
                                    @if ($resultArr->member1_status == 0)
                                        <span class="badge bg-label-warning">Pending</span>
                                    @elseif ($resultArr->member1_status == 1)
                                        <span class="badge bg-label-success">Accepted</span>
                                    @else
                                        <span class="badge bg-label-danger">Rejected</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Member 2 Status</div>
                                <div>
                                    @if ($resultArr->member2_status == 0)
                                        <span class="badge bg-label-warning">Pending</span>
                                    @elseif ($resultArr->member2_status == 1)
                                        <span class="badge bg-label-success">Accepted</span>
                                    @else
                                        <span class="badge bg-label-danger">Rejected</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Admin Remark --}}
                            <form action="{{ route('admin.personalizeMeeting.remark', $resultArr->id) }}"
                                method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label for="admin_remark" class="form-label">
                                        Admin Remark
                                    </label>

                                    <textarea
                                        name="admin_remark"
                                        id="admin_remark"
                                        class="form-control"
                                        rows="4"
                                        placeholder="Enter admin remark..."
                                    >{{ old('admin_remark', $resultArr->admin_remark) }}</textarea>

                                    @error('admin_remark')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    Submit Remark
                                </button>
                            </form>

                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>
@endsection
