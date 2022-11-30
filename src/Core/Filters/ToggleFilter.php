<?php

namespace Modules\DataTable\Core\Filters;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Modules\DataTable\Core\Abstracts\DataTableFilter;

/**
 * Class BooleanColumn
 *
 * @package App\DataTables\Core\Filters
 */
class ToggleFilter extends DataTableFilter
{
    /** @var string */
    public string $type = 'toggle';

    /**
     * @param string|null $key
     * @param $default
     * @return bool
     */
    public function value(string $key = null, $default = null): bool
    {
        return (bool)$this->value;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setQuery(Builder $query, $value): Builder
    {
        return $query->where($query->getModel()->getTable() . '.' . $this->extractAttributeWithoutRelationship(), $value);
    }

    /**
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\Support\Htmlable|string|\Closure
     */
    protected function render(): View|Htmlable|string|Closure
    {
        return view('datatable::filters.toggle-filter');
    }
}