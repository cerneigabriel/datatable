<?php

namespace Modules\DataTable\Core\Constraints;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Modules\DataTable\Core\Abstracts\DataTableConstraint;

/**
 * @package Modules\DataTable\Core\Filters
 */
class CountColumnConstraint extends DataTableConstraint
{
    /** @var string */
    public string $type = 'count';

    /**
     * @param $value
     * @return bool
     */
    public function canQuery($value): bool
    {
        return ctype_digit($value) && ((int) $value) >= 0;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setQuery(Builder $query, $value): Builder
    {
        $value = (int) $value;

        return $query->has($this->extractRelationshipFromAttribute() ? $this->extractAttributeWithoutRelationship() : $this->attribute, $value);
    }
}