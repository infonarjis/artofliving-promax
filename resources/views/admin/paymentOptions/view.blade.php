@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')

@section('admin_content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            {{-- Page Header --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">

                    <div class="row align-items-center">

                        <div class="col-lg-8 col-md-7">
                            <div class="d-flex align-items-center gap-3">

                                @if (!blank($resultArr->logo) && _checkStorageFileExists('upload_path.PAYMENT_LOGO_URL', $resultArr->logo))
                                    <img src="{{ _assetUrl('upload_path.PAYMENT_LOGO_URL') . $resultArr->logo }}"
                                        alt="{{ $resultArr->name }}" class="border rounded p-2 bg-white"
                                        style="width:70px;height:70px;object-fit:contain;">
                                @endif

                                <div>
                                    <h4 class="mb-1 fw-bold">
                                        {{ $resultArr->name }}
                                    </h4>

                                    <div class="d-flex flex-wrap gap-2 mt-2">

                                        <span class="badge bg-label-primary">
                                            {{ ucfirst($resultArr->payment_mode) }}
                                        </span>

                                        @if ($resultArr->status == 'APPROVED')
                                            <span class="badge bg-success">
                                                Active
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                Inactive
                                            </span>
                                        @endif

                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="col-lg-4 col-md-5 text-md-end mt-3 mt-md-0">
                            <small class="text-muted d-block">
                                Created On
                            </small>

                            <strong>
                                {{ _displayDate($resultArr->created_at, 'd M Y h:i A') }}
                            </strong>
                        </div>

                    </div>

                </div>
            </div>

            <div class="row g-4">

                {{-- Gateway Information --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 h-100">

                        <div class="card-header bg-light fw-semibold">
                            Payment Gateway Info
                        </div>

                        <div class="card-body">

                            <div class="mb-3">
                                <div class="text-muted small">Payment Name</div>
                                <div class="fw-semibold">
                                    {{ $resultArr->name }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="text-muted small">Payment Status</div>
                                <div class="fw-semibold">
                                    @if ($resultArr->status == 'APPROVED')
                                        <span class="badge bg-success">
                                            Active
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            Inactive
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Payment Mode</div>
                                <div class="fw-semibold">
                                    {{ _getStaticArr('paymentModeArr', $resultArr->payment_mode) }}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Credentials --}}
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">

                        <div class="card-header">
                            <h5 class="mb-0">
                                API Credentials
                            </h5>
                        </div>

                        <div class="card-body">

                            <div class="mb-4">

                                <label class="form-label text-muted fw-semibold">
                                    Client ID
                                </label>

                                <div class="border rounded p-2 bg-light small text-break">
                                    {{ $resultArr->client_id ?: '-' }}
                                </div>

                            </div>

                            <div>

                                <label class="form-label text-muted fw-semibold">
                                    Client Secret
                                </label>

                                <div class="border rounded p-2 bg-light small">
                                    ***************
                                </div>

                            </div>

                        </div>

                    </div>
                </div>

                {{-- System Information --}}
                <div class="col-12">

                    <div class="card border-0 shadow-sm">

                        <div class="card-header">
                            <h5 class="mb-0">
                                System Information
                            </h5>
                        </div>

                        <div class="card-body">

                            <div class="row gy-4">

                                <div class="col-xl-3 col-lg-4 col-md-6">
                                    <div>
                                        <small class="text-muted d-block mb-1">
                                            Gateway ID
                                        </small>

                                        <strong>
                                            #{{ $resultArr->id }}
                                        </strong>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-lg-4 col-md-6">
                                    <div>
                                        <small class="text-muted d-block mb-1">
                                            Created At
                                        </small>

                                        <strong>
                                            {{ _displayDate($resultArr->created_at, 'd M Y h:i A') }}
                                        </strong>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-lg-4 col-md-6">
                                    <div>
                                        <small class="text-muted d-block mb-1">
                                            Updated At
                                        </small>

                                        <strong>
                                            {{ _displayDate($resultArr->updated_at, 'd M Y h:i A') }}
                                        </strong>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>
@endsection
