@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@push('styles')
    <style>
        .event-planer-contents p {
            color: var(--white-color) !important;
            font-size: 14px;
        }

        /* Registered Attendees Card */
        /* Registered Attendees Card */
        .attendees-card {
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        :root:not(.light-mode) .attendees-card {
            border-color: var(--common-border, rgba(255, 255, 255, 0.1));
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: none;
        }
        .attendees-card:hover {
            border-color: rgba(var(--primary-color-rgb, 13, 86, 222), 0.35);
        }
        .attendees-icon-badge {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: rgba(var(--primary-color-rgb, 13, 86, 222), 0.12);
            color: var(--primary-color);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }
        .attendees-card-title {
            color: #1e293b;
            font-size: 16px;
            font-weight: 700;
        }
        :root:not(.light-mode) .attendees-card-title {
            color: #ffffff;
        }
        .attendees-card-sub {
            color: #64748b;
            font-size: 12px;
        }
        :root:not(.light-mode) .attendees-card-sub {
            color: rgba(255, 255, 255, 0.7);
        }
        .attendee-search-input {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #1e293b;
            border-radius: 30px;
            padding: 6px 14px 6px 36px;
            font-size: 13px;
            outline: none;
            transition: all 0.25s ease;
            width: 200px;
        }
        :root:not(.light-mode) .attendee-search-input {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.15);
            color: #ffffff;
        }
        .attendee-search-input:focus {
            background: #ffffff;
            border-color: var(--primary-color);
            width: 230px;
            color: #1e293b;
            box-shadow: 0 0 0 3px rgba(var(--primary-color-rgb, 13, 86, 222), 0.15);
        }
        :root:not(.light-mode) .attendee-search-input:focus {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }
        .attendees-chips-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            max-height: 290px;
            overflow-y: auto;
            padding: 6px 4px;
        }
        .attendees-chips-grid::-webkit-scrollbar {
            width: 6px;
        }
        .attendees-chips-grid::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.03);
            border-radius: 10px;
        }
        .attendees-chips-grid::-webkit-scrollbar-thumb {
            background: rgba(var(--primary-color-rgb, 13, 86, 222), 0.35);
            border-radius: 10px;
        }

        /* Other Users Chip: High contrast, clearly legible, clickable */
        .attendee-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 14px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.3px;
            text-decoration: none !important;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #1e293b !important;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.04);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            user-select: none;
        }
        :root:not(.light-mode) .attendee-chip {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.18);
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }
        .attendee-chip:hover {
            transform: translateY(-2px);
            background: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: #ffffff !important;
            box-shadow: 0 6px 18px rgba(var(--primary-color-rgb, 13, 86, 222), 0.35);
        }
        .attendee-chip:hover .attendee-matri-text {
            color: #ffffff !important;
        }
        .attendee-matri-text {
            color: #1e293b;
            font-weight: 600;
            font-size: 13px;
            transition: color 0.2s ease;
        }
        :root:not(.light-mode) .attendee-matri-text {
            color: #ffffff;
        }
        .attendee-avatar-dot {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: rgba(var(--primary-color-rgb, 13, 86, 222), 0.12);
            color: var(--primary-color);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }
        .attendee-chip:hover .attendee-avatar-dot {
            background: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }
        .attendee-chip .attendee-arrow {
            font-size: 13px;
            opacity: 0.5;
            color: #64748b;
            transition: all 0.2s ease;
        }
        :root:not(.light-mode) .attendee-chip .attendee-arrow {
            color: rgba(255, 255, 255, 0.7);
        }
        .attendee-chip:hover .attendee-arrow {
            opacity: 1;
            color: #ffffff;
            transform: translate(2px, -2px);
        }

        /* Own ID Chip: Dull, muted, grayed out, non-clickable */
        .attendee-chip.attendee-self {
            background: #f1f5f9 !important;
            border: 1px dashed #cbd5e1 !important;
            color: #94a3b8 !important;
            box-shadow: none !important;
            cursor: default !important;
            font-weight: 500;
            opacity: 0.85;
        }
        :root:not(.light-mode) .attendee-chip.attendee-self {
            background: rgba(255, 255, 255, 0.03) !important;
            border-color: rgba(255, 255, 255, 0.15) !important;
            color: rgba(255, 255, 255, 0.5) !important;
        }
        .attendee-chip.attendee-self:hover {
            transform: none !important;
            background: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: #94a3b8 !important;
            box-shadow: none !important;
        }
        :root:not(.light-mode) .attendee-chip.attendee-self:hover {
            background: rgba(255, 255, 255, 0.03) !important;
            border-color: rgba(255, 255, 255, 0.15) !important;
            color: rgba(255, 255, 255, 0.5) !important;
        }
        .attendee-chip.attendee-self .attendee-matri-text {
            color: #64748b !important;
        }
        :root:not(.light-mode) .attendee-chip.attendee-self .attendee-matri-text {
            color: rgba(255, 255, 255, 0.5) !important;
        }
        .attendee-self .attendee-avatar-dot {
            background: #e2e8f0 !important;
            color: #94a3b8 !important;
        }
        :root:not(.light-mode) .attendee-self .attendee-avatar-dot {
            background: rgba(255, 255, 255, 0.08) !important;
            color: rgba(255, 255, 255, 0.45) !important;
        }
        .attendee-self-pill {
            background: #e2e8f0;
            color: #64748b;
            padding: 1px 7px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        :root:not(.light-mode) .attendee-self-pill {
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.6);
        }
        .attendees-load-more-btn {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #334155;
            padding: 7px 18px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        :root:not(.light-mode) .attendees-load-more-btn {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.15);
            color: #ffffff;
        }
        .attendees-load-more-btn:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: #ffffff;
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

                                        {{-- Registered Members / Attendees --}}
                                        @if (!empty($allUsersRegisteredMatriId) && count($allUsersRegisteredMatriId) > 0)
                                            <div class="attendees-card p-3 p-lg-4 mt-4">
                                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 pb-3 border-bottom" style="border-color: rgba(148, 163, 184, 0.2) !important;">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="attendees-icon-badge">
                                                            <iconify-icon icon="solar:users-group-two-rounded-bold-duotone"></iconify-icon>
                                                        </div>
                                                        <div>
                                                            <h5 class="attendees-card-title mb-0 d-flex align-items-center gap-2">
                                                                {{ __('messages.lbl_registered_members') }}
                                                                <span class="badge rounded-pill bg-primary px-2 py-1 fts-11 fw-6">
                                                                    {{ count($allUsersRegisteredMatriId) }}
                                                                </span>
                                                            </h5>
                                                            <p class="attendees-card-sub mb-0 mt-1">
                                                                {{ __('messages.lbl_members_who_booked') ?? 'Members registered for this event' }}
                                                            </p>
                                                        </div>
                                                    </div>

                                                    {{-- Search filter if multiple attendees --}}
                                                    @if (count($allUsersRegisteredMatriId) > 4)
                                                        <div class="position-relative">
                                                            <iconify-icon icon="solar:magnifer-linear" class="position-absolute text-muted" style="left: 12px; top: 50%; transform: translateY(-50%); font-size: 14px; pointer-events: none;"></iconify-icon>
                                                            <input type="text" id="searchAttendeeInput" class="attendee-search-input" placeholder="Search Matri ID...">
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="attendees-chips-grid mt-3" id="attendeesGrid">
                                                    @php
                                                        $currentMatriId = auth()->user()?->matri_id;
                                                        // Put the logged-in user at the beginning if present
                                                        $orderedAttendees = collect($allUsersRegisteredMatriId)->sortByDesc(function($item) use ($currentMatriId) {
                                                            $mid = is_array($item) ? ($item['matri_id'] ?? '') : $item;
                                                            return ($currentMatriId && $mid === $currentMatriId) ? 1 : 0;
                                                        })->values()->all();
                                                    @endphp

                                                    @foreach ($orderedAttendees as $index => $attendee)
                                                        @php
                                                            $matriId = is_array($attendee) ? ($attendee['matri_id'] ?? '') : $attendee;
                                                            $memberId = is_array($attendee) ? ($attendee['member_id'] ?? null) : null;
                                                            $isSelf = ($currentMatriId && $matriId === $currentMatriId);
                                                            // For huge lists (up to 2000), render initial 80 and let Load More or Search show others
                                                            $isInitialHidden = ($index >= 80);
                                                        @endphp

                                                        @if ($isSelf)
                                                            {{-- Own ID: Dull, muted, non-clickable --}}
                                                            <div class="attendee-chip attendee-self {{ $isInitialHidden ? 'attendee-paged d-none' : '' }}"
                                                                data-matri-id="{{ strtolower($matriId) }}"
                                                                title="Your Matri ID">
                                                                <span class="attendee-avatar-dot">
                                                                    <iconify-icon icon="solar:user-circle-bold"></iconify-icon>
                                                                </span>
                                                                <span class="attendee-matri-text">{{ $matriId }}</span>
                                                                <span class="attendee-self-pill">You</span>
                                                            </div>
                                                        @elseif ($memberId)
                                                            {{-- Other User: Bright, high contrast, clickable to profile --}}
                                                            <a href="{{ route('web.userProfile.index', _encrypt($memberId)) }}"
                                                                class="attendee-chip {{ $isInitialHidden ? 'attendee-paged d-none' : '' }}"
                                                                data-matri-id="{{ strtolower($matriId) }}"
                                                                title="View profile of {{ $matriId }}"
                                                                target="_blank">
                                                                <span class="attendee-avatar-dot">
                                                                    <iconify-icon icon="solar:user-bold"></iconify-icon>
                                                                </span>
                                                                <span class="attendee-matri-text">{{ $matriId }}</span>
                                                                <iconify-icon icon="solar:arrow-right-up-linear" class="attendee-arrow fts-12"></iconify-icon>
                                                            </a>
                                                        @else
                                                            {{-- Fallback if memberId not available --}}
                                                            <div class="attendee-chip {{ $isInitialHidden ? 'attendee-paged d-none' : '' }}"
                                                                data-matri-id="{{ strtolower($matriId) }}">
                                                                <span class="attendee-avatar-dot">
                                                                    <iconify-icon icon="solar:user-bold"></iconify-icon>
                                                                </span>
                                                                <span class="attendee-matri-text">{{ $matriId }}</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>

                                                @if (count($orderedAttendees) > 80)
                                                    <div class="text-center pt-3 pb-1" id="attendeesLoadMoreWrapper">
                                                        <button type="button" class="attendees-load-more-btn" id="loadMoreAttendeesBtn">
                                                            <span id="loadMoreText">Show All ({{ count($orderedAttendees) }})</span>
                                                            <iconify-icon icon="solar:alt-arrow-down-linear" class="ms-1"></iconify-icon>
                                                        </button>
                                                    </div>
                                                @endif

                                                <div id="noAttendeeMatch" class="d-none text-center py-3">
                                                    <p class="fts-13 white-color70-n mb-0">No matching Matri ID found</p>
                                                </div>
                                            </div>
                                        @endif
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
                                        @auth('web')
                                            <button class="comman-bg-btn fts-15 w-100" type="button" id="authBookNowBtn">
                                                {{ __('messages.lbl_book_now') }}
                                            </button>
                                        @else
                                            <a href="{{ route('web.login.index') }}" class="comman-bg-btn fts-15 w-100 text-center text-decoration-none d-block">
                                                {{ __('messages.lbl_book_now') }}
                                            </a>
                                        @endauth
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

        // Search attendee Matri IDs
        const searchInput = document.getElementById('searchAttendeeInput');
        const loadMoreWrapper = document.getElementById('attendeesLoadMoreWrapper');
        const loadMoreBtn = document.getElementById('loadMoreAttendeesBtn');

        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', function() {
                const hiddenChips = document.querySelectorAll('#attendeesGrid .attendee-paged');
                hiddenChips.forEach(chip => {
                    chip.classList.remove('d-none', 'attendee-paged');
                    chip.style.display = 'inline-flex';
                });
                if (loadMoreWrapper) {
                    loadMoreWrapper.classList.add('d-none');
                }
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const term = this.value.trim().toLowerCase();
                const chips = document.querySelectorAll('#attendeesGrid .attendee-chip');
                let matchCount = 0;

                if (term === '') {
                    // Reset to initial state (respecting whether load more was clicked)
                    const isAllExpanded = !loadMoreWrapper || loadMoreWrapper.classList.contains('d-none');
                    chips.forEach((chip, idx) => {
                        if (isAllExpanded || idx < 80) {
                            chip.classList.remove('d-none');
                            chip.style.display = 'inline-flex';
                        } else {
                            chip.classList.add('d-none');
                            chip.style.display = 'none';
                        }
                    });
                    if (loadMoreWrapper && !isAllExpanded) {
                        loadMoreWrapper.classList.remove('d-none');
                    }
                    const noMatchEl = document.getElementById('noAttendeeMatch');
                    if (noMatchEl) noMatchEl.classList.add('d-none');
                    return;
                }

                // If searching, hide load more wrapper and filter across all chips
                if (loadMoreWrapper) {
                    loadMoreWrapper.classList.add('d-none');
                }

                chips.forEach(chip => {
                    const matriId = chip.getAttribute('data-matri-id') || '';
                    if (matriId.includes(term)) {
                        chip.classList.remove('d-none');
                        chip.style.display = 'inline-flex';
                        matchCount++;
                    } else {
                        chip.classList.add('d-none');
                        chip.style.display = 'none';
                    }
                });

                const noMatchEl = document.getElementById('noAttendeeMatch');
                if (noMatchEl) {
                    if (matchCount === 0) {
                        noMatchEl.classList.remove('d-none');
                    } else {
                        noMatchEl.classList.add('d-none');
                    }
                }
            });
        }
        // If logged in, clicking Book Now bypasses the modal and submits bookTicketForm with fix qty 1
        const authBookNowBtn = document.getElementById('authBookNowBtn');
        if (authBookNowBtn) {
            authBookNowBtn.addEventListener('click', function() {
                const qtySelect = document.getElementById('ticket_qty');
                if (qtySelect) {
                    qtySelect.value = '1';
                }
                document.getElementById('bookTicketForm').submit();
            });
        }
    </script>
@endpush
