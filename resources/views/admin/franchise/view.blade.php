@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')
@section('admin_content')
    @php
        $statusClass = $resultArr->status === 'APPROVED' ? 'bg-label-success' : 'bg-label-danger';
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Header Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-body d-flex justify-content-between flex-wrap align-items-start">

                    <div>
                        <h2 class="mb-1">{{ _displayNotAvailable($resultArr->username) }}</h2>
                        <span class="badge {{ $statusClass }} rounded-pill px-3 py-2">
                            {{ $resultArr->status }}
                        </span>
                    </div>

                    <div class="text-end text-muted small">
                        Created on<br>
                        {{ _displayDate($resultArr->created_at, 'j F, Y h:i A') }}
                    </div>

                </div>
            </div>

            <!-- Contact & Credentials -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light fw-semibold">
                    🔐 Contact Information
                </div>
                <div class="card-body row">

                    <div class="col-md-4 mb-3">
                        <strong>Email</strong>
                        <div>
                            @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                            @else
                                {{ _displayNotAvailable($resultArr->email) }}
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <strong>Mobile</strong>
                        <div>
                            @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                            @else
                                {{ _displayNotAvailable($resultArr->mobile) }}
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <strong>Password</strong>
                        <div class="text-danger">
                            {{ _displayNotAvailable($resultArr->password_decrypted) }}
                        </div>
                    </div>

                </div>
            </div>

            <!-- Business Details -->
            <!-- Business Details -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light fw-semibold">
                    💼 Business Details
                </div>

                <div class="card-body row">

                    <div class="col-md-4 mb-3">
                        <strong>Commission (%)</strong>
                        <div>{{ _displayNotAvailable($resultArr->commission) }}</div>
                    </div>

                    <div class="col-md-8 mb-3">
                        <strong>Referral Link</strong>

                        @php
                            $referralCode = $resultArr->referral_code ?? null;

                            $referralLink = $referralCode
                                ? route('web.register.referral', [
                                    'type' => 'franchise',
                                    'code' => $referralCode,
                                ])
                                : null;
                        @endphp

                        @if ($referralLink)
                            <div class="input-group mt-1">
                                <input type="text" class="form-control" id="referralLink" value="{{ $referralLink }}"
                                    readonly>

                                <button type="button" class="btn btn-primary" id="copyReferralLink">
                                    <i class="fa fa-copy"></i> Copy
                                </button>
                            </div>
                        @else
                            <div class="text-muted mt-1">N/A</div>
                        @endif
                    </div>

                    <div class="col-md-4 mb-3">
                        <strong>Last Login</strong>
                        <div>{{ _displayDate($resultArr->last_login, 'j F, Y h:i A') }}</div>
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).on('click', '#copyReferralLink', function () {
        const input = document.getElementById('referralLink');
        const button = $(this);

        navigator.clipboard.writeText(input.value).then(function () {
            const originalHtml = button.html();

            button.html('<i class="fa fa-check"></i> Copied');
            button.removeClass('btn-primary').addClass('btn-success');

            setTimeout(function () {
                button.html(originalHtml);
                button.removeClass('btn-success').addClass('btn-primary');
            }, 2000);
        });
    });
</script>
@endpush