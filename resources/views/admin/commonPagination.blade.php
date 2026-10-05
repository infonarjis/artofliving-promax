@if ($resultArr->hasPages())
<ul class="pagination customPagination" role="navigation">
    {{-- Previous Page Link --}}
    @if ($resultArr->onFirstPage())
    <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
        <span class="page-link" aria-hidden="true"><i class="tf-icon bx bx-chevrons-left"></i></span>
    </li>
    @else
    <li class="page-item ajaxPagination">
        <a class="page-link" href="{{ $resultArr->previousPageUrl() }}" data-page="{{$resultArr->currentPage()-1}}"
            rel="prev" aria-label="@lang('pagination.previous')"><i class="tf-icon bx bx-chevrons-left"></i></a>
    </li>
    @endif

    <?php
    $start = $resultArr->currentPage() - 3; // show 3 pagination links before current
    $end = $resultArr->currentPage() + 3; // show 3 pagination links after current
    if ($start < 1) {
        $start = 1; // reset start to 1
        $end += 1;
    }
    // if ($end >= $resultArr->lastPage() ) $end = $resultArr->lastPage(); // reset end to last page
    if ($end >= $resultArr->lastPage()) {
        $end = $resultArr->lastPage();
    }
    ?>

    @if ($start > 1)
    <li class="page-item ajaxPagination">
        <a class="page-link" href="{{ $resultArr->url(1) }}" data-page="{{1}}">{{1}}</a>
    </li>
    @if ($resultArr->currentPage() != 5)
    {{-- "Three Dots" Separator --}}
    <li class="page-item disabled" aria-disabled="true"><span class="page-link">...</span></li>
    @endif
    @endif
    @for ($i = $start; $i <= $end; $i++) <li
        class="page-item {{ ($resultArr->currentPage() == $i) ? ' active' : '' }} ajaxPagination">
        <a class="page-link" href="{{ $resultArr->url($i) }}" data-page="{{$i}}">{{$i}}</a>
        </li>
    @endfor
    @if ($end < $resultArr->lastPage())
        @if ($resultArr->currentPage() + 3 != $resultArr->lastPage())
        {{-- "Three Dots" Separator --}}
        <li class="page-item disabled" aria-disabled="true"><span class="page-link">...</span></li>
        @endif
        <li class="page-item ajaxPagination">
            <a class="page-link" href="{{ $resultArr->url($resultArr->lastPage()) }}"
                data-page="{{$resultArr->lastPage()}}">{{$resultArr->lastPage()}}</a>
        </li>
        @endif

        {{-- Next Page Link --}}
        @if ($resultArr->hasMorePages())
        <li class="page-item ajaxPagination">
            <a class="page-link" href="{{ $resultArr->nextPageUrl() }}" data-page="{{$resultArr->currentPage()+1}}"
                rel="next" aria-label="@lang('pagination.next')"><i class="tf-icon bx bx-chevrons-right"></i></a>
        </li>
        @else
        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
            <span class="page-link" aria-hidden="true"><i class="tf-icon bx bx-chevrons-right"></i></span>
        </li>
    @endif
</ul>
@endif
