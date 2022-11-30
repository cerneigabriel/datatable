<div
    wire:id="{{ "{$datatable->id}_datatable_top_right_filters" }}"
    x-on:datatable-updated="console.log('datatable:synced[datatable::datatable-top-right-filters]');"
>
    @if($datatable && $datatable->isFilterable() && $datatable->filters()->whereGroup($filtersGroup)->isNotEmpty())
        <div class="flex items-center gap-x-2 text-sm">
            {!! $datatable->filters()->whereGroup(\Modules\DataTable\Core\Abstracts\DataTableFilter::GroupDataTableTopRight)->render() !!}

            @if($datatable->showRecordsCountInDataTableHeader)
                <span class="min-w-10 text-right">
                     {{ "({$recordsCount})" }}
                </span>
            @endif
        </div>
    @endif
</div>
