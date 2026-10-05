@if ($paginator->hasPages())
    <nav class="pagination_nav mt-0 mt-lg-2" aria-label="pagination">
        <ul class="d-flex align-items-center gap-2">

            {{-- Previous Page --}}
            @if (!$paginator->onFirstPage())
                <li class="prev-next">
                    <a href="{{ $paginator->previousPageUrl() }}" class="box-shadow-r-l ajax-pagination">
                        <iconify-icon icon="solar:arrow-left-linear"></iconify-icon>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "..." Separator --}}
                @if (is_string($element))
                    <li class="page_items">
                        <span class="pb-2">{{ $element }}</span>
                    </li>
                @endif

                {{-- Page Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li class="page_items">
                            <a href="{{ $url }}"
                                class="ajax-pagination {{ $page == $paginator->currentPage() ? 'active' : '' }}">
                                {{ $page }}
                            </a>
                        </li>
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page --}}
            @if ($paginator->hasMorePages())
                <li class="prev-next">
                    <a href="{{ $paginator->nextPageUrl() }}" class="box-shadow-r-l ajax-pagination">
                        <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                    </a>
                </li>
            @endif

        </ul>
    </nav>
@endif
