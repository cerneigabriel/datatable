<div
    x-data="{ deleteDataTableRecordModal: false }"
    x-on:show-delete-datatable-record-modal-{{ $entity->{$primaryKey} }}-{{ $action->name }}.window="deleteDataTableRecordModal = true"
>
    <a
        href="javascript:void();"
        wire:click="deleteAction({{ $entity->{$primaryKey} }})"
        {!! $action->addClass('whitespace-nowrap')->renderAttributes() !!}
    >
        {!! $action->text !!}
    </a>

    @if($action->requireConfirmation)
        <x-modals.modal
            :size="'sm'"
            :xshow="'deleteDataTableRecordModal'"
            :title="'Delete #' . $entity->{$primaryKey}"
            :actions="[
                [
                    'close_modal_onclick' => true,
                    'text' => 'Yes',
                    'attributes' => [
                        'wire:click' => 'deleteAction(' . $entity->{$primaryKey} . ', true)'
                    ]
                ]
            ]"
        >
            Are you sure you want to delete record #{{ $entity->{$primaryKey} }}?
        </x-modals.modal>
    @endif
</div>
