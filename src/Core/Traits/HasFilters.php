<?php

namespace Modules\DataTable\Core\Traits;

use Modules\DataTable\Core\Abstracts\DataTableFilter;
use Modules\DataTable\Core\Collections\FiltersCollection;

/**
 * Trait HasFilters
 *
 * @package Modules\DataTable\Core\Traits
 */
trait HasFilters
{
    /** @var bool */
    public bool $enableFilters = false;

    /** @var bool */
    public bool $enableResetFilters = true;

    /** @var \Modules\DataTable\Core\Collections\FiltersCollection */
    public FiltersCollection $filters;

    /**
     * @return \Modules\DataTable\Core\Collections\FiltersCollection
     */
    public function filters(): FiltersCollection
    {
        return $this
            ->filters
            ->whereIn('name', $this->columns()->where('filterable', true)->pluck('name'))
            ->merge($this->filters->whereNotIn('name', $this->columns()->pluck('name')))
            ->sortBy(function (DataTableFilter $filter) {
                return $this->filters->search(function(DataTableFilter $orderedFilter) use ($filter) {
                    return $orderedFilter->name === $filter->name;
                });
            });
    }

    /**
     * @param \Modules\DataTable\Core\Abstracts\DataTableFilter ...$filters
     * @return $this
     */
    public function setFilters(DataTableFilter ...$filters): static
    {
        $this->filters = FiltersCollection::make($filters)->unique('name');

        return $this;
    }

    /**
     * @return bool
     */
    public function isFilterable(): bool
    {
        $columns = $this->columns()->whereIn('name', $this->filters()->pluck('name')->toArray());

        return $this->enableFilters && ($columns->isEmpty() || $columns->where('filterable', true)->count()) && $this->filters->isNotEmpty();
    }

    /**
     * @return bool
     */
    public function showResetFiltersButton(): bool
    {
        return $this->enableResetFilters && $this->isFilterable() && $this->filters()->hasValues();
    }
}