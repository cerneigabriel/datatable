<div
    x-data="{ duplicateDataTableRecordModal: false }"
    x-on:show-duplicate-datatable-record-modal-{{ $entity->{$primaryKey} }}-{{ $action->name }}.window="duplicateDataTableRecordModal = true"
>
    <a
        href="javascript:void();"
        wire:click="duplicateAction({{ $entity->{$primaryKey} }})"
        {!! $action->addClass('whitespace-nowrap')->renderAttributes() !!}
    >
        {!! $action->text !!}
    </a>

    @if($action->requireConfirmation)
        <x-modals.modal
            :size="'sm'"
            :xshow="'duplicateDataTableRecordModal'"
            :title="'Duplicate #' . $entity->{$primaryKey}"
            :actions="[
                [
                    'close_modal_onclick' => true,
                    'text' => 'Yes',
                    'attributes' => [
                        'wire:click' => 'duplicateAction(' . $entity->{$primaryKey} . ', true)'
                    ]
                ]
            ]"
        >
            Are you sure you want to duplicate record <b>{{$entity->publication->name}}</b> - <b>{{$entity->edition->name}}</b> ?
        </x-modals.modal>
    @endif
</div>
