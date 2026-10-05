@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')
@section('admin_content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            @php
                $imageURL = _assetUrl('upload_path.ADMIN_NO_IMAGE_FOUND');
                if (
                    !blank($resultArr->profile_image) &&
                    _checkStorageFileExists('upload_path.STAFF_IMAGE_URL', $resultArr->profile_image)
                ) {
                    $imageURL = _assetUrl('upload_path.STAFF_IMAGE_URL') . $resultArr->profile_image;
                }
            @endphp

            <!-- Profile Header Card -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">

                        <img src="{{ $imageURL }}" class="rounded-circle border me-4" width="110" height="110"
                            style="object-fit:cover;">

                        <div class="flex-grow-1">
                            <h4 class="mb-1">{{ _displayNotAvailable($resultArr->username) }}</h4>
                            <div class="text-muted mb-2">Staff ID : {{ _displayNotAvailable($resultArr->staff_prefix) }}
                            </div>

                            <span
                                class="badge {{ $resultArr->status == 'APPROVED' ? 'bg-label-success' : 'bg-label-danger' }} rounded-pill px-3 py-2">
                                {{ $resultArr->status }}
                            </span>
                        </div>

                        <div class="text-end">
                            <div class="text-muted small">Role</div>
                            <div class="fw-semibold">{{ _displayNotAvailable($resultArr->staffRole->role_name) }}</div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Information Cards -->
            <div class="row">

                <!-- Personal Info -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-light fw-semibold">
                            Personal Information
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-5 text-muted">
                                    Gender
                                </div>
                                <div class="col-7 fw-semibold">
                                    {{ _displayNotAvailable($resultArr->gender) }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-5 text-muted">
                                    Email
                                </div>
                                <div class="col-7 fw-semibold">
                                    @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                        {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                    @else
                                        {{ _displayNotAvailable($resultArr->email) }}
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-5 text-muted">
                                    Mobile
                                </div>
                                <div class="col-7 fw-semibold">
                                    @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                        {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                    @else
                                        {{ _displayNotAvailable($resultArr->mobile) }}
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-5 text-muted">
                                    Date of Birth
                                </div>
                                <div class="col-7 fw-semibold">
                                    {{ _displayDate($resultArr->birthdate, 'j F, Y') }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-5 text-muted">
                                    Marital Status
                                </div>
                                <div class="col-7 fw-semibold">
                                    {{ _displayNotAvailable($resultArr->marital_status) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Info -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-light fw-semibold">
                            Account Information
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-5 text-muted">
                                    Password
                                </div>
                                <div class="col-7 fw-semibold">
                                    {{ _displayNotAvailable($resultArr->password_decrypted) }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-5 text-muted">
                                    Created On
                                </div>
                                <div class="col-7 fw-semibold">
                                    {{ _displayDate($resultArr->created_at, 'j F, Y h:i A') }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-5 text-muted">
                                    Last Login
                                </div>
                                <div class="col-7 fw-semibold">
                                    {{ _displayDate($resultArr->last_login, 'j F, Y h:i A') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection
