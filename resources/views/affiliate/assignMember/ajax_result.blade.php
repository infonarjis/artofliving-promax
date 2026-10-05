<div class="afd-table-wrap">
    <table class="afd-table">
        <thead>
            <tr>
                <th class="afd-th-active">Matri id <span class="afd-sort-icon"><iconify-icon
                            icon="hugeicons:arrow-down-01"></iconify-icon></span></th>
                <th>Name <span class="afd-sort-icon"><iconify-icon icon="hugeicons:arrows-up-down"></iconify-icon></span>
                </th>
                <th>Registered <span class="afd-sort-icon"><iconify-icon
                            icon="hugeicons:arrows-up-down"></iconify-icon></span></th>
                <th>Plan</th>
                <th>Status <span class="afd-sort-icon"><iconify-icon
                            icon="hugeicons:arrows-up-down"></iconify-icon></span></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($resultData as $item)
                <tr>
                    <td class="afd-td-name">{{ $item->matri_id }}</td>
                    <td class="afd-td-name">{{ $item->fullname }}</td>
                    <td>
                        <div class="afd-reg-icons">
                            @if ($item->status == 'APPROVED')
                                <span class="afd-badge-verified d-flex justify-content-center afd-ri-check">
                                    <iconify-icon icon="hugeicons:tick-02" width="14"
                                        height="14"></iconify-icon>{{ $item->status }}
                                </span>
                            @else
                                <span class="afd-badge-pending d-flex justify-content-center afd-ri-check">
                                    <iconify-icon icon="hugeicons:tick-02" width="14"
                                        height="14"></iconify-icon>{{ $item->status }}
                                </span>
                            @endif

                            @if ($item->plan_status == 'Paid')
                                <span class="afd-badge-premium">Premium</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="afd-plan-pill">
                            @if ($item->plan_status == 'Paid')
                                <span class="afd-plan-dot afd-plan-dot-diamond"></span> {{ $item->plan_name ?? '-' }}
                            @else
                                -
                            @endif
                        </div>
                    </td>
                    <td>
                        {{ _displayDate($item->created_at, 'j F, Y') }}
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
