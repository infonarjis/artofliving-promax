@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@push('styles')
    <style>
        .event-planer-contents p {
            color: var(--white-color) !important;
            font-size: 14px;
        }

        .event-planer-contents p {
            color: var(--white-color) !important;
            font-size: 14px;
        }
    </style>
@endpush
@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}
    <section class="common-section-bg pb-4 pb-lg-5">
        <div class="common-section-page">
            <div class="vendor-common-topbar event-bnr position-relative"></div>
            <div class="container">
                <div class="event-inner-section">
                    <div class="event-details-views">
                        <div class="row px-1">

                            {{-- ============================================================
                                 LEFT — Event images + details
                            ============================================================ --}}
                            <div class="col-xxl-9 col-lg-8 px-2">
                                <div class="common-bgwhite-main p-3">

                                    {{-- Images --}}
                                    <div class="event-side-img">
                                        @php
                                            $images = array_filter([
                                                $event->image,
                                                $event->image_2,
                                                $event->image_3,
                                                $event->image_4,
                                            ]);
                                        @endphp

                                        @if (count($images))
                                            @foreach ($images as $img)
                                                @if (_checkStorageFileExists('upload_path.EVENT_IMAGE_URL', $img))
                                                    <img src="{{ _assetUrl('upload_path.EVENT_IMAGE_URL') . $img }}"
                                                        alt="{{ $event->title }}" class="events-details-img">
                                                @else
                                                    <img src="{{ _assetUrl('upload_path.WEB_NO_IMAGE_FOUND') }}"
                                                        alt="{{ $event->title }}" class="events-details-img">
                                                @endif
                                            @endforeach
                                        @else
                                            <img src="{{ _assetUrl('upload_path.WEB_NO_IMAGE_FOUND') }}"
                                                alt="{{ $event->title }}" class="events-details-img">
                                        @endif
                                    </div>

                                    {{-- Title, date & price --}}
                                    <div class="event-details-contents mt-3">
                                        <div class="top-planner-heading d-flex justify-content-between gap-2 flex-wrap">
                                            <div class="top-left-content">
                                                <h4 class="fw-7 white-color-n fts-20">{{ $event->title }}</h4>
                                                <div class="fts-14 fw-4 white-color70-n mt-1">
                                                    {{ _displayDate($event->event_date, 'j F, Y') }}
                                                    <span class="mx-1 d-inline-block">|</span>
                                                    {{ _displayDate($event->event_time, 'h:i A') }}
                                                </div>
                                            </div>
                                            <div class="top-right-contents">
                                                <div class="pricing-vendors fts-14">
                                                    {{ $event->currency }}
                                                    <span class="d-block">{{ $event->ticket_price }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Description --}}
                                        <div class="event-planer-contents mt-2 mt-lg-3">
                                            <p class="mt-1 mt-lg-2 fts-14 fw-4 white-color-n">
                                                {!! _displayNotAvailable($event->description) !!}
                                            </p>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            {{-- ============================================================
                                 RIGHT — Actions, contact, social, map
                            ============================================================ --}}
                            <div class="col-xxl-3 col-lg-4 mt-3 mt-lg-0 px-2">

                                {{-- Book now button + contact info --}}
                                <div class="common-bgtransparent-main p-3">
                                    @if ($event->available_tickets > 0)
                                        <button class="comman-bg-btn fts-15 w-100" type="button" data-bs-toggle="modal"
                                            data-bs-target="#events-book">
                                            {{ __('messages.lbl_book_now') }}
                                        </button>
                                    @else
                                        <button class="comman-bg-btn fts-15 w-100 opacity-50" type="button" disabled>
                                            {{ __('messages.lbl_sold_out') }}
                                        </button>
                                    @endif

                                    <div class="vendor-contact-list">
                                        <div class="vendor-single-contact w-100 mt-4 d-flex gap-2 align-items-center">
                                            <div class="icon-contact-vendor">
                                                <iconify-icon icon="tdesign:location"></iconify-icon>
                                            </div>
                                            <p class="fts-14 fw-4 white-color-n">{{ $event->venue }}</p>
                                        </div>
                                        <div class="vendor-single-contact w-100 mt-4 d-flex gap-2 align-items-center">
                                            <div class="icon-contact-vendor">
                                                <iconify-icon icon="fluent:call-16-regular"></iconify-icon>
                                            </div>
                                            <p class="fts-14 fw-4 white-color-n">{{ $event->contact_number }}</p>
                                        </div>
                                        <div class="vendor-single-contact w-100 mt-4 d-flex gap-2 align-items-center">
                                            <div class="icon-contact-vendor">
                                                <iconify-icon icon="hugeicons:mail-01"></iconify-icon>
                                            </div>
                                            <p class="fts-14 fw-4 white-color-n">{{ $event->contact_email }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Social links --}}
                                @php
                                    $socialLinks = [
                                        [
                                            'url' => $event->event_instagram_link,
                                            'icon' => 'hugeicons:instagram',
                                            'title' => __('messages.lbl_instagram'),
                                        ],
                                        [
                                            'url' => $event->event_facebook_link,
                                            'icon' => 'hugeicons:facebook-01',
                                            'title' => __('messages.lbl_facebook'),
                                        ],
                                        [
                                            'url' => $event->event_youtube_link,
                                            'icon' => 'hugeicons:youtube',
                                            'title' => __('messages.lbl_youtube'),
                                        ],
                                        [
                                            'url' => $event->event_pinterest_link,
                                            'icon' => 'mingcute:pinterest-line',
                                            'title' => __('messages.lbl_pinterest'),
                                        ],
                                        [
                                            'url' => $event->event_twitter_link,
                                            'icon' => 'ri:twitter-x-fill',
                                            'title' => __('messages.lbl_twitter'),
                                        ],
                                    ];
                                    $activeSocials = array_filter($socialLinks, fn($s) => !blank($s['url']));
                                @endphp

                                @if (count($activeSocials))
                                    <div class="common-bgtransparent-main p-3 mt-3">
                                        <div class="vendor-social d-flex gap-2 justify-content-between flex-wrap mt-1">
                                            @foreach ($activeSocials as $social)
                                                <a href="{{ \Illuminate\Support\Str::startsWith($social['url'], ['http://', 'https://']) ? $social['url'] : 'https://' . $social['url'] }}"
                                                    rel="noopener noreferrer" target="_blank" class="fts-20"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{ $social['title'] }}">
                                                    <iconify-icon icon="{{ $social['icon'] }}"></iconify-icon>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Map --}}
                                <div class="common-bgtransparent-main p-3 mt-3">
                                    <div class="event-right-maps">
                                        @if (!blank($event->map_address))
                                            <iframe title="map"
                                                src="https://www.google.com/maps?q={{ urlencode($event->map_address) }}&output=embed"
                                                style="border:0;" allowfullscreen="" loading="lazy"
                                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                                        @else
                                            <iframe title="map"
                                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3672.8094570161666!2d72.49664067544367!3d22.99403321739227!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395e8534627869c7%3A0xbcf02454a61d9bc0!2sNarjis%20Infotech!5e0!3m2!1sen!2sin!4v1739792219338!5m2!1sen!2sin"
                                                style="border:0;" allowfullscreen="" loading="lazy"
                                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================================
         Book Event Modal
    ================================================================ --}}
    <div class="customsmallmodel_light couponsize modal fade" id="events-book" tabindex="-1"
        aria-labelledby="events-bookLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header mx-3 mx-lg-4 pt-4 pb-1">
                    <h1 class="fts-18 fw-6 white-color-n" id="events-bookLabel">
                        {{ __('messages.lbl_book_your_ticket') }}
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
                    </button>
                </div>

                <div class="modal_liteBody px-3 px-lg-4 py-3">

                    {{-- Summary bar --}}
                    <div class="common-bgprimary-main px-3 px-lg-4 py-2 py-lg-3">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="vendor-single-contact my-2 d-flex gap-2 align-items-center">
                                    <div class="icon-contact-vendor primary-icon">
                                        <iconify-icon icon="solar:calendar-linear"></iconify-icon>
                                    </div>
                                    <p class="fts-15 fw-5 white-color-n">
                                        {{ _displayDate($event->event_date, 'j F, Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="vendor-single-contact my-2 d-flex gap-2 align-items-center">
                                    <div class="icon-contact-vendor primary-icon">
                                        <iconify-icon icon="ion:time-outline"></iconify-icon>
                                    </div>
                                    <p class="fts-15 fw-5 white-color-n">
                                        {{ _displayDate($event->event_time, 'h:i A') }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="vendor-single-contact my-2 d-flex gap-2 align-items-center">
                                    <div class="icon-contact-vendor primary-icon">
                                        <iconify-icon icon="tdesign:location"></iconify-icon>
                                    </div>
                                    <p class="fts-15 fw-5 white-color-n">{{ $event->venue }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Short description --}}
                    <div class="event-planer-contents mt-2 mt-lg-3">
                        <p class="mt-1 mt-lg-2 fts-14 fw-4 white-color-n">
                            {!! \Illuminate\Support\Str::limit(strip_tags($event->description), 300) !!}
                        </p>
                    </div>

                    {{-- Qty + proceed form --}}
                    <form method="GET" action="{{ route('web.event.checkout', $event->id) }}" id="bookTicketForm">
                        <div class="row pt-1 pt-lg-2 align-items-end">
                            <div class="col-sm-9 mt-2">
                                <div class="placeQty_box mx-auto mx-md-0">
                                    <div class="price_icons-n d-flex align-items-center gap-2">
                                        <iconify-icon icon="hugeicons:coupon-percent" class="fts-20"></iconify-icon>
                                        <p class="fts-14 fw-5 white-color-n">
                                            {{ $event->currency }}
                                            <span class="fw-7" id="totalEventPrice">
                                                {{ $event->ticket_price }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="qty_select-opt">
                                        @php $maxTickets = max(1, $event->available_tickets); @endphp
                                        <select name="qty" id="ticket_qty" required class="ticket_qty"
                                            onchange="ticketPrice('{{ $event->ticket_price }}','{{ $event->currency }}')">
                                            <option value="">{{ _getLang('lbl_qty') }}</option>
                                            @for ($i = 1; $i <= $maxTickets; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3 mt-2">
                                <button class="comman-bg-btn fts-15 w-100" type="submit" id="proceedBtn">
                                    {{ __('messages.lbl_next') }}
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function ticketPrice(price, currency) {
            const qty = parseInt(document.getElementById('ticket_qty').value) || 0;
            const total = qty && price ? (parseFloat(price) * qty).toFixed(2) : parseFloat(price).toFixed(2);
            document.getElementById('totalEventPrice').innerText = total;
        }

        // Prevent form submit if no qty selected
        document.getElementById('bookTicketForm').addEventListener('submit', function(e) {
            const qty = document.getElementById('ticket_qty').value;
            if (!qty) {
                e.preventDefault();
                showToastMessage('error', '{{ __('messages.lbl_select_qty') ?? 'Please select a quantity.' }}');
            }
        });
    </script>
@endpush
