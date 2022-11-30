<?php

namespace Modules\DataTable\Core\Collections;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Modules\DataTable\Core\Collections\Traits\SerializableCollection;

/**
 * Class ActionsCollection
 *
 * @package Modules\DataTable\Core\Collections
 */
class ActionsCollection extends Collection
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
}