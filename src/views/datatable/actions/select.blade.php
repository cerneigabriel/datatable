<div
    class="w-full min-w-48" {!! $action->renderAttributes() !!}
    x-data="{ customDataTableRecordModal: false }"
    x-on:show-select-datatable-record-modal-{{ $entity->{$primaryKey} }}-{{ $action->name }}.window="customDataTableRecordModal = true"
>
    @if(isset($action->label))
        <label for="datatable.action.{{ $action->name }}" class="block mb-2">{{ $action->label }}</label>
    @endif


    @if(isset($action->actionHandler))
        <x-input
            type="select-choices"
            wire:change="selectAction('{{ $entity->{$primaryKey} }}', '{{ $action->name }}', false, $event.detail.value, $event.target.getOldValue());"
            wire:loading.attr="disabled"
            :config="$action->config($entity)"
        />
    @else
        <x-input type="select-choices" :config="$action->config($entity)" />
    @endif

    <x-modals.modal
        :size="'md'"
        :xshow="'customDataTableRecordModal'"
        :title="$action->getModalTitle($entity)"
        :actions="[
            [
                'close_modal_onclick' => true,
                'text' => 'Yes',
                'attributes' => [
                    'wire:click' => 'selectAction(' . $entity->{$primaryKey} . ', \'' . $action->name . '\', true, ' . $action->getValue($entity->{$primaryKey}, 'currentOption', 'null') . ', ' . $action->getValue($entity->{$primaryKey}, 'previousOption', 'null') . ')'
                ]
            ]
        ]"
    >
        {!! $action->getModalText($entity) !!}
    </x-modals.modal>
</div>
