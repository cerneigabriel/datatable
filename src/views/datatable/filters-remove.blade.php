<div
    x-on:datatable-updated="console.log('datatable:synced[datatable::filters.remove]');"
>
    @if($datatable && $datatable->showResetFiltersButton())
        <button class="link-info uppercase" wire:click="removeFilters">x Remove Filters</button>
    @endif
</div>
