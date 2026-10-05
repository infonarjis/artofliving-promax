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
                                {{ _displayNotAvailable($resultArr->package_title) }}
                            </h4>

                            <div class="d-flex flex-wrap gap-2 align-items-center">

                                <span class="badge bg-label-primary px-3 py-2 rounded-pill">
                                    {{ _displayNotAvailable($resultArr->package_category) }}
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

                {{-- PACKAGE INFO --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 h-100">

                        <div class="card-header bg-light fw-semibold">
                            Package Information
                        </div>

                        <div class="card-body">

                            <div class="mb-3">
                                <div class="text-muted small">Package Title</div>
                                <div class="fw-semibold">
                                    {{ _displayNotAvailable($resultArr->package_title) }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Category</div>
                                <div class="fw-semibold">
                                    {{ _displayNotAvailable($resultArr->package_category) }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Package Count</div>
                                <div class="fw-semibold">
                                    {{ _displayNotAvailable($resultArr->package_count) }}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- PRICING --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 h-100">

                        <div class="card-header bg-light fw-semibold">
                            Pricing Details
                        </div>

                        <div class="card-body">

                            <div class="mb-3">
                                <div class="text-muted small">Package Amount</div>
                                <div class="fw-semibold">
                                    {{ _displayNotAvailable($resultArr->package_amount) }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Description</div>
                                <div class="fw-semibold">
                                    {{ _displayNotAvailable($resultArr->description) }}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection
