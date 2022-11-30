<div class="lg:col-span-4">
    @if(isset($filter->label))
        <label for="datatable.filters.{{ $filter->name }}" class="block mb-2">{{ $filter->label }}</label>
    @endif
    <x-input
        id="datatable.filters.{{ $filter->name }}"
        wire:model.debounce.500ms="filters.{{ $filter->name }}"
        type="text"
        placeholder="{{ $filter->placeholder }}"
    />
</div>
