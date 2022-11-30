<?php

namespace Modules\DataTable\Core\Traits;

use Modules\DataTable\Core\Facades\Column;

/**
 * Trait HasPagination
 *
 * @package Modules\DataTable\Core\Traits
 */
trait HasPagination
{
    /** @var string */
    public string $sortBy;

    /** @var string */
    public string $sortDir;

    /** @var bool */
    public bool $paginate = true;

    /** @var int */
    public int $pageLength = 10;

    /** @var bool */
    public bool $hasQueryStrings = true;

    /**
     * Mount Sorting
     *
     * @return $this
     */
    public function mountSorting(): static
    {
        if (!$this->columns()->get($this->sortBy)) {
            $this->addColumns(Column::text($this->sortBy)->setLabel('')->setVisibility(false));
        }

        return $this;
    }

    /**
     * @return int[]
     */
    public function pageLengthMenu(): array
    {
        return [10, 25, 50, 100];
    }
}