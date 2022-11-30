<?php

namespace Modules\DataTable\Livewire;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Modules\DataTable\Core\Abstracts\DataTable;
use Modules\DataTable\Livewire\Traits\HasDatatable;
use Modules\DataTable\Livewire\Traits\EmitToMultiple;
use Modules\DataTable\Livewire\Traits\SyncWithDatatable;

/**
 * Class Filters
 *
 * @package Modules\DataTable\Livewire
 */
class FiltersRemove extends Component
{
    use EmitToMultiple;
    use SyncWithDatatable;
    use HasDatatable;

    /** @var bool */
    public bool $showRemoveFilters = false;

    /**
     * @return string[]
     */
    public function getListeners(): array
    {
        return array_merge(
            [
                'refresh' => 'checkIfCanShowRemoveFilters'
            ],
            $this->datatableListeners(),
        );
    }

    /**
     * @param \Modules\DataTable\Core\Abstracts\DataTable $datatable
     * @return void
     */
    public function mount(DataTable $datatable): void
    {
        $this->mountDatatable();
    }

    /**
     * @return void
     */
    public function booted()
    {
        $this->refresh();
    }

    /**
     * @return void
     */
    public function refresh(): void
    {
        $this->checkIfCanShowRemoveFilters();
    }

    /**
     * @return void
     */
    public function checkIfCanShowRemoveFilters(): void
    {
        $this->showRemoveFilters = $this->datatable->showResetFiltersButton();
    }

    /**
     * @return void
     */
    public function removeFilters(): void
    {
        $this->emit('resetFilters', $this->datatable->id);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render(): View|Factory|Application
    {
        return view('datatable::filters-remove', [
            'datatable' => $this->datatable
        ]);
    }
}
