@php
    $announcements = \App\Models\AnnouncementBanner::active()->lang('en')->latest()->get();
@endphp

<!-- Announcement Modal -->
@if ($announcements->count())
    <div class="modal fade announcement-modal" id="announcementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal" aria-label="Close">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>

                <div class="announcement-slider-wrapper">
                    <div class="announcement-slider">
                        @foreach ($announcements as $announcement)
                            <div class="announcement-slide">
                                <div class="announcement-image-area">

                                    <div class="announcement-banner-wrapper">

                                        <img src="{{ _assetUrl('upload_path.ANNOUNCEMENT_IMAGE_URL') . $announcement->image }}"
                                            alt="{{ $announcement->title }}">

                                        @if ($announcements->count() > 1)
                                            <button type="button" class="announcement-arrow announcement-prev-arrow"
                                                aria-label="Previous announcement">
                                                <svg viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M15 18l-6-6 6-6" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            </button>

                                            <button type="button" class="announcement-arrow announcement-next-arrow"
                                                aria-label="Next announcement">
                                                <svg viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M9 6l6 6-6 6" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                        @endif

                                    </div>

                                </div>

                                <div class="announcement-body">
                                    <h2 class="announcement-title">{{ $announcement->title }}</h2>
                                    <p class="announcement-text">{{ $announcement->description }}</p>
                                    @if ($announcement->link)
                                        <a target="_blank" href="{{ $announcement->link }}"
                                            class="comman-bg-btn px-5 m-auto">{{ __('messages.lbl_explore_now') }}</a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const el = document.getElementById('announcementModal');
            if (!el) return;

            const todayKey = 'announcement_seen_' + new Date().toISOString().slice(0, 10);
            const alreadySeen = localStorage.getItem(todayKey);
            const $slider = jQuery('.announcement-slider');
            const $wrapper = jQuery('.announcement-slider-wrapper');
            const myModal = new bootstrap.Modal(el);

            el.addEventListener('hidden.bs.modal', function() {
                localStorage.setItem(todayKey, 'yes');
            });

            function initSlider() {
                if (!$slider.length) return;

                $slider.on('init', function() {
                    $wrapper.addClass('is-ready');
                });

                $slider.slick({
                    dots: false,
                    arrows: false, // using our own custom, per-slide click handlers instead
                    infinite: true,
                    speed: 400,
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    adaptiveHeight: true
                });

                // delegate, since arrows exist on every slide (including cloned ones)
                $slider.on('click', '.announcement-prev-arrow', function() {
                    $slider.slick('slickPrev');
                });
                $slider.on('click', '.announcement-next-arrow', function() {
                    $slider.slick('slickNext');
                });
            }

            if (alreadySeen) return;

            el.addEventListener('shown.bs.modal', function onShown() {
                initSlider();
                el.removeEventListener('shown.bs.modal', onShown);
            });

            myModal.show();
        });
    </script>
@endpush