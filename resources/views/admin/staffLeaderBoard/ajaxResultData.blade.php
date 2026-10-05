<div class="staff-roster">
    @foreach ($staffList as $key => $staff)
        @php
            $rank = $key + 1;
            $rankRowClass = $rank == 1 ? 'rank-1' : ($rank == 2 ? 'rank-2' : ($rank == 3 ? 'rank-3' : ''));

            $ratingClean = trim(preg_replace('/[^\p{L}\s]/u', '', $staff->rating_text ?? ''));
            switch (true) {
                case str_contains($ratingClean, 'Outstanding'):
                    $ratingClass = 'rating-outstanding';
                    $ratingIcon =
                        '<path d="M8 21h8M12 17v4M7 4h10v4a5 5 0 0 1-10 0V4z"/><path d="M7 5H4a2 2 0 0 0 0 4h1.5M17 5h3a2 2 0 0 1 0 4h-1.5"/>';
                    break;
                case str_contains($ratingClean, 'Exceeds Expectations'):
                    $ratingClass = 'rating-exceeds';
                    $ratingIcon = '<path d="M23 6l-9.5 9.5-5-5L1 18"/><path d="M17 6h6v6"/>';
                    break;
                case str_contains($ratingClean, 'Meets Expectations'):
                    $ratingClass = 'rating-meets';
                    $ratingIcon = '<path d="M20 6L9 17l-5-5"/>';
                    break;
                case str_contains($ratingClean, 'Needs Improvement'):
                    $ratingClass = 'rating-needs';
                    $ratingIcon = '<circle cx="12" cy="12" r="9"/><path d="M12 8v4l2.5 1.5"/>';
                    break;
                case str_contains($ratingClean, 'Underperforming'):
                    $ratingClass = 'rating-under';
                    $ratingIcon =
                        '<path d="M12 9v4M12 17h.01M10.29 3.86l-8.18 14A2 2 0 0 0 3.82 21h16.36a2 2 0 0 0 1.71-3.14l-8.18-14a2 2 0 0 0-3.42 0z"/>';
                    break;
                default:
                    $ratingClass = 'rating-default';
                    $ratingIcon = '<circle cx="12" cy="12" r="9"/>';
            }
        @endphp
        <!-- User Section -->
        <div class="staff-row {{ $rankRowClass }}">
            <div class="rank-badge">
                @if ($rank <= 3)
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M8 21h8M12 17v4M7 4h10v4a5 5 0 0 1-10 0V4z" />
                        <path d="M7 5H4a2 2 0 0 0 0 4h1.5M17 5h3a2 2 0 0 1 0 4h-1.5" />
                    </svg>
                @else
                    #{{ $rank }}
                @endif
            </div>

            <div class="staff-identity">
                <div class="staff-avatar-wrap">
                    <img src="{{ $staff->profile_image }}" alt="{{ $staff->staff_prefix }}">
                </div>
                <div class="staff-identity-text">
                    <div class="staff-name-row">
                        <h4>{{ $staff->username }} - {{ $staff->staff_prefix }}</h4>
                    </div>
                    @if (!empty($ratingClean))
                        <span class="rating-badge {{ $ratingClass }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round"
                                stroke-linejoin="round">{!! $ratingIcon !!}</svg>
                            {{ $ratingClean }}
                        </span>
                    @endif
                    <small>{{ $staff->email }}</small>
                </div>
            </div>

            <div class="staff-divider"></div>

            <div class="staff-chips">
                <div class="chip chip-kpi">
                    <span class="dot"></span>
                    <span class="chip-label">KPI Score</span>
                    <span
                        class="chip-value">{{ number_format($staff->kpi_score, 2) }}{{ $staff->kpi_score > 0 ? '%' : '' }}</span>
                </div>

                <div class="chip chip-revenue">
                    <span class="dot"></span>
                    <span class="chip-label">Revenue Score</span>
                    <span
                        class="chip-value">{{ number_format($staff->revenue_score, 2) }}{{ $staff->revenue_score > 0 ? '%' : '' }}</span>
                </div>

                <div class="chip chip-final">
                    <span class="dot"></span>
                    <span class="chip-label">Final Score</span>
                    <span
                        class="chip-value">{{ number_format($staff->total_score, 2) }}{{ $staff->total_score > 0 ? '%' : '' }}</span>
                </div>
            </div>
        </div>
    @endforeach
</div>
