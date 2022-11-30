<?php

namespace Modules\DataTable\Livewire;

use Livewire\Component;
use Modules\DataTable\Core\Abstracts\DataTable;
use Modules\DataTable\Livewire\Traits\HasDatatable;
use Modules\DataTable\Core\Abstracts\DataTableFilter;
use Modules\DataTable\Livewire\Traits\HasFilters;
use Modules\DataTable\Livewire\Traits\SyncWithDatatable;

/**
 * Class Search
 *
 * @package Modules\DataTable\Livewire
 */
class Search extends Component
{
    use HasFilters;
    use HasDatatable;

    /** @var string */
    public string $search = '';

    /** @var string  */
    public string $constraint = '';

    /** @var array  */
    public array $constraints = [];

    /** @var string */
    public string $title = 'Search';

    /** @var string|null */
    public string|null $description = null;

    /** @var string */
    public string $placeholder = 'Enter search term';

    /** @var string  */
    public string $searchByPlaceholder = 'Search by';

    /** @var bool */
    public bool $showResetButton = true;

    /** @var string */
    public string $filtersGroup = DataTableFilter::GroupSearch;

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
     * @param string $title
     * @param string|null $description
     * @param string $placeholder
     * @param $searchByPlaceholder
     * @param bool $showResetButton
     * @return void
     */
    public function mount(DataTable $datatable, string $title = 'Search', string $description = null, string $placeholder = 'Enter search term', $searchByPlaceholder = 'Search by', bool $showResetButton = true)
    {
        $this->title = $title;
        $this->description = $description;
        $this->placeholder = $placeholder;
        $this->searchByPlaceholder = $searchByPlaceholder;
        $this->showResetButton = $showResetButton;

        $this->mountDatatable();
        $this->mountFilters();

        if ($this->datatable->hasConstraints()) {
            $this->constraints = [null => $this->searchByPlaceholder] + $this->datatable->constraintsOptions();
        }
    }

    /**
     * @return void
     */
    public function booted()
    {
        $this->setQueryString(array_merge(
            $this->datatable->isSearchable()
                ? ['search' => ['as' => "{$this->datatable->name}_search", 'except' => '']]
                : [],
            $this->datatable->isSearchable() && $this->datatable->hasVisibleConstraints()
                ? ['constraint' => ['as' => "{$this->datatable->name}_constraint", 'except' => '']]
                : [],
            $this->datatable->isFilterable() && $this->datatable->filters()->whereGroup($this->filtersGroup)->isNotEmpty()
                ? ['filters' => ['as' => "{$this->datatable->name}_filters_$this->filtersGroup"]]
                : []
        ));
        $this->bootFilters();
    }

    /**
     * @return void
     */
    public function updatedSearch()
    {
        $this->datatable->setSearch($this->search);
        $this->syncDatatableComponents([
            'datatable::filters',
            'datatable::filters.remove'
        ]);
    }

    /**
     * @return void
     */
    public function updatedConstraint()
    {
        $this->datatable->setConstraint($this->constraint);
        $this->syncDatatableComponents([
            'datatable::filters',
            'datatable::filters.remove'
        ]);
    }

    /**
     * @return void
     */
    public function resetSearch(): void
    {
        $this->reset('search', 'constraint');
        $this->datatable->setSearch($this->search)->setConstraint($this->constraint);
        $this->syncDatatableComponents([
            'datatable::filters',
            'datatable::filters.remove'
        ]);

    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('datatable::search', [
            'datatable' => $this->datatable
        ]);
    }
}
