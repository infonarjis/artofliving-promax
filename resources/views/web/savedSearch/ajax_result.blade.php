@forelse($data as $key => $search)
    @php $collapseId = 'search-details-'.$search->id; @endphp
    <div class="common-bglight-main p-3 mt-2">
        <div class="d-md-flex">
            <div class="left-plan-history w-100">
                <div class="plan-namesd d-flex align-items-center gap-2">
                    <div class="history-plan-icon">
                        <iconify-icon icon="gg:search"></iconify-icon>
                    </div>
                    <div class="save-search-details ms-1">
                        <h4 class="fts-16 fw-6 white-color-n">{{ $search->search_name ?? 'Saved Search' }}</h4>
                        <p class="fts-14 fw-4 white-color70-n mt-1">
                            {{ _displayDate($search->created_at, 'D jS F - Y') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="right-plans-btngroup d-flex gap-2 mt-2 mt-md-0">
                <button class="btn-plan-print arrow-collapse fts-14 fw-5 collapsed" data-bs-toggle="collapse"
                    data-bs-target="#search-details-{{ $collapseId }}" aria-expanded="false"
                    aria-controls="search-details-{{ $collapseId }}"><iconify-icon
                        icon="iconamoon:arrow-down-2-duotone" class="fts-20"></iconify-icon>Details</button>
                <a href="{{ route('web.savedSearch.apply', $search->id) }}"
                    class="btn-plan-download fts-22"
                    title="Apply Search">
                    <iconify-icon icon="gg:search"></iconify-icon>
                </a>
                <button class="btn-plan-download fts-22 deleteSavedSearch" data-id="{{ $search->id }}" title="Delete">
                    <iconify-icon icon="fluent:delete-24-regular"></iconify-icon>
                </button>
            </div>
        </div>
        <div class="collapse" id="search-details-{{ $collapseId }}">
            <ul class="matches-details-lists d-flex flex-wrap justify-content-center gap-1 mt-3">
                @foreach ($search->display_values as $key => $displayValue)
                    <li class="fts-13 white-color70-n fw-5 {{ floor($key / 2) % 2 == 0 ? 'odd-matches' : '' }}">
                        <span class="fw-4">{{ $displayValue['label'] }}</span>
                        {{ _displayNotAvailable($displayValue['value']) }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@empty
    @include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.noDataFound', [
        'message' => __('messages.lbl_no_data_found'),
    ])
@endforelse

<!-- pagination  -->
@if ($data->hasPages())
    {{ $data->links(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.pagination') }}
@endif
