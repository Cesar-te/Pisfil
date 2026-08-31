@if ($paginator->hasPages())
<nav class="pisfil-pagination" role="navigation" aria-label="{{ __('Pagination Navigation') }}">

    {{-- Mobile: Previous / Next only --}}
    <div class="pisfil-pagination__mobile">
        @if ($paginator->onFirstPage())
            <span class="pisfil-page-btn is-disabled" aria-disabled="true">
                <i class="fas fa-chevron-left"></i> Anterior
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="pisfil-page-btn" rel="prev">
                <i class="fas fa-chevron-left"></i> Anterior
            </a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="pisfil-page-btn" rel="next">
                Siguiente <i class="fas fa-chevron-right"></i>
            </a>
        @else
            <span class="pisfil-page-btn is-disabled" aria-disabled="true">
                Siguiente <i class="fas fa-chevron-right"></i>
            </span>
        @endif
    </div>

    {{-- Desktop: info + numbered pages --}}
    <div class="pisfil-pagination__desktop">

        <p class="pisfil-pagination__info">
            Mostrando
            <span class="pisfil-pagination__bold">{{ $paginator->firstItem() }}</span>
            al
            <span class="pisfil-pagination__bold">{{ $paginator->lastItem() }}</span>
            de
            <span class="pisfil-pagination__bold">{{ $paginator->total() }}</span>
            resultados
        </p>

        <div class="pisfil-pagination__links">

            {{-- Prev --}}
            @if ($paginator->onFirstPage())
                <span class="pisfil-page-btn is-disabled" aria-disabled="true">
                    <i class="fas fa-chevron-left"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="pisfil-page-btn" rel="prev">
                    <i class="fas fa-chevron-left"></i>
                </a>
            @endif

            {{-- Pages --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="pisfil-page-btn is-dots">{{ $element }}</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="pisfil-page-btn is-active" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="pisfil-page-btn">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="pisfil-page-btn" rel="next">
                    <i class="fas fa-chevron-right"></i>
                </a>
            @else
                <span class="pisfil-page-btn is-disabled" aria-disabled="true">
                    <i class="fas fa-chevron-right"></i>
                </span>
            @endif

        </div>
    </div>
</nav>

<style>
.pisfil-pagination { font-family: var(--font-body); }

/* Mobile */
.pisfil-pagination__mobile { display: flex; justify-content: space-between; gap: 10px; }
@media (min-width: 640px) { .pisfil-pagination__mobile { display: none; } }

/* Desktop */
.pisfil-pagination__desktop { display: none; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap; }
@media (min-width: 640px) { .pisfil-pagination__desktop { display: flex; } }

/* Info text */
.pisfil-pagination__info { font-family: var(--font-mono); font-size: 12px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; margin: 0; }
.pisfil-pagination__bold { font-weight: 600; color: var(--text); }

/* Links row */
.pisfil-pagination__links { display: flex; align-items: center; gap: 4px; flex-wrap: wrap; }

/* Base button */
.pisfil-page-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    min-width: 36px; height: 36px; padding: 0 10px;
    border-radius: var(--radius-md, 10px);
    border: 1px solid var(--line);
    background: var(--surface-2);
    color: var(--text);
    font-family: var(--font-mono); font-size: 13px; font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
    white-space: nowrap;
}
a.pisfil-page-btn:hover {
    border-color: var(--primary);
    color: var(--primary);
    background: rgba(63, 167, 218, 0.08);
    transform: translateY(-1px);
}
[data-theme="light"] a.pisfil-page-btn:hover { background: rgba(37, 99, 235, 0.06); }

/* Active */
.pisfil-page-btn.is-active {
    background: var(--primary); border-color: var(--primary); color: #fff;
    font-weight: 700;
    box-shadow: 0 4px 12px rgba(63, 167, 218, 0.35);
    cursor: default;
}

/* Disabled */
.pisfil-page-btn.is-disabled { opacity: 0.35; cursor: not-allowed; pointer-events: none; }

/* Dots */
.pisfil-page-btn.is-dots { border-color: transparent; background: transparent; color: var(--muted); pointer-events: none; letter-spacing: 0.1em; }
</style>
@endif
