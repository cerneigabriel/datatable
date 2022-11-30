<div class="lg:col-span-4 flex items-center gap-1.5">
    @if(isset($filter->placeholder))
        <label for="datatable.filters.{{ $filter->name }}" class="block">{{ $filter->placeholder }}</label>
    @endif
    <x-input
        id="datatable.filters.{{ $filter->name }}"
        type="switch"
        wire:model.debounce.500ms="filters.{{ $filter->name }}"
        :value="$filter->value()"
    />
</div>