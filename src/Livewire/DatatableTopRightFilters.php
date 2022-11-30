<?php

namespace Modules\DataTable\Livewire;

use Livewire\Component;
use Modules\DataTable\Core\Abstracts\DataTable;
use Modules\DataTable\Livewire\Traits\HasDatatable;
use Modules\DataTable\Core\Abstracts\DataTableFilter;
use Modules\DataTable\Livewire\Traits\HasFilters;
use Modules\DataTable\Livewire\Traits\SyncWithDatatable;

/**
 * Class DatatableTopRightFilters
 *
 * @package Modules\DataTable\Livewire
 */
class DatatableTopRightFilters extends Component
{
    use HasFilters;
    use HasDatatable;

    /** @var string */
    public string $filtersGroup = DataTableFilter::GroupDataTableTopRight;

    /** @var int */
    public int $recordsCount = 0;

    /**
     * @return string[]
     */
    public function getListeners(): array
    {
        return array_merge(
            [
                'refreshRecordsCount'
            ],
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
     * Refresh Records Count
     *
     * @param int $recordsCount
     *
     * @return void
     */
    public function refreshRecordsCount(int $recordsCount = 0): void
    {
        $this->recordsCount = $recordsCount;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('datatable::datatable-top-right-filters', [
            'datatable' => $this->datatable,
        ]);
    }
}
