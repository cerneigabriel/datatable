<div {{ $attributes }}>
    @if (isset($title) || isset($header) || isset($leftHeader) || isset($rightHeader))
        <div class="flex items-center justify-between gap-4 pb-7 flex-wrap">
            <div class="flex items-center gap-4 sm:w-auto w-full flex-wrap">
                <h2 class="page_title {{ \Illuminate\Support\Str::wordCount($title) > 3 ? 'text-xl' : '' }}">{{ $title ?? '' }}</h2>

                @if($showRemoveFiltersButton)
                    <livewire:datatable::filters.remove :datatable="$datatable"/>
                @endif

                @if(isset($leftHeader))
                    <div {{ $leftHeader->attributes->class(['flex', 'items-center', 'gap-4']) }}>
                        {{ $leftHeader }}
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-x-3">
                @if(isset($header) || isset($rightHeader))
                    <div {{ ($header ?? $rightHeader)->attributes->class(['flex', 'items-center', 'gap-4']) }}>
                        {{ ($header ?? $rightHeader) }}
                    </div>
                @endif

                @if($datatable->isFilterable() && $datatable->filters()->whereGroup(\Modules\DataTable\Core\Abstracts\DataTableFilter::GroupDataTableTopRight)->isNotEmpty())
                    {{ isset($header) || isset($rightHeader) ? '/' : '' }}
                    <livewire:datatable::datatable-top-right-filters :datatable="$datatable" />
                @endif
            </div>
        </div>
    @endif

    <livewire:datatable::datatable :datatable="$datatable" :mobile-breakpoint="$mobile_breakpoint" />

    @if (isset($footer))
        <div {{ $footer->attributes->class(['flex', 'items-center', 'justify-between', 'gap-4', 'pb-7']) }}>
            {{ $footer }}
        </div>
    @endif

    <livewire:datatable::notifications />
</div>
