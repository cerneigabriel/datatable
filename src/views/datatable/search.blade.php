<div
    wire:id="{{ "$datatable->id-datatable_search" }}"
    x-on:datatable-updated="console.log('datatable:synced[datatable::search]');"
>
    @if($datatable && $datatable->isSearchable())
        <div class="w-full border-2 rounded-lg border-blue-light">
            <div class="flex items-center">
                <p class="text-sm font-semibold poppins text-black-soft my-2.5 px-6 uppercase">
                    {{ $title }}
                </p>

                @if($description)
                    <x-popover :arrange="'right'">{{ $description }}</x-popover>
                @endif
            </div>
            <div class="p-6 rounded-b-lg bg-blue-light">
                <div class="flex gap-8 flex-wrap flex-col lg:flex-row">
                    <div class="flex-1">
                        <x-input wire:model.debounce.500ms="search" :placeholder="$placeholder"/>
                    </div>
                    @if($datatable->hasVisibleConstraints())
                        <div class="lg:w-1/3">
                            <x-input
                                type="select"
                                wire:model.debounce.500ms="constraint"
                                :options="$constraints"
                            ></x-input>
                        </div>
                    @endif
                    @if($showResetButton)
                        <div class="flex-col">
                            <button wire:click="resetSearch" type="button" class="button-secondary">Reset</button>
                        </div>
                    @endif
                    @if($datatable->isFilterable())
                        {!! $datatable->filters()->whereGroup(\Modules\DataTable\Core\Abstracts\DataTableFilter::GroupSearch)->render() !!}
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
