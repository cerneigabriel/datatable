<?php

namespace Modules\DataTable\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\DataTable\Livewire\Traits\HasFilters;
use Modules\DataTable\Livewire\Traits\HasDatatable;
use Modules\DataTable\Livewire\Traits\SyncWithDatatable;
use Modules\DataTable\Core\Abstracts\DataTable as DataTableAbstract;

/**
 * Class Datatable
 *
 * @package Modules\DataTable\Livewire
 */
class Datatable extends Component
{
    use WithPagination;
    use SyncWithDatatable;
    use HasDatatable;

    /** @var int|null */
    public ?int $pageLength = null;

    /** @var string */
    public string $sortBy = '';

    /** @var string */
    public string $sortDir = '';

    /** @var string */
    public string $mobile_breakpoint = 'lg';

    /** @var array */
    public array $availableBreakpoints = ['xs', 'sm', 'md', 'lg', 'xl'];

    /**
     * @return string
     */
    public function paginationView()
    {
        return 'datatable::pagination';
    }

    /**
     * @return string[]
     */
    public function getListeners(): array
    {
        return array_merge(
            [
                'setFilters',
                'removeFilters',
                'refresh' => '$refresh'
            ],
            $this->datatableListeners(),
        );
    }

    /**
     * @param \Modules\DataTable\Core\Abstracts\DataTable $datatable
     * @param string|null $mobileBreakpoint
     * @return void
     */
    public function mount(DataTableAbstract $datatable, ?string $mobileBreakpoint = 'lg')
    {
        $this->mobile_breakpoint = in_array($mobileBreakpoint, $this->availableBreakpoints) ? $mobileBreakpoint : $this->mobile_breakpoint;
        $this->mountDatatable();
        $this->setQueryString(array_merge(
            $this->datatable->isSortable()
                ? [
                    'sortBy' => ['as' => "{$this->datatable->name}_sortBy"],
                    'sortDir' => ['as' => "{$this->datatable->name}_sortDir"],
                ]
                : [],
            $this->datatable->paginate
                ? ['pageLength' => ['as' => "{$this->datatable->name}_perPage", 'except' => '10']]
                : []
        ));
    }

    /**
     * @return void
     */
    public function booted()
    {
//        $this->syncWithDataTable('queryString');

        $this->setQueryString(array_merge(
            $this->datatable->isSortable()
                ? [
                'sortBy' => ['as' => "{$this->datatable->name}_sortBy"],
                'sortDir' => ['as' => "{$this->datatable->name}_sortDir"],
            ]
                : [],
            $this->datatable->paginate
                ? ['pageLength' => ['as' => "{$this->datatable->name}_perPage", 'except' => '10']]
                : []
        ));
        $this->syncWithDataTable();
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render()
    {
        $this->syncWithDataTable();
        $this->syncWithDataTable('cacheDataTable');
        $this->emitTo('datatable::filters.remove', 'refresh');
        $this->emitTo(
            'datatable::datatable-top-right-filters',
            'refreshRecordsCount',
            $this->datatable->getRecordsCount()
        );
        return view(
            'datatable::datatable',
            $this->datatable->viewData()
        );
    }

    /**
     * @param string $name
     * @param string $dir
     * @return void
     */
    public function sortBy(string $name, string $dir = 'asc'): void
    {
        $this->sortBy = $name;
        $this->sortDir = $dir;
        $this->syncWithDataTable('sort');
    }

    /**
     * @param $values
     * @param string $datatableId
     * @return void
     */
    public function setFilters($values, string $datatableId)
    {
        if ($datatableId === $this->datatable->id) {
            $this->filters = collect($this->filters)->merge($values)->toArray();
            $this->syncWithDataTable('filters');
        }
    }

    /**
     * @param string $datatableId
     * @return void
     */
    public function removeFilters(string $datatableId)
    {
        if ($datatableId === $this->datatable->id) {
            $this->reset('filters');
            $this->emitTo('datatable::filters', 'refresh', $this->datatable->id);
            $this->syncWithDataTable('filters');
        }
    }

    /**
     * @param $pageLength
     * @return void
     */
    public function updatedPageLength($pageLength): void
    {
        $this->pageLength = $pageLength;
        $this->syncWithDataTable('pagination');
    }

    /**
     * @param array $events
     * @return void
     */
    public function dispatchBrowserEvents(array $events): void
    {
        foreach ($events as $event) {
            if (is_array($event)) {
                $event = $event[0] ?? null;
                $data = $event[1] ?? null;
            }

            $this->dispatchBrowserEvent($event, $data ?? null);
        }
        $this->emitUp('refresh');
    }

    /**
     * @param array $events
     * @return void
     */
    public function dispatchListeners(array $events): void
    {
        foreach ($events as $event) {
            if (is_array($event)) {
                $event = $event[0] ?? null;
                $data = $event[1] ?? null;
            }

            $this->emit($event, $data ?? null);
        }
    }

    /**
     * @return void
     */
    public function notify(): void
    {
        $this->emitTo('datatable::notifications', 'notify');
    }

    /**
     * @param $id
     * @param bool $confirmed
     * @return bool|void
     */
    public function deleteAction($id, bool $confirmed = false)
    {
        /** @var \Modules\DataTable\Core\Actions\DeleteAction $deleteAction */
        $deleteAction = $this->datatable->actions()->firstWhere('name', 'delete');

        if ($deleteAction && ($entity = $this->datatable->model::find($id)) && $deleteAction->isAvailable($entity)) {
            if ($deleteAction->requireConfirmation && !$confirmed) {
                $this->dispatchBrowserEvent("show-delete-datatable-record-modal-$id-{$deleteAction->name}");
                return false;
            }

            $this->emitUp('beforeDeleteActionPerformed', $entity);
            $this->dispatchBrowserEvents($deleteAction->beforeBrowserEvents);
            $this->dispatchListeners($deleteAction->beforeListeners);

            if ($entity->delete()) {
                $this->notify();
                $this->emitUp('afterDeleteActionPerformed', $entity);
                $this->dispatchBrowserEvents($deleteAction->afterBrowserEvents);
                $this->dispatchListeners($deleteAction->afterListeners);

                return true;
            }
        }
    }

    /**
     * @param $id
     * @param bool $confirmed
     * @return bool|void
     * @throws \Exception
     */
    public function duplicateAction($id, bool $confirmed = false)
    {
        /** @var \Modules\DataTable\Core\Actions\DuplicateAction $duplicateAction */
        $duplicateAction = $this->datatable->actions()->firstWhere('name', 'duplicate');

        /** @var \Illuminate\Database\Eloquent\Model $entity */
        if ($duplicateAction && ($entity = $this->datatable->model::find($id)) && $duplicateAction->isAvailable($entity)) {
            if ($duplicateAction->requireConfirmation && !$confirmed) {
                $this->dispatchBrowserEvent("show-duplicate-datatable-record-modal-$id-$duplicateAction->name");
                return false;
            }

            $this->emitUp('beforeDuplicateActionPerformed', $entity);

            if ($entity = $entity->duplicate()) {
                $this->notify();
                $this->emitUp('afterDuplicateActionPerformed', $entity);
                if (!empty($route = $duplicateAction->getRedirectRoute($entity))) {
                    $this->redirect($route);
                }

                return true;
            }
        }
    }

    /**
     * @param int $id
     * @param string $actionName
     * @param bool $confirmed
     *
     * @return bool|void
     * @throws \Exception
     */
    public function customAction(int $id, string $actionName, bool $confirmed = false)
    {
        /** @var \Modules\DataTable\Core\Actions\CustomAction|null $customAction */
        $customAction = $this->datatable->actions()->firstWhere('name', $actionName);

        if ($customAction && $entity = $this->datatable->model::find($id)) {
            if ($customAction->getRequireConfirmation($entity) && !$confirmed) {
                $this->dispatchBrowserEvent("show-custom-datatable-record-modal-{$id}-{$customAction->name}");
                return false;
            }

            $this->emitUp('beforeCustomActionPerformed', $entity);
            $this->dispatchBrowserEvents($customAction->beforeBrowserEvents);
            $this->dispatchListeners($customAction->beforeListeners);

            $customAction->getActionHandler()->call($this, $entity);
            $this->notify();

            $this->emitUp('afterCustomActionPerformed', $entity);
            $this->dispatchBrowserEvents($customAction->afterBrowserEvents);
            $this->dispatchListeners($customAction->afterListeners);

            return true;
        }
    }

    /**
     * @param $id
     * @param string $actionName
     * @param bool $confirmed
     * @param mixed|null $currentOption
     * @param mixed|null $previousOption
     * @return bool|void
     * @throws \Exception
     */
    public function selectAction($id, string $actionName, bool $confirmed = false, mixed $currentOption = null, mixed $previousOption = null)
    {
        /** @var \Modules\DataTable\Core\Actions\SelectAction|null $selectAction */
        $selectAction = $this->datatable->actions()->firstWhere('name', $actionName);

        if ($selectAction && $entity = $this->datatable->model::find($id)) {
            $selectAction->setValue($id, [
                'currentOption' => $currentOption,
                'previousOption' => $previousOption,
            ]);

            if ($selectAction->getRequireConfirmation($entity, $currentOption, $previousOption) && !$confirmed) {
                $this->dispatchBrowserEvent("show-select-datatable-record-modal-{$id}-{$selectAction->name}");
                return false;
            }

            $this->emitUp('beforeSelectActionPerformed', $entity);
            $this->dispatchBrowserEvents($selectAction->beforeBrowserEvents);
            $this->dispatchListeners($selectAction->beforeListeners);

            $selectAction->getActionHandler()->call($this, $entity, $currentOption, $previousOption);
            $this->notify();

            $this->emitUp('afterSelectActionPerformed', $entity);
            $this->dispatchBrowserEvents($selectAction->afterBrowserEvents);
            $this->dispatchListeners($selectAction->afterListeners);

            $this->emitSelf('refresh');

            return true;
        }
    }
}
