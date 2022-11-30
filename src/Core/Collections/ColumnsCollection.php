<?php

namespace Modules\DataTable\Core\Collections;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Modules\DataTable\Core\Abstracts\DataTableColumn;
use Modules\DataTable\Core\Collections\Traits\SerializableCollection;

/**
 * Class ColumnsCollection
 *
 * @package Modules\DataTable\Core\Collections
 */
class ColumnsCollection extends Collection
{
    use SerializableCollection;

    /**
     * @param $key
     * @param $default
     * @return mixed
     */
    public function get($key, $default = null): mixed
    {
        return Arr::first($this->items, function (DataTableColumn $item) use ($key) {
            return $item->name === $key;
        }, $default);
    }

    /**
     * @param \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|array $records
     * @return \Illuminate\Support\Collection|\Modules\DataTable\Core\Collections\ColumnsCollection
     */
    public function visible(LengthAwarePaginator|Collection|array $records): ColumnsCollection|Collection
    {
        if (is_array($records)) {
            $records = collect($records);
        } elseif ($records instanceof LengthAwarePaginator) {
            $records = $records->getCollection();
        }

        return $this->filter(function (DataTableColumn $item) use ($records) {
            return $records->map(function ($record) use ($item) {
                return $item->isVisible($record);
            })->unique()->min();
        })->values();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Modules\DataTable\Core\Collections\ColumnsCollection|m.\Modules\DataTable\Core\Collections\ColumnsCollection.filter
     */
    public function queryable(Builder $query): m|ColumnsCollection
    {
        $query = (clone $query);
        return $this->filter(function (DataTableColumn $column) use ($query) {
            return $column->queryable($query);
        });
    }

    /**
     * @return \Modules\DataTable\Core\Collections\ColumnsCollection|m.\Modules\DataTable\Core\Collections\ColumnsCollection.where
     */
    public function searchable(): m|ColumnsCollection
    {
        return $this->where('searchable', true);
    }

    /**
     * @return \Illuminate\Support\Collection|\Modules\DataTable\Core\Collections\ColumnsCollection
     */
    public function groupByFirstRelationship(): ColumnsCollection|Collection
    {
        return (clone $this)
            ->filter(fn(DataTableColumn $column) => $column->attributeContainsRelationship())
            ->groupBy(function (DataTableColumn $column) {
                return $column->extractRelationshipFromAttribute()[0];
            })
            ->merge($this->filter(fn(DataTableColumn $column) => !$column->attributeContainsRelationship()));
    }
}