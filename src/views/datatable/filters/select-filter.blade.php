<div class="lg:col-span-4">
    @if(isset($filter->label))
        <label for="datatable.filters.{{ $filter->name }}" class="block mb-2">{{ $filter->label }}</label>
    @endif
    <x-input
        type="select"
        wire:model.debounce.500ms="filters.{{ $filter->name }}"
        :options="$filter->options()"
    ></x-input>
</div>