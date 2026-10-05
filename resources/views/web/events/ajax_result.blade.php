@push('styles')
    <style>
        .event-gried-text p {
            color: var(--white-color) !important;
            font-size: 14px;
        }
    </style>
@endpush
<div class="row px-1 mt-3">
    @forelse($events as $event)
        @php
            $eventImage = _assetUrl('upload_path.WEB_NO_IMAGE_FOUND');
            if (!blank($event->image) && _checkStorageFileExists('upload_path.EVENT_IMAGE_URL', $event->image)) {
                $eventImage = _assetUrl('upload_path.EVENT_IMAGE_URL') . $event->image;
            }
        @endphp
        <div class="col-lg-4 col-md-6 px-2 mb-3">
            <div class="blog-singles-mng position-relative">
                <a href="{{ route('web.event.show', $event->id) }}">
                    <div class="blogs-multieswagg mb-2 position-relative">
                        <img src="{{ $eventImage }}" alt="" class="comman-blogs">
                    </div>
                </a>
                <div class="blogs_contentsettextvdf pt-lg-1">
                    <div class="fts-13 fw-4 white-color70-n">{{ _displayDate($event->event_date, 'j F, Y') }} <span
                            class="mx-1 d-inline-block">|</span> {{ _displayDate($event->event_time, 'h:i A') }}</div>
                    <div class="event-flex-content d-flex justify-content-between gap-3 mt-2">
                        <div class="event-gried-text">
                            <h4 class="fts-18 fw-6 white-color-n">{{ $event->title }}</h4>
                            <p class="fts-14 fw-4 white-color70-n mt-1">{!! $event->description !!}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        @include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.noDataFound', [
            'message' => __('messages.lbl_no_events_found'),
        ])
    @endforelse
</div>
<!-- pagination  -->
@if ($events->hasPages())
    {{ $events->links(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.pagination') }}
@endif
