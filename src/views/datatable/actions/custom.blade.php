<div
    x-data="{ customDataTableRecordModal: false }"
    x-on:show-custom-datatable-record-modal-{{ $entity->{$primaryKey} }}-{{ $action->name }}.window="customDataTableRecordModal = true"
>
    <a
        href="javascript:void(0);"
        wire:click="customAction({{ $entity->{$primaryKey} }}, '{{ $action->name }}')"
        {!! $action->addClass('whitespace-nowrap')->renderAttributes() !!}
    >
        {!! $action->text !!}
    </a>

    @if($action->getRequireConfirmation($entity))
        <x-modals.modal
            :size="'sm'"
            :xshow="'customDataTableRecordModal'"
            :title="$action->getModalTitle($entity)"
            :default-actions="[]"
            :actions="$action->getModalActions($entity)"
        >
            {!! $action->getModalText($entity) !!}
        </x-modals.modal>
    @endif
</div>
