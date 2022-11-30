<?php

namespace Modules\DataTable\Core\Traits;

use Modules\DataTable\Core\Abstracts\DataTableColumn;
use Modules\DataTable\Core\Collections\ColumnsCollection;

/**
 * Trait HasColumns
 *
 * @package Modules\DataTable\Core\Traits
 */
trait HasColumns
{
    /** @var bool */
    public bool $enableSorting = true;

    /** @var \Modules\DataTable\Core\Collections\ColumnsCollection */
    public ColumnsCollection $columns;

    /**
     * @param \Modules\DataTable\Core\Abstracts\DataTableColumn ...$columns
     *
     * @return $this
     */
    public function setColumns(DataTableColumn ...$columns): self
    {
        $this->columns = ColumnsCollection::make($columns)->unique('name');

        return $this;
    }

    /**
     * @param \Modules\DataTable\Core\Abstracts\DataTableColumn $column
     * @param string                                            $direction
     *
     * @return bool
     */
    public function isCurrentSortingColumn(DataTableColumn $column, string $direction): bool
    {
        return $this->sortBy === $column->name && $this->sortDir === $direction;
    }

    /**
     * @return bool
     */
    public function isSortable()
    {
        return $this->enableSorting;
    }

    /**
     * @param string $name
     * @param string $dir
     * @return bool
     */
    public function sortBy(string $name, string $dir = 'asc'): bool
    {
        if ($this->columns()->get($name) && $this->columns()->get($name)->sortable) {
            $this->sortBy = $name;
            $this->sortDir = $dir;

            return true;
        }

        return false;
    }

    /**
     * Get Columns
     *
     * @return \Modules\DataTable\Core\Collections\ColumnsCollection
     */
    public function columns(): ColumnsCollection
    {
        return $this->columns;
    }

    /**
     * @param \Modules\DataTable\Core\Abstracts\DataTableColumn ...$columns
     *
     * @return $this
     */
    public function addColumns(DataTableColumn ...$columns): static
    {
        $this->columns->push(...$columns);

        return $this;
    }
}