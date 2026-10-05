<div class="afd-toolbar">
    <div class="afd-select-wrap">
        <select class="afd-custom-select" id="filter">
            <option value="">All Time</option>
            <option value="today">Today</option>
            <option value="yesterday">Yesterday</option>
            <option value="last7">Last 7 Days</option>
            <option value="month">This Month</option>
        </select>
    </div>
    <div class="afd-search-wrap">
        <span class="afd-search-icon"><iconify-icon icon="hugeicons:search-02" width="18" height="18"
                style="vertical-align:-3px"></iconify-icon></span>
        <input type="text" id="search" placeholder="Search...">
    </div>
</div>

<div class="afd-table-wrap">
    <table class="afd-table">
        <thead>
            <tr>
                <th class="afd-th-active">Date <span class="afd-sort-icon"><iconify-icon
                            icon="hugeicons:arrow-down-01"></iconify-icon></span></th>
                <th>Referral URL <span class="afd-sort-icon"><iconify-icon
                            icon="hugeicons:arrows-up-down"></iconify-icon></span></th>
                <th>IP Address <span class="afd-sort-icon"><iconify-icon
                            icon="hugeicons:arrows-up-down"></iconify-icon></span></th>
                <th>Browser <span class="afd-sort-icon"><iconify-icon
                            icon="hugeicons:arrows-up-down"></iconify-icon></span></th>
                <th>Device <span class="afd-sort-icon"><iconify-icon
                            icon="hugeicons:arrows-up-down"></iconify-icon></span></th>
                <th>Clicks <span class="afd-sort-icon"><iconify-icon
                            icon="hugeicons:arrows-up-down"></iconify-icon></span></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($resultData as $item)
                <tr>
                    <td class="afd-td-date">{{ _displayDate($item->click_date, 'j F, Y') }}</td>
                    <td><span style="color:var(--afd-blue)">
                            {{ $item->referral_url }}
                        </span>
                        @php
                            $isNew =
                                \Carbon\Carbon::parse($item->first_click_time)->format('Y-m-d') == $item->click_date;

                        @endphp
                        @if ($isNew)
                            <span class="badge"
                                style="background:rgba(245, 166, 35, 0.15); color:#f5a623; font-size:9px; vertical-align:middle;">New</span>
                        @endif
                    </td>
                    <td>{{ $item->ip_address }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2"><iconify-icon icon="logos:chrome"
                                width="16"></iconify-icon> {{ ucfirst($item->browser) }}</div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2"><iconify-icon icon="hugeicons:computer"
                                width="16"></iconify-icon> {{ ucfirst($item->device) }}
                        </div>
                    </td>
                    <td>
                        {{ $item->total_clicks }}

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- pagination  -->
@if ($resultData->hasPages())
    {{ $resultData->links(_getConstant('dir_path.AFFILIATE_DIR_PATH') . '.layouts.pagination') }}
@endif
<!-- pagination  -->
