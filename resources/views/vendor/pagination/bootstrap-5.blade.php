@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="d-flex justify-content-center">
        <ul class="pagination" style="display: inline-flex; align-items: center; gap: 0.25rem; direction: rtl; margin: 0; padding: 0; list-style: none;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link" style="display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem 0.75rem; border-radius: 8px; border: none; color: #94a3b8; background: #f8fafc; cursor: not-allowed; opacity: 0.8; font-weight: 600; line-height: 1.25rem;">
                        <i class="fas fa-chevron-left" style="font-size: 1rem; width: 1rem; height: 1rem;"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" style="display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem 0.75rem; border-radius: 8px; border: none; color: #1e40af; font-weight: 600; line-height: 1.25rem; transition: all 0.3s ease; cursor: pointer;">
                        <i class="fas fa-chevron-left" style="font-size: 1rem; width: 1rem; height: 1rem;"></i>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link" style="display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem 0.75rem; border-radius: 8px; border: none; color: #1e40af; font-weight: 600; line-height: 1.25rem;">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link" style="display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem 0.75rem; border-radius: 8px; border: none; background: linear-gradient(135deg, #1e40af 0%, #6366f1 100%); color: white; font-weight: 600; line-height: 1.25rem;">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}" style="display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem 0.75rem; border-radius: 8px; border: none; color: #1e40af; font-weight: 600; line-height: 1.25rem; transition: all 0.3s ease; cursor: pointer;">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" style="display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem 0.75rem; border-radius: 8px; border: none; color: #1e40af; font-weight: 600; line-height: 1.25rem; transition: all 0.3s ease; cursor: pointer;">
                        <i class="fas fa-chevron-right" style="font-size: 1rem; width: 1rem; height: 1rem;"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link" style="display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem 0.75rem; border-radius: 8px; border: none; color: #94a3b8; background: #f8fafc; cursor: not-allowed; opacity: 0.8; font-weight: 600; line-height: 1.25rem;">
                        <i class="fas fa-chevron-right" style="font-size: 1rem; width: 1rem; height: 1rem;"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
