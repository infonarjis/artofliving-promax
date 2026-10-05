@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')

@section('admin_content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            {{-- HEADER CARD --}}
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body">

                    <div class="row align-items-center g-3">

                        <div class="col-md-3 text-center">
                            @php
                                $imageURL = _assetUrl('upload_path.ADMIN_NO_IMAGE_FOUND');
                                if (
                                    !blank($resultArr->image) &&
                                    _checkStorageFileExists(
                                        'upload_path.AFFILIATE_MEMBER_PHOTOS_URL',
                                        $resultArr->image,
                                    )
                                ) {
                                    $imageURL =
                                        _assetUrl('upload_path.AFFILIATE_MEMBER_PHOTOS_URL') . $resultArr->image;
                                }
                            @endphp

                            <img src="{{ $imageURL }}" class="rounded border w-100"
                                style="max-height:180px; object-fit:cover;">
                        </div>

                        <div class="col-md-9">

                            <h4 class="mb-2">{{ _displayNotAvailable($resultArr->fullname) }}</h4>

                            <div class="text-muted mb-2">
                                Referral Code:
                                <code>{{ _displayNotAvailable($resultArr->referral_code) }}</code>
                            </div>

                            <div class="d-flex flex-wrap gap-2 align-items-center">

                                <span
                                    class="badge {{ $resultArr->status == 'APPROVED' ? 'bg-label-success' : 'bg-label-danger' }} px-3 py-2 rounded-pill">
                                    {{ $resultArr->status }}
                                </span>

                                <span class="text-muted small">
                                    Joined: {{ _displayDate($resultArr->created_at, 'j F, Y h:i A') }}
                                </span>

                                <span class="badge bg-label-primary px-3 py-2 rounded-pill">
                                    {{ _displayNotAvailable($resultArr->gender) }}
                                </span>

                            </div>

                        </div>

                    </div>

                </div>
            </div>

            {{-- DETAILS GRID --}}
            <div class="row g-4">

                {{-- BASIC INFO --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light fw-semibold">
                            Basic Information
                        </div>

                        <div class="card-body">

                            <div class="mb-3">
                                <div class="text-muted small">Full Name</div>
                                <div class="fw-semibold">{{ _displayNotAvailable($resultArr->fullname) }}</div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Email</div>
                                <div class="fw-semibold">
                                    @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                        {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                    @else
                                        {{ _displayNotAvailable($resultArr->email) }}
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Mobile</div>
                                <div class="fw-semibold">
                                    @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                        {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                    @else
                                        {{ _displayNotAvailable($resultArr->mobile) }}
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- COMMISSION INFO --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light fw-semibold">
                            Commission Details
                        </div>

                        <div class="card-body">

                            <div class="mb-3">
                                <div class="text-muted small">Verify Profile</div>
                                <div class="fw-semibold">{{ ($resultArr->verify_profile == 1) ? 'Yes' : 'No' }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="text-muted small">Verify Profile Commission</div>
                                <div class="fw-semibold">{{ _displayNotAvailable($resultArr->verify_profile_commission) }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="text-muted small">Paid Profile</div>
                                <div class="fw-semibold">{{ ($resultArr->paid_profile == 1) ? 'Yes' : 'No' }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="text-muted small">Paid Profile Commission</div>
                                <div class="fw-semibold">{{ _displayNotAvailable($resultArr->paid_profile_commission) }}
                                </div>
                            </div>
                            <!-- <div class="mb-3">
                                <div class="text-muted small">On Field Profile</div>
                                <div class="fw-semibold">{{ ($resultArr->on_field_verify_profile == 1) ? 'Yes' : 'No' }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="text-muted small">On Field Commission</div>
                                <div class="fw-semibold">
                                    {{ _displayNotAvailable($resultArr->on_field_verify_profile_commission) }}</div>
                            </div> -->

                        </div>
                    </div>
                </div>

                {{-- BANK DETAILS --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light fw-semibold">
                            Bank Details
                        </div>

                        <div class="card-body">

                            <div class="mb-3">
                                <div class="text-muted small">Bank Name</div>
                                <div class="fw-semibold">{{ _displayNotAvailable($resultArr->bank_name) }}</div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Account Holder</div>
                                <div class="fw-semibold">{{ _displayNotAvailable($resultArr->bank_account_holder_name) }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Account Number</div>
                                <div class="fw-semibold">{{ _displayNotAvailable($resultArr->bank_account_number) }}</div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">IFSC Code</div>
                                <div class="fw-semibold">{{ _displayNotAvailable($resultArr->bank_ifsc_code) }}</div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">UPI ID</div>
                                <div class="fw-semibold">{{ _displayNotAvailable($resultArr->upi_id) }}</div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- LOCATION INFO --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light fw-semibold">
                            Location Details
                        </div>

                        <div class="card-body">

                            <div class="mb-3">
                                <div class="text-muted small">Country</div>
                                <div class="fw-semibold">
                                    {{ _displayNotAvailable(optional($resultArr->country)->country_name) }}</div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">State</div>
                                <div class="fw-semibold">
                                    {{ _displayNotAvailable(optional($resultArr->state)->state_name) }}</div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">City</div>
                                <div class="fw-semibold">{{ _displayNotAvailable(optional($resultArr->city)->city_name) }}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection
