@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')

@section('admin_content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            {{-- HEADER --}}
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                        <div>
                            <h4 class="mb-1">
                                {{ _displayNotAvailable($resultArr->name) }}
                            </h4>

                            <div class="text-muted">
                                Wedding Request from Vendor:
                                <strong>{{ _displayNotAvailable(optional($resultArr->vendor)->planner_name) }}</strong>
                            </div>
                        </div>

                        <div class="text-end">

                            <div class="text-muted small">
                                Created: {{ _displayDate($resultArr->created_at, 'j F, Y h:i A') }}
                            </div>

                            <span class="badge bg-label-primary px-3 py-2 rounded-pill mt-1">
                                {{ _displayDate($resultArr->wedding_date, 'j F, Y') }}
                            </span>

                        </div>

                    </div>

                </div>
            </div>

            {{-- DETAILS --}}
            <div class="row g-4">

                {{-- CUSTOMER INFO --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 h-100">

                        <div class="card-header bg-light fw-semibold">
                            Customer Information
                        </div>

                        <div class="card-body">

                            <div class="mb-3">
                                <div class="text-muted small">Name</div>
                                <div class="fw-semibold">
                                    {{ _displayNotAvailable($resultArr->name) }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Mobile Number</div>
                                <div class="fw-semibold">
                                    {{ _displayNotAvailable($resultArr->mobile) }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Send Info By</div>
                                <div class="fw-semibold">
                                    {{ _displayNotAvailable($resultArr->sent_info_by) }}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- WEDDING DETAILS --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 h-100">

                        <div class="card-header bg-light fw-semibold">
                            Wedding Details
                        </div>

                        <div class="card-body">

                            <div class="mb-3">
                                <div class="text-muted small">Wedding Date</div>
                                <div class="fw-semibold">
                                    {{ _displayDate($resultArr->wedding_date, 'j F, Y') }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Total Guests</div>
                                <div class="fw-semibold">
                                    {{ _displayNotAvailable($resultArr->total_guest) }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Vendor</div>
                                <div class="fw-semibold">
                                    {{ _displayNotAvailable(optional($resultArr->vendor)->planner_name) }}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- DESCRIPTION --}}
                <div class="col-12">
                    <div class="card shadow-sm border-0">

                        <div class="card-header bg-light fw-semibold">
                            Description
                        </div>

                        <div class="card-body" style="line-height:1.8;">
                            {!! _displayNotAvailable(html_entity_decode($resultArr->description)) !!}
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection
