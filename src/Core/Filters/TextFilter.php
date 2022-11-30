<?php

namespace Modules\DataTable\Core\Filters;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Modules\DataTable\Core\Abstracts\DataTableFilter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TextColumn
 *
 * @package Modules\DataTable\Core\Filters
 */
class TextFilter extends DataTableFilter
{
    /** @var string */
    public string $type = 'text';

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws \Exception
     */
    protected function setQuery(Builder $query, $value): Builder
    {
        return $query->where($query->getModel()->getTable() . '.' . $this->extractAttributeWithoutRelationship(), 'like', "%$value%");
    }

    /**
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\Support\Htmlable|string|\Closure
     */
    protected function render(): View|Htmlable|string|Closure
    {
        return view('datatable::filters.text-filter');
    }
}