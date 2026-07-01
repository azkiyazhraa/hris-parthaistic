@if ($paginator->hasPages())
    <div class="flex justify-end gap-1 flex-wrap">

        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1 rounded text-sm border bg-white text-gray-300 border-gray-200 cursor-not-allowed">&#8249;</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               class="px-3 py-1 rounded text-sm border bg-white text-gray-700 border-gray-300 hover:bg-gray-50">&#8249;</a>
        @endif

        {{-- Page numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-3 py-1 rounded text-sm border bg-white text-gray-400 border-gray-200">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-3 py-1 rounded text-sm border bg-blue-600 text-white border-blue-600">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}"
                           class="px-3 py-1 rounded text-sm border bg-white text-gray-700 border-gray-300 hover:bg-gray-50">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               class="px-3 py-1 rounded text-sm border bg-white text-gray-700 border-gray-300 hover:bg-gray-50">&#8250;</a>
        @else
            <span class="px-3 py-1 rounded text-sm border bg-white text-gray-300 border-gray-200 cursor-not-allowed">&#8250;</span>
        @endif

    </div>
@endif
