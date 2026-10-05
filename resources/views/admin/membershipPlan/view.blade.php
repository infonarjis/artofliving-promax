@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')

@section('admin_content')

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            {{-- HEADER --}}
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body">

                    <div class="row align-items-center g-3">

                        <div class="col-md-9">

                            <h4 class="mb-2">
                                {{ _displayNotAvailable($resultArr->plan_name) }}
                            </h4>

                            <div class="d-flex flex-wrap gap-2 align-items-center">

                                <span class="badge bg-label-primary px-3 py-2 rounded-pill">
                                    {{ _displayNotAvailable($resultArr->plan_type) }}
                                </span>

                                <span class="badge bg-label-info px-3 py-2 rounded-pill">
                                    {{ _displayNotAvailable($resultArr->currency_code) }}
                                </span>

                                <span
                                    class="badge {{ $resultArr->status == 'APPROVED' ? 'bg-label-success' : 'bg-label-danger' }} px-3 py-2 rounded-pill">
                                    {{ $resultArr->status }}
                                </span>

                                <span class="text-muted small">
                                    Created: {{ _displayDate($resultArr->created_at, 'j F, Y h:i A') }}
                                </span>

                            </div>

                        </div>

                    </div>

                </div>
            </div>

            {{-- DETAILS --}}
            <div class="row g-4">

                {{-- BASIC INFO --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 h-100">

                        <div class="card-header bg-light fw-semibold">
                            Plan Information
                        </div>

                        <div class="card-body">

                            <div class="mb-3">
                                <div class="text-muted small">Plan Name</div>
                                <div class="fw-semibold">
                                    {{ _displayNotAvailable($resultArr->plan_name) }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Plan Amount</div>
                                <div class="fw-semibold">
                                    {{ _displayNotAvailable($resultArr->plan_amount) }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Plan Type</div>
                                <div class="fw-semibold">
                                    {{ _displayNotAvailable($resultArr->plan_type) }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Currency</div>
                                <div class="fw-semibold">
                                    {{ _displayNotAvailable($resultArr->currency_code) }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Validity (Days)</div>
                                <div class="fw-semibold">
                                    {{ _displayNotAvailable($resultArr->validity_days) }}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- FEATURES --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 h-100">

                        <div class="card-header bg-light fw-semibold">
                            Plan Features
                        </div>

                        <div class="card-body">

                            <div class="mb-3">
                                <div class="text-muted small">Chat Access</div>
                                <div class="fw-semibold">
                                    {{ $resultArr->can_chat ? 'Yes' : 'No' }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Discount (%)</div>
                                <div class="fw-semibold">
                                    {{ _displayNotAvailable($resultArr->plan_discount) }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Send Interest Limit</div>
                                <div class="fw-semibold">
                                    {{ _displayNotAvailable($resultArr->interests_limit) }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Contact View Limit</div>
                                <div class="fw-semibold">
                                    {{ _displayNotAvailable($resultArr->contact_views_limit) }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Personalized Plan</div>
                                <div class="fw-semibold">
                                    {{ $resultArr->is_personalized ? 'Yes' : 'No' }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Send Auto AI Interest</div>
                                <div class="fw-semibold">
                                    {{ $resultArr->ai_interest ? 'Yes' : 'No' }}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- CALL FEATURES --}}
                @if ($configArr['zego_video_call_setting'] == 'APPROVED' || $configArr['zego_voice_call_setting'] == 'APPROVED')
                    <div class="col-lg-6">
                        <div class="card shadow-sm border-0 h-100">

                            <div class="card-header bg-light fw-semibold">
                                Call Features
                            </div>

                            <div class="card-body">

                                @if ($configArr['zego_video_call_setting'] == 'APPROVED')
                                    <div class="mb-3">
                                        <div class="text-muted small">Video Call Minutes</div>
                                        <div class="fw-semibold">
                                            {{ _displayNotAvailable($resultArr->video_minutes_limit) }}
                                        </div>
                                    </div>
                                @endif

                                @if ($configArr['zego_voice_call_setting'] == 'APPROVED')
                                    <div class="mb-3">
                                        <div class="text-muted small">Audio Call Minutes</div>
                                        <div class="fw-semibold">
                                            {{ _displayNotAvailable($resultArr->audio_minutes_limit) }}
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                @endif

                {{-- DESCRIPTION --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 h-100">

                        <div class="card-header bg-light fw-semibold">
                            Plan Description
                        </div>

                        <div class="card-body">

                            <div class="fw-semibold text-muted">
                                {{ _displayNotAvailable($resultArr->plan_description) }}
                            </div>

                        </div>
                    </div>
                </div>

                {{-- META --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 h-100">

                        <div class="card-header bg-light fw-semibold">
                            Meta Information
                        </div>

                        <div class="card-body">

                            <div class="mb-3">
                                <div class="text-muted small">Created On</div>
                                <div class="fw-semibold">
                                    {{ _displayDate($resultArr->created_at, 'j F, Y h:i A') }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Status</div>
                                <div>
                                    <span
                                        class="badge {{ $resultArr->status == 'APPROVED' ? 'bg-label-success' : 'bg-label-danger' }} px-3 py-2 rounded-pill">
                                        {{ $resultArr->status }}
                                    </span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

@endsection
