<?php

namespace Modules\DataTable\Core\Constraints;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Modules\DataTable\Core\Abstracts\DataTableConstraint;

/**
 * @package Modules\DataTable\Core\Filters
 */
class BooleanColumnConstraint extends DataTableConstraint
{
    /** @var string */
    public string $type = 'boolean';

    /** @var string */
    public string $true_string = 'Yes';

    /** @var string */
    public string $false_string = 'No';

    /**
     * @param $value
     * @return bool
     */
    public function canQuery($value): bool
    {
        $value = strtolower(trim($value));
        return Str::startsWith(strtolower($this->true_string), $value) || Str::startsWith(strtolower($this->false_string), $value);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setQuery(Builder $query, $value): Builder
    {
        $value = strtolower(trim($value));

        return $query->where(
            $this->extractRelationshipFromAttribute() ? $this->extractAttributeWithoutRelationship() : $this->attribute,
            Str::startsWith(strtolower($this->true_string), $value) || !Str::startsWith(strtolower($this->false_string), $value)
        );
    }

    /**
     * @param string $false_string
     * @return $this
     */
    public function setFalseString(string $false_string): self
    {
        $this->false_string = trim($false_string);

        return $this;
    }

    /**
     * @param string $true_string
     * @return $this
     */
    public function setTrueString(string $true_string): self
    {
        $this->true_string = trim($true_string);

        return $this;
    }
}