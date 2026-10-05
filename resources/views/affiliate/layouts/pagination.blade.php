@if ($paginator->hasPages())
    <div class="afd-pagination-bar">
        <div class="afd-pager">

            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <button class="afd-page-btn" disabled>
                    <iconify-icon icon="hugeicons:arrow-left-01"></iconify-icon> Prev
                </button>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="afd-page-btn pagination-link">
                    <iconify-icon icon="hugeicons:arrow-left-01"></iconify-icon> Prev
                </a>
            @endif


            {{-- Page Numbers --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="afd-page-sep">…</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <button class="afd-page-btn afd-page-active">{{ $page }}</button>
                        @else
                            <a href="{{ $url }}" class="afd-page-btn pagination-link">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach


            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="afd-page-btn pagination-link">
                    Next <iconify-icon icon="hugeicons:arrow-right-01"></iconify-icon>
                </a>
            @else
                <button class="afd-page-btn" disabled>
                    Next <iconify-icon icon="hugeicons:arrow-right-01"></iconify-icon>
                </button>
            @endif

        </div>
    </div>
@endif