<?php

namespace Modules\DataTable\Core\Traits;

/**
 * Trait HasSearch
 *
 * @package Modules\DataTable\Core\Traits
 */
trait HasSearch
{
    /** @var bool */
    public bool $enableSearch = true;

    /** @var string|null */
    public ?string $search = null;

    /**
     * @return bool
     */
    public function isSearchable(): bool
    {
        return $this->enableSearch && $this->columns()->where('searchable', true)->count();
    }

    /**
     * @param string $search
     * @return $this
     */
    public function setSearch(string $search)
    {
        $this->search = $search;

        return $this;
    }
}