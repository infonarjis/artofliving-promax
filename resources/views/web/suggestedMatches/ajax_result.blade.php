<!-- Profiles Grid -->
<div class="row g-3">
    @forelse($resultArr as $result)
        @include(_getConstant('dir_path.WEB_DIR_PATH').'.memberCardLayouts.index', ['result' => $result])
    @empty
        @include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.noDataFound', [
            'message' => __('messages.lbl_no_matches_found'),
        ])
    @endforelse
</div>

<!-- pagination  -->
@if ($resultArr->hasPages())
    {{ $resultArr->links(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.pagination') }}
@endif