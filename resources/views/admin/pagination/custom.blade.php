@if ($paginator->hasPages())
    @php
    $params = request()->input();
    unset($params['page']);
    $pr = $params ? '&'.http_build_query($params) : '';
    @endphp

    <ul class="pager">

        @if (!$paginator->onFirstPage())
            {{--<li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">← Previous</a></li>--}}
        {{--@else
            <li class="disabled"><span>← Previous</span></li>--}}
        @endif

        @foreach ($elements as $element)

            @if (is_string($element))
                <li class="disabled"><span>{{ $element }}</span></li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="active"><span>{{ $page }}</span></li>
                    @else
                        <li><a href="{{ $url . $pr }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            {{--<li><a href="{{ $paginator->nextPageUrl() }}" rel="next">Next →</a></li>--}}
        {{--@else
            <li class="disabled"><span>Next →</span></li>--}}
        @endif
    </ul>
@endif
