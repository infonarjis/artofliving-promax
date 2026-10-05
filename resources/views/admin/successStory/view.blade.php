@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')
@section('admin_content')
    @php
        $imageURL = _assetUrl('upload_path.ADMIN_NO_IMAGE_FOUND');
        if (
            !blank($resultArr->wedding_photo) &&
            _checkStorageFileExists('upload_path.SUCCESS_STORY_IMAGE_URL', $resultArr->wedding_photo)
        ) {
            $imageURL = _assetUrl('upload_path.SUCCESS_STORY_IMAGE_URL') . $resultArr->wedding_photo;
        }

        $statusClass = match ($resultArr->status) {
            'APPROVED' => 'bg-label-success',
            'UNAPPROVED' => 'bg-label-danger',
            'PENDING' => 'bg-label-warning',
            default => 'bg-label-secondary',
        };
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Header Card -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">

                        <div class="col-md-3">
                            <img src="{{ $imageURL }}" class="rounded border w-100"
                                style="object-fit:cover; max-height:180px;">
                        </div>

                        <div class="col-md-9">
                            <h3 class="mb-1">
                                {{ _displayNotAvailable($resultArr->bridename) }}
                                <span class="text-muted"> & </span>
                                {{ _displayNotAvailable($resultArr->groomname) }}
                            </h3>

                            <div class="text-muted mb-2">
                                Bride ID: <code>{{ _displayNotAvailable($resultArr->brideid) }}</code> |
                                Groom ID: <code>{{ _displayNotAvailable($resultArr->groomid) }}</code>
                            </div>

                            <div class="d-flex align-items-center gap-3">
                                <span class="badge {{ $statusClass }} rounded-pill px-3 py-2">
                                    {{ $resultArr->status }}
                                </span>

                                <span class="text-muted small">
                                    Married on {{ _displayDate($resultArr->marriagedate, 'j F, Y') }}
                                </span>

                                <span class="text-muted small">
                                    Created {{ _displayDate($resultArr->created_at, 'j F, Y h:i A') }}
                                </span>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            <div class="row">

                <!-- Success Message -->
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light fw-semibold">
                            Success Story Message
                        </div>
                        <div class="card-body" style="font-size:15px; line-height:1.8;">
                            {{ _displayNotAvailable($resultArr->successmessage) }}
                        </div>
                    </div>
                </div>

                <!-- Meta + SEO -->
                <div class="col-lg-4 mb-4">

                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light fw-semibold">
                            Story Details
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-5 text-muted">
                                    Slug
                                </div>
                                <div class="col-7 fw-semibold">
                                    {{ _displayNotAvailable($resultArr->slug) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-header bg-light fw-semibold">
                            SEO Information
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-5 text-muted">
                                    SEO Title
                                </div>
                                <div class="col-7 fw-semibold">
                                    {{ _displayNotAvailable($resultArr->seo_title) }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-5 text-muted">
                                    SEO Keywords
                                </div>
                                <div class="col-7 fw-semibold">
                                    {{ _displayNotAvailable($resultArr->seo_keywords) }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-5 text-muted">
                                    SEO Description
                                </div>
                                <div class="col-7 fw-semibold">
                                    {{ _displayNotAvailable($resultArr->seo_description) }}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
@endsection
