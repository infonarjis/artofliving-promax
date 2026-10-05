@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')

@section('title', $pageName ?? 'Setup / Installation Checklist')

@php
    $pendingCount = 0;
    $manualCount = 0;
    foreach ($groups as $g) {
        foreach ($g['items'] as $it) {
            if (!$it['status'] && $it['type'] === 'manual') {
                $manualCount++;
            } elseif (!$it['status']) {
                $pendingCount++;
            }
        }
    }
@endphp

@section('admin_content')
    <div class="setup-checklist-page">
        <div class="scp-inner">

            <div class="scp-header">
                <div>
                    <h2 class="scp-title">Setup / Installation Checklist</h2>
                    <p class="scp-subtitle">Track what's configured before you hand the site off to a client.</p>
                </div>
                <button type="button" class="scp-btn scp-btn-ghost" id="refreshChecklistBtn">
                    <svg class="scp-btn-icon" viewBox="0 0 24 24">
                        <path d="M17.65 6.35A7.95 7.95 0 0012 4a8 8 0 108 8h-2a6 6 0 11-1.76-4.24L13 11h7V4l-2.35 2.35z" />
                    </svg>
                    Re-Check Status
                </button>
            </div>

            <div class="scp-card scp-summary">
                <div class="scp-summary-top">
                    <span class="scp-summary-label">Overall Progress</span>
                    <span class="scp-summary-value">{{ $completedItems }} / {{ $totalItems }}
                        &nbsp;({{ $percentDone }}%)</span>
                </div>
                <div class="scp-progress-track">
                    <div class="scp-progress-fill" style="width: {{ $percentDone }}%;"></div>
                </div>
                <div class="scp-summary-stats">
                    <span><i class="scp-dot scp-dot-done"></i> {{ $completedItems }} Completed</span>
                    <span><i class="scp-dot scp-dot-pending"></i> {{ $pendingCount }} Pending</span>
                    <span><i class="scp-dot scp-dot-manual"></i> {{ $manualCount }} Needs Verification</span>
                </div>
            </div>

            @foreach ($groups as $groupName => $group)
                @php
                    $groupTotal = count($group['items']);
                    $groupDone = count(array_filter($group['items'], fn($i) => $i['status']));
                @endphp
                <div class="scp-card">
                    <div class="scp-card-header">
                        <span>{{ $groupName }}</span>
                        <span class="scp-card-count">{{ $groupDone }} / {{ $groupTotal }}</span>
                    </div>

                    @foreach ($group['items'] as $item)
                        <div class="scp-row" data-key="{{ $item['key'] }}">
                            <div class="scp-row-left">
                                <span
                                    class="scp-status-icon {{ $item['status'] ? 'is-done' : ($item['type'] === 'manual' ? 'is-manual' : 'is-pending') }}">
                                    @if ($item['status'])
                                        &#10003;
                                    @elseif ($item['type'] === 'manual')
                                        ?
                                    @else
                                        &times;
                                    @endif
                                </span>
                                <div>
                                    <div class="scp-row-title">{{ $item['label'] }}</div>
                                    <div class="scp-row-hint">{{ $item['hint'] }}</div>
                                </div>
                            </div>

                            <div class="scp-row-right">
                                @if ($item['status'])
                                    <span class="scp-status-text is-done">Completed</span>
                                @elseif ($item['type'] === 'manual')
                                    <span class="scp-status-text is-manual">Not Verified</span>
                                @else
                                    <span class="scp-status-text is-pending">Pending</span>
                                @endif

                                <div class="scp-row-actions">
                                    @if (!empty($item['url']))
                                        <a href="{{ $item['url'] }}" target="_blank"
                                            class="scp-btn scp-btn-sm scp-btn-primary">Open</a>
                                    @endif

                                    @if ($item['type'] === 'manual')
                                        <button type="button" class="scp-btn scp-btn-sm scp-btn-ghost toggleManualBtn"
                                            data-key="{{ $item['key'] }}">
                                            {{ $item['status'] ? 'Mark Pending' : 'Mark Done' }}
                                        </button>
                                    @endif

                                    @if ($item['key'] === 'queue')
                                        <button type="button" class="scp-btn scp-btn-sm scp-btn-ghost"
                                            id="testQueueBtn">Test Worker</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach

        </div>
    </div>


@endsection

@push('styles')
    <style>
        .setup-checklist-page {
            --scp-green: #16a34a;
            --scp-green-bg: #ecfdf3;
            --scp-red: #dc2626;
            --scp-amber: #b45309;
            --scp-primary: #4f46e5;
            --scp-primary-dark: #4338ca;
            --scp-text: #1f2937;
            --scp-muted: #8a8f98;
            --scp-border: #e9eaec;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: var(--scp-text);
            background: #f7f7f8;
            padding: 28px 16px;
        }

        .setup-checklist-page * {
            box-sizing: border-box;
        }

        .scp-inner {
            max-width: 900px;
            margin: 0 auto;
        }

        .scp-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
        }

        .scp-title {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: var(--scp-text);
        }

        .scp-subtitle {
            margin: 3px 0 0;
            font-size: 12.5px;
            color: var(--scp-muted);
        }

        /* ---- Buttons ---- */
        .scp-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 7px;
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none;
            line-height: 1;
            white-space: nowrap;
            transition: background .15s ease, border-color .15s ease, box-shadow .15s ease, transform .05s ease;
        }

        .scp-btn:active {
            transform: translateY(1px);
        }

        .scp-btn-sm {
            font-size: 11.5px;
            padding: 6px 12px;
            border-radius: 6px;
        }

        .scp-btn-icon {
            width: 14px;
            height: 14px;
            fill: currentColor;
        }

        .scp-btn-primary {
            background: var(--scp-primary);
            color: #fff;
            box-shadow: 0 1px 2px rgba(79, 70, 229, .25);
        }

        .scp-btn-primary:hover {
            background: var(--scp-primary-dark);
            color: #fff;
        }

        .scp-btn-ghost {
            background: #f3f4f6;
            color: #374151;
            border-color: #e5e7eb;
        }

        .scp-btn-ghost:hover {
            background: #e5e7eb;
        }

        /* ---- Cards ---- */
        .scp-card {
            background: #fff;
            border: 1px solid var(--scp-border);
            border-radius: 10px;
            margin-bottom: 16px;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(16, 24, 40, .03);
        }

        .scp-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 13px 20px;
            font-size: 14px;
            font-weight: 600;
            color: var(--scp-text);
            background: #fbfbfc;
            border-bottom: 1px solid var(--scp-border);
        }

        .scp-card-count {
            font-size: 12px;
            font-weight: 500;
            color: var(--scp-muted);
        }

        .scp-summary {
            padding: 20px 22px;
        }

        .scp-summary-top {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 10px;
        }

        .scp-summary-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--scp-text);
        }

        .scp-summary-value {
            font-size: 12.5px;
            color: var(--scp-muted);
        }

        .scp-progress-track {
            height: 6px;
            border-radius: 999px;
            background: #eef0f2;
            overflow: hidden;
            margin-bottom: 14px;
        }

        .scp-progress-fill {
            height: 100%;
            background: var(--scp-green);
            border-radius: 999px;
            transition: width .5s ease;
        }

        .scp-summary-stats {
            display: flex;
            gap: 18px;
            flex-wrap: wrap;
            font-size: 12.5px;
            color: #4b5563;
        }

        .scp-summary-stats span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .scp-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            display: inline-block;
        }

        .scp-dot-done {
            background: var(--scp-green);
        }

        .scp-dot-pending {
            background: var(--scp-red);
        }

        .scp-dot-manual {
            background: var(--scp-amber);
        }

        /* ---- Rows ---- */
        .scp-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            padding: 12px 20px;
            border-bottom: 1px solid var(--scp-border);
            flex-wrap: wrap;
        }

        .scp-row:last-child {
            border-bottom: none;
        }

        .scp-row:nth-child(even) {
            background: #fbfbfc;
        }

        .scp-row-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 220px;
            flex: 1;
        }

        .scp-status-icon {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            color: #fff;
        }

        .scp-status-icon.is-done {
            background: var(--scp-green);
        }

        .scp-status-icon.is-pending {
            background: var(--scp-red);
        }

        .scp-status-icon.is-manual {
            background: var(--scp-amber);
        }

        .scp-row-title {
            font-size: 13.5px;
            font-weight: 500;
            color: var(--scp-text);
        }

        .scp-row-hint {
            font-size: 11.5px;
            color: var(--scp-muted);
            margin-top: 1px;
        }

        .scp-row-right {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-shrink: 0;
        }

        .scp-status-text {
            font-size: 12px;
            font-weight: 600;
            min-width: 78px;
            text-align: right;
        }

        .scp-status-text.is-done {
            color: var(--scp-green);
        }

        .scp-status-text.is-pending {
            color: var(--scp-red);
        }

        .scp-status-text.is-manual {
            color: var(--scp-amber);
        }

        .scp-row-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        @media (max-width: 560px) {
            .scp-row {
                align-items: flex-start;
            }

            .scp-row-right {
                width: 100%;
                justify-content: space-between;
            }

            .scp-status-text {
                text-align: left;
            }
        }
    </style>
@endpush
@push('scripts')
    <script>
        $(document).ready(function() {
            var csrfToken = $("input[name=_token]").val();

            // Toggle manual items (Payment Gateway / Membership Plan etc.)
            $('.toggleManualBtn').on('click', function() {
                var $btn = $(this);
                var key = $btn.data('key');

                $.ajax({
                    url: '{{ route('admin.setupChecklist.toggleManualStatus') }}',
                    method: 'POST',
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: JSON.stringify({
                        key: key
                    }),
                    success: function(res) {
                        if (res.status === 'success') {
                            window.location.reload();
                        } else {
                            alert(res.msg || 'Something went wrong.');
                        }
                    },
                    error: function() {
                        alert('Something went wrong.');
                    }
                });
            });

            // Test Queue Worker
            $('#testQueueBtn').on('click', function() {
                var $btn = $(this);
                $btn.prop('disabled', true).text('Dispatching...');

                $.ajax({
                    url: '{{ route('admin.setupChecklist.testQueue') }}',
                    method: 'POST',
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(res) {
                        alert(res.msg);
                    },
                    error: function() {
                        alert('Something went wrong.');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('Test Worker');
                    }
                });
            });

            // Re-Check Status
            $('#refreshChecklistBtn').on('click', function() {
                window.location.reload();
            });
        });
    </script>
@endpush
