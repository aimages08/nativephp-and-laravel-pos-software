@if ($paginator->hasPages())
    <nav style="display:flex;justify-content:center;align-items:center;gap:6px;margin-top:16px;flex-wrap:wrap;font-family:system-ui,sans-serif;">
        @if ($paginator->onFirstPage())
            <span style="padding:6px 12px;border:1px solid #e2e8f0;border-radius:6px;color:#cbd5e1;background:#f8fafc;font-size:14px;">‹ Prev</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" style="padding:6px 12px;border:1px solid #e2e8f0;border-radius:6px;color:#334155;text-decoration:none;background:#fff;font-size:14px;">‹ Prev</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span style="padding:6px 12px;color:#94a3b8;font-size:14px;">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span style="padding:6px 12px;border-radius:6px;background:#3b82f6;color:#fff;font-weight:600;font-size:14px;">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" style="padding:6px 12px;border:1px solid #e2e8f0;border-radius:6px;color:#334155;text-decoration:none;background:#fff;font-size:14px;">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" style="padding:6px 12px;border:1px solid #e2e8f0;border-radius:6px;color:#334155;text-decoration:none;background:#fff;font-size:14px;">Next ›</a>
        @else
            <span style="padding:6px 12px;border:1px solid #e2e8f0;border-radius:6px;color:#cbd5e1;background:#f8fafc;font-size:14px;">Next ›</span>
        @endif
    </nav>

    <div style="text-align:center;color:#94a3b8;font-size:12px;margin-top:6px;">
        Showing {{ $paginator->firstItem() ?? 0 }}–{{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }}
    </div>
@endif