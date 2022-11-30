<?php

namespace Modules\DataTable\Core\Collections;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Modules\DataTable\Core\Abstracts\DataTableFilter;
use Modules\DataTable\Core\Collections\m;
use Modules\DataTable\Core\Collections\Traits\SerializableCollection;

/**
 * Class FiltersCollection
 *
 * @package Modules\DataTable\Core\Collections
 */
class FiltersCollection extends Collection
{
    use SerializableCollection;

    /**
     * @param $key
     * @param $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::first($this->items, function ($item) use ($key) {
            return $item->name === $key;
        }, $default);
    }

    /**
     * @param array $values
     * @return \Modules\DataTable\Core\Collections\FiltersCollection|\Illuminate\Support\Collection|m.\Modules\DataTable\Core\Collections\FiltersCollection.mapWithKeys
     */
    public function setValues(array $values)
    {
        if (empty($values)) {
            $this->each(function ($filter) {
                $filter->setValue(null);
            });
        }

        foreach ($values as $filter => $value) {
            $filterIndex = $this->search(function ($item) use ($filter) {
                return $item->name === $filter;
            });

            if (is_numeric($filterIndex)) {
                $this->items[$filterIndex]->setValue($value);
            }
        }

        return $this->getValues();
    }

    /**
     * @return \Modules\DataTable\Core\Collections\FiltersCollection|\Illuminate\Support\Collection|m.\Modules\DataTable\Core\Collections\FiltersCollection.mapWithKeys
     */
    public function getValues()
    {
        return $this->mapWithKeys(function ($filter) {
            try {
                return [$filter->name => $filter->value()];
            } catch(\Throwable) {

                dd($this->items);
            }
        });
    }

    /**
     * @return \Modules\DataTable\Core\Collections\FiltersCollection|\Illuminate\Support\Collection|m.\Modules\DataTable\Core\Collections\FiltersCollection.mapWithKeys
     */
    public function getDefaultValues()
    {
        return $this->mapWithKeys(function ($filter) {
            return [$filter->name => $filter->defaultValue()];
        });
    }

    /**
     * @return bool
     */
    public function hasValues()
    {
        return $this->filter(function ($item) {
            return $item->value() !== $item->defaultValue();
        })->isNotEmpty();
    }

    /**
     * @return \Modules\DataTable\Core\Collections\FiltersCollection|m.\Modules\DataTable\Core\Collections\FiltersCollection.filter
     */
    public function whereNotNullValue()
    {
        return $this->filter(function ($item) {
            return !is_null($item->value());
        });
    }

    /**
     * @param string|null $group
     * @return $this
     */
    public function whereGroup(?string $group = null): self
    {
        return $this->when($group, function ($collection, $group) {
            return $collection->filter(function (DataTableFilter $filter) use ($group) {
                return $filter->group === $group;
            });
        });
    }

    /**
     * @return string
     */
    public function render()
    {
        return implode('', Arr::map($this->items, function ($item) {
            return $item->resolveRender();
        }));
    }
}