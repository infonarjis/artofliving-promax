@if (isset($resultArr) && count($resultArr) > 0)
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- 3-column grid --}}
        <div class="result-grid">
            @foreach ($resultArr as $key => $dataValue)
                <details class="accordion-card" open>

                    {{-- Header / Summary --}}
                    <summary class="accordion-trigger">
                        <div class="accordion-trigger-left">
                            <span class="accordion-icon-wrap">
                                <i class="bx bx-trending-up"></i>
                            </span>
                            <div class="accordion-meta">
                                <span class="accordion-label">{{ ucwords(str_replace('_', ' ', $key)) }}</span>
                                <span class="accordion-sub">{{ count($dataValue) }}
                                    {{ Str::plural('field', count($dataValue)) }}</span>
                            </div>
                        </div>
                        <span class="accordion-chevron" aria-hidden="true">
                            <i class="bx bx-chevron-down"></i>
                        </span>
                    </summary>

                    {{-- Body --}}
                    <div class="accordion-body-inner">
                        @foreach ($dataValue as $detailKey => $detailValue)
                            <div class="detail-row {{ $loop->last ? 'is-last' : '' }}">
                                <span class="detail-key">
                                    <i class="bx bx-right-arrow-alt detail-arrow" aria-hidden="true"></i>
                                    {{ ucwords(str_replace('_', ' ', $detailKey)) }}
                                </span>
                                <span class="detail-value">
                                    @if (is_array($detailValue))
                                        @foreach ($detailValue as $item)
                                            <span class="detail-tag">{{ $item }}</span>
                                        @endforeach
                                    @else
                                        {{ _displayNotAvailable($detailValue) }}
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>

                </details>
            @endforeach
        </div>

    </div>
@else
    <div class="no-data-wrap text-center p-5">
        <img src="{{ _assetUrl('upload_path.ADMIN_NO_DATA_FOUND') }}" alt="No Data Found" class="no-data-img">
        <p class="no-data-text mt-3 text-muted">No results to display.</p>
    </div>
@endif

{{-- ── Styles ── --}}
<style>
    /* Header */
    .result-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .result-title {
        font-size: 1rem;
        font-weight: 600;
        color: #3d3d3d;
        display: flex;
        align-items: center;
    }

    .result-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #e8f0fe;
        color: #3d72d7;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 20px;
        letter-spacing: 0.02em;
    }

    /* 3-column grid */
    .result-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        align-items: start;
    }

    @media (max-width: 991px) {
        .result-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 575px) {
        .result-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Each card — <details> element */
    .accordion-card {
        background: #fff;
        border: 1px solid #e4e6ef;
        border-radius: 12px;
        overflow: hidden;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .accordion-card[open] {
        border-color: #b8cef7;
        box-shadow: 0 2px 12px 0 rgba(61, 114, 215, 0.08);
    }

    .accordion-card:hover {
        border-color: #c4d4f5;
    }

    /* <summary> — remove browser default marker */
    .accordion-trigger {
        list-style: none;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 18px;
        cursor: pointer;
        gap: 12px;
        transition: background 0.15s;
        user-select: none;
    }

    .accordion-trigger::-webkit-details-marker {
        display: none;
    }

    .accordion-trigger::marker {
        display: none;
    }

    .accordion-trigger:hover {
        background: #f7f9ff;
    }

    .accordion-trigger-left {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
        min-width: 0;
    }

    /* Icon wrap */
    .accordion-icon-wrap {
        flex-shrink: 0;
        width: 36px;
        height: 36px;
        background: #edf2ff;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #3d72d7;
        font-size: 1.1rem;
        transition: background 0.2s, color 0.2s;
    }

    .accordion-card[open] .accordion-icon-wrap {
        background: #3d72d7;
        color: #fff;
    }

    /* Label + sub */
    .accordion-meta {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .accordion-label {
        font-size: 0.88rem;
        font-weight: 600;
        color: #2d2d2d;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .accordion-sub {
        font-size: 0.75rem;
        color: #9399a6;
        margin-top: 1px;
    }

    /* Chevron — CSS-only rotate via [open] selector */
    .accordion-chevron {
        flex-shrink: 0;
        color: #9399a6;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), color 0.2s;
    }

    .accordion-card[open] .accordion-chevron {
        transform: rotate(180deg);
        color: #3d72d7;
    }

    /* Body */
    .accordion-body-inner {
        padding: 4px 18px 16px;
        display: flex;
        flex-direction: column;
        border-top: 1px solid #eef0f5;
    }

    /* Detail rows */
    .detail-row {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 8px 0;
        border-bottom: 1px dashed #eef0f5;
        flex-wrap: wrap;
    }

    .detail-row.is-last {
        border-bottom: none;
        padding-bottom: 0;
    }

    .detail-key {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 0.78rem;
        font-weight: 600;
        color: #5a5f72;
        white-space: nowrap;
        min-width: 110px;
        flex-shrink: 0;
    }

    .detail-arrow {
        color: #b0b8cc;
        font-size: 0.95rem;
    }

    .detail-value {
        font-size: 0.8rem;
        color: #2d2d2d;
        word-break: break-word;
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        align-items: center;
        flex: 1;
    }

    /* Array tags */
    .detail-tag {
        display: inline-block;
        background: #f0f4fd;
        color: #3d72d7;
        border: 1px solid #d4e0fa;
        font-size: 0.72rem;
        font-weight: 500;
        padding: 2px 8px;
        border-radius: 20px;
    }

    /* No data */
    .no-data-img {
        max-width: 220px;
        opacity: 0.85;
    }

    .no-data-text {
        font-size: 0.9rem;
    }
</style>
