@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')
@section('admin_content')
    @php
        $authUserType = Auth::user()->type;
    @endphp
    @push('styles')
        <style>
            :root {
                --clr-today: #4c6ef5;
                --clr-total: #f59f00;
                --clr-fresh: #12b886;
                --clr-repeat: #ae3ec9;
                --clr-open: #868e96;
                --clr-close: #e8590c;
            }

            /* ============ Metric strip (overall admin stats) ============ */
            .metric-strip {
                display: grid;
                grid-template-columns: repeat(6, 1fr);
                gap: 16px;
                margin-bottom: 32px;
            }

            .metric-card {
                display: flex;
                align-items: center;
                gap: 12px;
                background: #ffffff;
                border-radius: 10px;
                padding: 14px 16px;
                border: 1px solid #eef0f2;
                box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
                transition: box-shadow 0.2s ease, transform 0.2s ease;
                text-decoration: none;
            }

            .metric-card:hover {
                box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
                transform: translateY(-2px);
            }

            .metric-icon {
                flex: 0 0 auto;
                width: 42px;
                height: 42px;
                border-radius: 9px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .metric-icon svg {
                width: 20px;
                height: 20px;
                stroke-width: 2;
            }

            .metric-text h4 {
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: #8a94a6;
                margin: 0 0 3px;
            }

            .metric-text p {
                font-size: 21px;
                font-weight: 700;
                margin: 0;
                color: #1f2937;
                line-height: 1;
            }

            .metric-icon {
                box-shadow: 0 4px 10px rgba(15, 23, 42, 0.12);
            }

            .m-today .metric-icon {
                background: linear-gradient(135deg, #5c7cfa, #4c6ef5);
                color: #fff;
            }

            .m-total .metric-icon {
                background: linear-gradient(135deg, #ffb703, #f59f00);
                color: #fff;
            }

            .m-fresh .metric-icon {
                background: linear-gradient(135deg, #20c997, #12b886);
                color: #fff;
            }

            .m-repeat .metric-icon {
                background: linear-gradient(135deg, #cc5de8, #ae3ec9);
                color: #fff;
            }

            .m-open .metric-icon {
                background: linear-gradient(135deg, #adb5bd, #868e96);
                color: #fff;
            }

            .m-close .metric-icon {
                background: linear-gradient(135deg, #ff922b, #e8590c);
                color: #fff;
            }

            .metric-card {
                border-left: 3px solid transparent;
            }

            .m-today {
                border-left-color: var(--clr-today);
            }

            .m-total {
                border-left-color: var(--clr-total);
            }

            .m-fresh {
                border-left-color: var(--clr-fresh);
            }

            .m-repeat {
                border-left-color: var(--clr-repeat);
            }

            .m-open {
                border-left-color: var(--clr-open);
            }

            .m-close {
                border-left-color: var(--clr-close);
            }

            .m-today p {
                color: var(--clr-today);
            }

            .m-total p {
                color: var(--clr-total);
            }

            .m-fresh p {
                color: var(--clr-fresh);
            }

            .m-repeat p {
                color: var(--clr-repeat);
            }

            .m-open p {
                color: var(--clr-open);
            }

            .m-close p {
                color: var(--clr-close);
            }

            /* ============ Staff roster ============ */
            .staff-roster {
                display: flex;
                flex-direction: column;
                gap: 18px;
            }

            .staff-row {
                display: flex;
                align-items: center;
                gap: 22px;
                background: #ffffff;
                border-radius: 14px;
                padding: 18px 22px;
                border: 1px solid #eef0f2;
                box-shadow: 0 1px 4px rgba(15, 23, 42, 0.04);
                transition: box-shadow 0.25s ease;
                flex-wrap: wrap;
            }

            .staff-row:hover {
                box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
            }

            .staff-identity {
                display: flex;
                align-items: center;
                gap: 14px;
                flex: 0 0 auto;
                min-width: 210px;
            }

            .staff-identity img {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                object-fit: cover;
                border: 3px solid #f1f3f5;
            }

            .staff-identity h4 {
                font-size: 15px;
                font-weight: 700;
                color: #1f2937;
                margin: 0;
            }

            .staff-identity .staff-tag {
                display: inline-block;
                font-size: 10px;
                font-weight: 700;
                color: #4c6ef5;
                background: rgba(76, 110, 245, 0.1);
                border-radius: 5px;
                padding: 1px 6px;
                margin-left: 6px;
                letter-spacing: 0.3px;
            }

            .staff-identity small {
                display: block;
                color: #9aa3af;
                font-size: 12.5px;
                margin-top: 2px;
            }

            .staff-divider {
                width: 1px;
                align-self: stretch;
                background: #eef0f2;
                margin: 0 4px;
            }

            .staff-chips {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
                flex: 1;
            }

            .chip {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 8px 14px;
                border-radius: 999px;
                text-decoration: none;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
                border: 1px solid transparent;
            }

            a.chip:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 10px rgba(15, 23, 42, 0.1);
            }

            .chip .dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                flex: 0 0 auto;
                box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.05);
            }

            .chip .chip-label {
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.3px;
            }

            .chip .chip-value {
                font-size: 15px;
                font-weight: 800;
            }

            .chip-today {
                background: rgba(76, 110, 245, 0.22);
            }

            .chip-today .dot {
                background: var(--clr-today);
            }

            .chip-today .chip-label {
                color: #3b56d9;
            }

            .chip-today .chip-value {
                color: #2d43b3;
            }

            .chip-total {
                background: rgba(245, 159, 0, 0.24);
            }

            .chip-total .dot {
                background: var(--clr-total);
            }

            .chip-total .chip-label {
                color: #ad7300;
            }

            .chip-total .chip-value {
                color: #8f5f00;
            }

            .chip-fresh {
                background: rgba(18, 184, 134, 0.22);
            }

            .chip-fresh .dot {
                background: var(--clr-fresh);
            }

            .chip-fresh .chip-label {
                color: #0b8560;
            }

            .chip-fresh .chip-value {
                color: #08704f;
            }

            .chip-repeat {
                background: rgba(174, 62, 201, 0.22);
            }

            .chip-repeat .dot {
                background: var(--clr-repeat);
            }

            .chip-repeat .chip-label {
                color: #832b98;
            }

            .chip-repeat .chip-value {
                color: #6d1f80;
            }

            .chip-open {
                background: rgba(134, 142, 150, 0.26);
            }

            .chip-open .dot {
                background: var(--clr-open);
            }

            .chip-open .chip-label {
                color: #5a6268;
            }

            .chip-open .chip-value {
                color: #40464b;
            }

            .chip-close {
                background: rgba(232, 89, 12, 0.22);
            }

            .chip-close .dot {
                background: var(--clr-close);
            }

            .chip-close .chip-label {
                color: #b1420a;
            }

            .chip-close .chip-value {
                color: #963707;
            }

            .section-heading {
                font-size: 13px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.6px;
                color: #9aa3af;
                margin: 0 0 14px 2px;
            }

            @media (max-width: 992px) {
                .metric-strip {
                    grid-template-columns: repeat(3, 1fr);
                }
            }

            @media (max-width: 576px) {
                .metric-strip {
                    grid-template-columns: repeat(2, 1fr);
                }

                .staff-divider {
                    display: none;
                }
            }

            .staff-avatar-wrap {
                position: relative;
                flex: 0 0 auto;
            }

            .staff-avatar-wrap img {
                width: 54px;
                height: 54px;
                border-radius: 50%;
                object-fit: cover;
                display: block;
                border: 3px solid #fff;
                box-shadow: 0 0 0 2px #eef0f2;
            }
        </style>
    @endpush

    <div class="container-xxl flex-grow-1 container-p-y">
        @if ($authUserType == 'Admin')
            <p class="section-heading">Overview</p>
            <div class="metric-strip">
                <div class="metric-card m-today">
                    <span class="metric-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round"
                            stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" />
                            <path d="M16 2v4M8 2v4M3 10h18" />
                        </svg>
                    </span>
                    <div class="metric-text">
                        <h4>Today Followup</h4>
                        <p>{{ $leadData['today_followup'] }}</p>
                    </div>
                </div>

                <a href="{{ route('admin.leadGeneration.index') }}" target="_blank" class="metric-card m-total">
                    <span class="metric-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </span>
                    <div class="metric-text">
                        <h4>Total Leads</h4>
                        <p>{{ $leadData['total_lead'] }}</p>
                    </div>
                </a>

                <a href="{{ route('admin.leadGeneration.freshFollowUp') }}" target="_blank" class="metric-card m-fresh">
                    <span class="metric-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M12 2v20M17 5l-5-3-5 3M17 19l-5 3-5-3" />
                        </svg>
                    </span>
                    <div class="metric-text">
                        <h4>Fresh Followup</h4>
                        <p>{{ $leadData['fresh_followup'] }}</p>
                    </div>
                </a>

                <a href="{{ route('admin.leadGeneration.repeatedFollowUp') }}" target="_blank" class="metric-card m-repeat">
                    <span class="metric-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M17 1l4 4-4 4M3 11V9a4 4 0 0 1 4-4h14M7 23l-4-4 4-4M21 13v2a4 4 0 0 1-4 4H3" />
                        </svg>
                    </span>
                    <div class="metric-text">
                        <h4>Repeated</h4>
                        <p>{{ $leadData['repeat_followup'] }}</p>
                    </div>
                </a>

                <a href="{{ route('admin.leadGeneration.index') }}" target="_blank" class="metric-card m-open">
                    <span class="metric-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
                        </svg>
                    </span>
                    <div class="metric-text">
                        <h4>Open Lead</h4>
                        <p>{{ $leadData['open_followup'] }}</p>
                    </div>
                </a>

                <a href="{{ route('admin.leadGeneration.closedLeads') }}" target="_blank" class="metric-card m-close">
                    <span class="metric-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M20 6L9 17l-5-5" />
                        </svg>
                    </span>
                    <div class="metric-text">
                        <h4>Close Lead</h4>
                        <p>{{ $leadData['close_followup'] }}</p>
                    </div>
                </a>
            </div>
        @endif

        <p class="section-heading">Staff Wise Breakdown</p>
        <div class="staff-roster">
            @foreach ($staffList as $staff)
                <div class="staff-row">
                    <div class="staff-identity">
                        <div class="staff-avatar-wrap">
                            @php
                                $profileImageUrl = _assetUrl('upload_path.IDPROOF_ADMIN_NO_IMAGE_FOUND');
                                if (
                                    !blank($staff['profile_image']) &&
                                    _checkStorageFileExists('upload_path.STAFF_IMAGE_URL', $staff['profile_image'])
                                ) {
                                    $profileImageUrl = $staff['profile_image'];
                                }
                            @endphp
                            <img src="{{ $profileImageUrl }}" alt="{{ $staff['username'] }} Profile Image">
                        </div>
                        <div>
                            <h4>{{ $staff['username'] }}<span class="staff-tag">{{ $staff['staff_prefix'] }}</span></h4>
                            <small>{{ $staff['email'] }}</small>
                        </div>
                    </div>

                    <div class="staff-divider"></div>

                    <div class="staff-chips">
                        <div class="chip chip-today">
                            <span class="dot"></span>
                            <span class="chip-label">Today</span>
                            <span class="chip-value">{{ $staff['today_followup'] }}</span>
                        </div>

                        <a href="{{ route('admin.leadGeneration.index') }}" target="_blank" class="chip chip-total">
                            <span class="dot"></span>
                            <span class="chip-label">Total</span>
                            <span class="chip-value">{{ $staff['total_lead'] }}</span>
                        </a>

                        <a href="{{ route('admin.leadGeneration.freshFollowUp') }}" target="_blank"
                            class="chip chip-fresh">
                            <span class="dot"></span>
                            <span class="chip-label">Fresh</span>
                            <span class="chip-value">{{ $staff['fresh_followup'] }}</span>
                        </a>

                        <a href="{{ route('admin.leadGeneration.repeatedFollowUp') }}" target="_blank"
                            class="chip chip-repeat">
                            <span class="dot"></span>
                            <span class="chip-label">Repeat</span>
                            <span class="chip-value">{{ $staff['repeat_followup'] }}</span>
                        </a>

                        <a href="{{ route('admin.leadGeneration.index') }}" target="_blank">
                            <div class="chip chip-open">
                                <span class="dot"></span>
                                <span class="chip-label">Open</span>
                                <span class="chip-value">{{ $staff['open_followup'] }}</span>
                            </div>
                        </a>

                        <a href="{{ route('admin.leadGeneration.index') }}" target="_blank" class="chip chip-close">
                            <span class="dot"></span>
                            <span class="chip-label">Closed</span>
                            <span class="chip-value">{{ $staff['close_followup'] }}</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
