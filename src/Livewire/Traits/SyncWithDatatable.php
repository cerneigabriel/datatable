<?php

namespace Modules\DataTable\Livewire\Traits;

use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * Trait SyncWithDatatable
 *
 * @package Modules\DataTable\Livewire\Traits
 */
trait SyncWithDatatable
{
    /** @var bool */
    public bool $refreshing = false;

    /**
     * $syncItem = [ * | search | constraint | filters | sort | pagination | queryString | queryStringSearch | queryStringFilters | queryStringSort | queryStringPagination | cacheDataTable | getCachedDataTable ]
     *
     * @param string $syncItem
     * @return void
     */
    protected function syncWithDataTable(string $syncItem = '*'): void
    {
//        try {
//            $authId = Auth::id() ?? '';
//            $datatableCacheKey = (Auth::check() ? "users.$authId." : '') . "datatables.$this->datatableId";
//
//            if ($syncItem == 'cacheDataTable') {
//                Cache::forever($datatableCacheKey, $this->datatable);
//            }
//
//            if ($syncItem == 'getCachedDataTable') {
//                $this->datatable = Cache::get($datatableCacheKey);
//            }

//            if (in_array($syncItem, ['*', 'search', 'constraint']) && $this->datatable->isSearchable()) {
//                $this->datatable->setSearch($this->search);
//                $this->datatable->setConstraint($this->constraint);
//            }
//
//            if (in_array($syncItem, ['*', 'filters']) && $this->datatable->isFilterable()) {
//                $this->filters = $this->datatable->filters()->whereGroup($this->filtersGroup ?? null)->setValues($this->filters)->toArray();
//            }


            if ($syncItem === 'reset-filters' && $this->datatable->isFilterable()) {
                /** @var \Modules\DataTable\Core\Abstracts\DataTable $newDatatable */
                $newDatatable = (new ($this->datatable::class)());

                // Reset Filters to default values
                $this->datatable->filters = $newDatatable->filters;
                $this->filters = $this->datatable->filters()->whereGroup($this->filtersGroup ?? null)->getValues()->toArray();
            }

            if (in_array($syncItem, ['*', 'sort']) && $this->datatable->isSortable()) {
                if (!$this->sortBy || !$this->sortDir || !$this->datatable->sortBy($this->sortBy, $this->sortDir)) {
                    $this->sortBy = $this->datatable->sortBy;
                    $this->sortDir = $this->datatable->sortDir;
                }
            }

            if (in_array($syncItem, ['*', 'pagination'])) {
                if ($this->pageLength) {
                    $this->datatable->pageLength = $this->pageLength;
                } else {
                    $this->pageLength = $this->datatable->pageLength;
                }
            }

            if (in_array($syncItem, ['search', 'constraint', 'filters', 'pagination']) && method_exists($this::class, 'resetPage')) {
                $this->resetPage();
            }

//            if (in_array($syncItem, ['queryString', 'queryStringSearch', 'queryStringFilters', 'queryStringSort', 'queryStringPagination'])) {
//                if (!$this->datatable->hasQueryStrings) {
//                    $this->queryString = [];
//                } else {
//                    if (in_array($syncItem, ['queryString', 'queryStringSearch']) && $this->datatable->isSearchable()) {
//                        $this->queryString = collect($this->queryString)->merge([
//                            'search' => ['as' => "{$this->datatable->name}_search", 'except' => ''],
//                        ])->toArray();
//
//                        $this->search = request()->query("{$this->datatable->name}_search", $this->search);
//
//                        if ($this->datatable->hasConstraints()) {
//                            $this->queryString = collect($this->queryString)->merge([
//                                'constraint' => ['as' => "{$this->datatable->name}_constraint", 'except' => ''],
//                            ])->toArray();
//
//                            $this->constraint = request()->query("{$this->datatable->name}_constraint", $this->constraint);
//                        }
//                    }
//
//                    if (in_array($syncItem, ['queryString', 'queryStringFilters']) && $this->datatable->isFilterable()) {
//                        $this->queryString = collect($this->queryString)->merge(['filters' => ['as' => "{$this->datatable->name}_filters"]])->toArray();
//
//                        $this->filters = request()->query("{$this->datatable->name}_filters", $this->filters);
//                    }
//
//                    if (in_array($syncItem, ['queryString', 'queryStringSort']) && $this->datatable->isSortable()) {
//                        $this->queryString = collect($this->queryString)->merge([
//                            'sortBy' => ['as' => "{$this->datatable->name}_sortBy"],
//                            'sortDir' => ['as' => "{$this->datatable->name}_sortDir"],
//                        ])->toArray();
//
//                        $this->sortBy = request()->query("{$this->datatable->name}_sortBy", $this->sortBy);
//                        $this->sortDir = request()->query("{$this->datatable->name}_sortDir", $this->sortDir);
//                    }
//
//                    if (in_array($syncItem, ['queryString', 'queryStringPagination']) && $this->datatable->paginate) {
//                        $this->queryString = collect($this->queryString)->merge(['pageLength' => ['as' => "{$this->datatable->name}_perPage", 'except' => '10']])->toArray();
//
//                        $this->pageLength = request()->query("{$this->datatable->name}_perPage", $this->pageLength);
//                    }
//                }
//            }
//        } catch (Throwable $throwable) {
//            Log::channel('datatable')->error('sync.datatable.error', [
//                'message' => $throwable->getMessage(),
//                'line' => $throwable->getLine(),
//                'code' => $throwable->getCode(),
//                'trace' => $throwable->getTrace(),
//            ]);
//            $this->emit('datatable.error');
//        }
    }
}