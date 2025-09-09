{{-- resources/views/pagination/red.blade.php --}}
@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination Navigation" class="inline-flex">
    <ul class="inline-flex items-center gap-1 rounded-xl bg-gray-100 text-red-700 p-1">
        {{-- Prev --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-2 rounded-lg opacity-40 select-none">‹</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')"
               class="px-3 py-2 rounded-lg hover:text-white hover:bg-red-600">‹</a>
        @endif

        {{-- Page elements --}}
        @foreach ($elements as $element)
            {{-- Ellipsis --}}
            @if (is_string($element))
                <span class="px-3 py-2 rounded-lg bg-gray-200 text-red-700 select-none">…</span>
            @endif

            {{-- Numbered links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page"
                              class="px-3 py-2 rounded-lg text-white bg-red-600">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}"
                           class="px-3 py-2 rounded-lg hover:text-white hover:bg-red-600">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')"
               class="px-3 py-2 rounded-lg hover:text-white hover:bg-red-600">›</a>
        @else
            <span class="px-3 py-2 rounded-lg opacity-40 select-none">›</span>
        @endif
    </ul>
</nav>
@endif
