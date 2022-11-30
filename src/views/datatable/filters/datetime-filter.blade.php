<div class="lg:col-span-4">
    @if(isset($filter->label))
        <label for="datatable.filters.{{ $filter->name }}" class="block mb-2">{{ $filter->label }}</label>
    @endif
    <x-input
        id="datepicker-{{ $filter->name }}"
        placeholder="{{ $filter->placeholder }}"
        :type="'datepicker'"
        :datepicker-config="[
            'mode' => $filter->range ? 'range' : 'single',
            'dateFormat' => $filter->format,
            'enableTime' => in_array($filter->type, ['time', 'datetime']),
            'noCalendar' => in_array($filter->type, ['time'])
        ]"
        wire:model.debounce.500ms="filters.{{ $filter->name }}"
    />
</div>
