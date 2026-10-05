@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')
@section('admin_content')
    @php
        function eventImg($img)
        {
            $url = _assetUrl('upload_path.ADMIN_NO_IMAGE_FOUND');
            if (!blank($img) && _checkStorageFileExists('upload_path.EVENT_IMAGE_URL', $img)) {
                $url = _assetUrl('upload_path.EVENT_IMAGE_URL') . $img;
            }
            return $url;
        }

        $statusClass = $resultArr->status == 'APPROVED' ? 'bg-label-success' : 'bg-label-danger';
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Header Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap">

                    <div>
                        <h3 class="mb-1">{{ _displayNotAvailable($resultArr->title) }}</h3>
                        <div class="text-muted">
                            {{ _displayNotAvailable(_displayDate($resultArr->event_date, 'j F, Y')) }}
                            &nbsp; | &nbsp;
                            {{ _displayNotAvailable($resultArr->event_time) }}
                        </div>
                        <div class="mt-1">
                            📍 {{ _displayNotAvailable($resultArr->venue) }}
                        </div>
                    </div>

                    <div class="text-end">
                        <span class="badge {{ $statusClass }} rounded-pill px-3 py-2">
                            {{ $resultArr->status }}
                        </span>
                        <div class="text-muted small mt-2">
                            Created {{ _displayDate($resultArr->created_at, 'j F, Y h:i A') }}
                        </div>
                    </div>

                </div>
            </div>

            <div class="row">

                <!-- Description -->
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-light fw-semibold">
                            Event Description
                        </div>
                        <div class="card-body" style="line-height:1.9;">
                            {!! html_entity_decode(_displayNotAvailable($resultArr->description)) !!}
                        </div>
                    </div>
                </div>

                <!-- Gallery -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-light fw-semibold">
                            Event Gallery
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                @foreach ([$resultArr->image, $resultArr->image_2, $resultArr->image_3, $resultArr->image_4] as $img)
                                    <div class="col-6">
                                        <a href="{{ eventImg($img) }}" target="_blank">
                                            <img src="{{ eventImg($img) }}" class="w-100 rounded border"
                                                style="height:110px;object-fit:cover;">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection
