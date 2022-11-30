<?php

namespace Modules\DataTable\Core\Collections;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Modules\DataTable\Core\Abstracts\DataTableConstraint;
use Modules\DataTable\Core\Collections\Traits\SerializableCollection;

/**
 * @package Modules\DataTable\Core\Collections
 */
class ConstraintsCollection extends Collection
{
    use SerializableCollection;

    /**
     * @param $key
     * @param $default
     * @return mixed
     */
    public function get($key, $default = null): mixed
    {
        return Arr::first($this->items, function ($item) use ($key) {
            return $item->name === $key;
        }, $default);
    }

    /**
     * @return \Modules\DataTable\Core\Collections\ConstraintsCollection|m.\Modules\DataTable\Core\Collections\ConstraintsCollection.filter
     */
    public function visible(): m|ConstraintsCollection
    {
        return $this->filter(fn(DataTableConstraint $constraint) => $constraint->isVisibile());
    }
}