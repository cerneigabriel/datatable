<?php

namespace Modules\DataTable\Livewire;

use Livewire\Component;
use Modules\DataTable\Core\Abstracts\DataTable;
use Modules\DataTable\Livewire\Traits\HasDatatable;
use Modules\DataTable\Core\Abstracts\DataTableFilter;
use Modules\DataTable\Livewire\Traits\HasFilters;
use Modules\DataTable\Livewire\Traits\SyncWithDatatable;

/**
 * Class Filters
 *
 * @package Modules\DataTable\Livewire
 */
class Filters extends Component
{
    use HasFilters;
    use HasDatatable;

    /** @var string */
    public string $filtersGroup = DataTableFilter::GroupDefault;

    /**
     * @return string[]
     */
    public function getListeners(): array
    {
        return array_merge(
            $this->datatableListeners(),
            $this->filtersListeners(),
        );
    }

    /**
     * @param \Modules\DataTable\Core\Abstracts\DataTable $datatable
     *
     * @return void
     */
    public function mount(DataTable $datatable)
    {
        $this->mountDatatable();
        $this->mountFilters();
    }

    /**
     * @return void
     */
    public function booted()
    {
        $this->setQueryString($this->datatable->isFilterable()
            ? ['filters' => ['as' => "{$this->datatable->name}_filters_$this->filtersGroup"]]
            : []
        );
        $this->bootFilters();
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('datatable::filters', [
            'datatable' => $this->datatable,
        ]);
    }
}
