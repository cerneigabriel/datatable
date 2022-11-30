<?php

namespace Modules\DataTable\Core\Filters;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Modules\DataTable\Core\Abstracts\DataTableFilter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class IDFilter
 *
 * @package Modules\DataTable\Core\Filters
 */
class IDFilter extends DataTableFilter
{
    /** @var string */
    public string $type = 'id';

    /** @var string */
    public string $name = 'id';

    /** @var string */
    public string $attribute = 'id';

    /** @var string|null */
    public ?string $label = null;

    /**
     * @param string|null $name
     * @param string|null $attribute
     * @param string|null $label
     */
    public function __construct(string $name = null, string $attribute = null, string $label = null)
    {
        parent::__construct($name ?? $this->name, $attribute ?? $this->attribute, $label ?? $this->label);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws \Exception
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
        return view('datatable::filters.id-filter');
    }
}