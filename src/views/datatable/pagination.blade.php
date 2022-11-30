<div>
    @if ($paginator->hasPages())
        @php(isset($this->numberOfPaginatorsRendered[$paginator->getPageName()]) ? $this->numberOfPaginatorsRendered[$paginator->getPageName()]++ : $this->numberOfPaginatorsRendered[$paginator->getPageName()] = 1)

        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center py-2.5 text-[15px] text-black-light w-full">
            <div class=" flex-1 flex items-center justify-center sm:justify-end">
                <div>
                    <span class="relative z-0 inline-flex items-center w-full sm:w-auto">
                        <div class="px-4">
                            {{ "{$paginator->firstItem()}-{$paginator->lastItem()} of {$paginator->total()}" }}
                        </div>

                        <span>
                            {{-- Previous Page Link --}}
                            @if ($paginator->onFirstPage())
                                <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                                    <span class="link-secondary-lg block py-2.5 px-6 opacity-70" aria-hidden="true">
                                        <img src="{{ asset('img/datatable/pagination/left_arrow_icon.svg') }}">
                                    </span>
                                </span>
                            @else
                                <button wire:click="previousPage('{{ $paginator->getPageName() }}')" dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.after" rel="prev" class="link-secondary-lg py-2.5 px-6" aria-label="{{ __('pagination.previous') }}">
                                    <img src="{{ asset('img/datatable/pagination/left_arrow_icon.svg') }}">
                                </button>
                            @endif
                        </span>

                        <span>
                            {{-- Next Page Link --}}
                            @if ($paginator->hasMorePages())
                                <button wire:click="nextPage('{{ $paginator->getPageName() }}')" dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.after" rel="next" class="link-secondary-lg py-2.5 px-6" aria-label="{{ __('pagination.next') }}">
                                    <img src="{{ asset('img/datatable/pagination/right_arrow_icon.svg') }}">
                                </button>
                            @else
                                <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                                    <span class="link-secondary-lg block py-2.5 px-6 opacity-70" aria-hidden="true">
                                        <img src="{{ asset('img/datatable/pagination/right_arrow_icon.svg') }}">
                                    </span>
                                </span>
                            @endif
                        </span>
                    </span>
                </div>
            </div>
        </nav>
    @endif
</div>