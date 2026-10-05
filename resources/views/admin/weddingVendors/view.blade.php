@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')
@section('admin_content')
    @php
        $statusClass = $resultArr->status === 'APPROVED' ? 'bg-label-success' : 'bg-label-danger';

        function vendorImg($img)
        {
            $url = _assetUrl('upload_path.ADMIN_NO_IMAGE_FOUND');
            if (!blank($img) && _checkStorageFileExists('upload_path.WEDDING_PLANNER_IMAGE_URL', $img)) {
                $url = _assetUrl('upload_path.WEDDING_PLANNER_IMAGE_URL') . $img;
            }
            return $url;
        }
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Header -->
            <div class="card shadow-sm mb-4">
                <div class="card-body d-flex justify-content-between flex-wrap align-items-start">

                    <div>
                        <h2 class="mb-1">{{ _displayNotAvailable($resultArr->planner_name) }}</h2>

                        <div class="text-muted">
                            {{ _displayNotAvailable($resultArr->title) }}
                        </div>

                        <span class="badge {{ $statusClass }} rounded-pill px-3 py-2 mt-2 d-inline-block">
                            {{ $resultArr->status }}
                        </span>
                    </div>

                    <div class="text-end text-muted small">
                        Created on<br>
                        {{ _displayDate($resultArr->created_at, 'j F, Y h:i A') }}
                    </div>

                </div>
            </div>

            <div class="row">

                <!-- Business Info -->
                <div class="col-lg-8 mb-4">

                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light fw-semibold">
                            🏢 Vendor Information
                        </div>
                        <div class="card-body row">

                            <div class="col-md-6 mb-3">
                                <strong>Category</strong>
                                <div>{{ _displayNotAvailable($resultArr->category->category_name) }}</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <strong>Capacity</strong>
                                <div>{{ _displayNotAvailable($resultArr->capacity) }} People</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <strong>Price Range</strong>
                                <div>
                                    {{ _displayNotAvailable($resultArr->start_rate_range) }}
                                    -
                                    {{ _displayNotAvailable($resultArr->end_rate_range) }}
                                    {{ _displayNotAvailable($resultArr->currency) }}
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <strong>Address</strong>
                                <div>{{ _displayNotAvailable($resultArr->address) }}</div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <strong>Country</strong>
                                <div>{{ _displayNotAvailable($resultArr->country->country_name) }}</div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <strong>State</strong>
                                <div>{{ _displayNotAvailable($resultArr->state->state_name) }}</div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <strong>City</strong>
                                <div>{{ _displayNotAvailable($resultArr->city->city_name) }}</div>
                            </div>

                        </div>
                    </div>

                    <!-- Contact -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light fw-semibold">
                            📞 Contact Information
                        </div>
                        <div class="card-body row">

                            <div class="col-md-6 mb-3">
                                <strong>Email</strong>
                                <div>
                                    @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                        {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                    @else
                                        {{ _displayNotAvailable($resultArr->email) }}
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <strong>Mobile</strong>
                                <div>
                                    @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                        {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                    @else
                                        {{ _displayNotAvailable($resultArr->mobile) }}
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Description -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-light fw-semibold">
                            📝 Description
                        </div>
                        <div class="card-body" style="line-height:1.9;">
                            {!! html_entity_decode(_displayNotAvailable($resultArr->description)) !!}
                        </div>
                    </div>

                </div>

                <!-- Gallery & Links -->
                <div class="col-lg-4 mb-4">

                    <!-- Gallery -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light fw-semibold">
                            🖼 Gallery
                        </div>
                        <div class="card-body">

                            @foreach (['image', 'image_2', 'image_3', 'image_4', 'image_5'] as $img)
                                <div class="mb-2">
                                    <img src="{{ vendorImg($resultArr->$img ?? null) }}" class="w-100 rounded border"
                                        style="height:120px;object-fit:cover;">
                                </div>
                            @endforeach

                        </div>
                    </div>

                    <!-- Social Links -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-light fw-semibold">
                            🌐 Social Links
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-5 text-muted">
                                    Website
                                </div>
                                <div class="col-7 fw-semibold">
                                    {{ _displayNotAvailable($resultArr->website) }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-5 text-muted">
                                    Facebook
                                </div>
                                <div class="col-7 fw-semibold">
                                    {{ _displayNotAvailable($resultArr->facebook_link) }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-5 text-muted">
                                    Twitter
                                </div>
                                <div class="col-7 fw-semibold">
                                    {{ _displayNotAvailable($resultArr->twitter_link) }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-5 text-muted">
                                    Google
                                </div>
                                <div class="col-7 fw-semibold">
                                    {{ _displayNotAvailable($resultArr->google_link) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
