<?php

namespace Modules\DataTable\Core\Abstracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Modules\DataTable\Core\Interfaces\Abstracts\DataTableConstraintInterface;

/**
 * @package Modules\DataTable\Core\Abstracts
 */
abstract class DataTableConstraint extends SerializableDataTable implements DataTableConstraintInterface
{
    /** @var string */
    public string $type;

    /** @var string */
    public string $name;

    /** @var string */
    public string $attribute;

    /** @var string|mixed|null */
    public ?string $label = null;

    /** @var bool */
    public bool $visibility = true;

    /**
     * @return bool
     */
    public function isVisibile(): bool
    {
        return $this->visibility;
    }

    /**
     * @param bool $visibility
     * @return $this
     */
    public function setVisibility(bool $visibility): static
    {
        $this->visibility = $visibility;

        return $this;
    }

    /**
     * @param string $name
     * @param string $attribute
     * @param string $label
     */
    public function __construct(string $name, string $attribute, string $label)
    {
        $this->mountSerialization();
        $this->name = $name;
        $this->attribute = $attribute;
        $this->label = $label;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $term
     * @param bool $andOperator
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Builder $query, string $term, bool $andOperator = true): Builder
    {
        $clause = $andOperator ? 'where' : 'orWhere';

        return $query->when($this->canQuery($term), function (Builder $query) use ($andOperator, $clause, $term) {

            if ($relationship = $this->extractRelationshipFromAttribute()) {
                return $query->{"{$clause}Has"}($relationship, function (Builder $query) use ($term) {

                    if (method_exists($this::class, 'setQuery')) {
                        return $this->setQuery($query, $term);
                    }

                    return $query->where("{$query->getModel()->getTable()}.{$this->extractAttributeWithoutRelationship()}", 'like', "%{$term}%");
                });
            }

            if ($scope = $this->extractScopeFromAttribute()) {
                return $query->$clause(fn(Builder $builder) => $builder->{$scope}($term));
            } elseif (method_exists($this::class, 'setQuery')) {
                return $query->$clause(fn(Builder $builder) => $this->setQuery($builder, $term));
            }

            return $query->$clause("{$query->getModel()->getTable()}.$this->attribute", 'like', "%{$term}%");
        });
    }

    /**
     * @param $value
     * @return bool
     */
    public function canQuery($value): bool
    {
        return true;
    }

    /**
     * @return string|null
     */
    protected function extractRelationshipFromAttribute(): string|null
    {
        $relationship = collect(explode('.', $this->attribute));

        $relationship->pop();

        if (!$relationship->count()) {
            return null;
        }

        return $relationship->implode('.');
    }

    /**
     * @return string|null
     */
    public function extractAttributeWithoutRelationship(): string|null
    {
        $attribute = collect(explode('.', $this->attribute));

        if ($attribute->count() > 1) {
            return $attribute->last();
        }

        return null;
    }

    /**
     * @return string|null
     */
    private function extractScopeFromAttribute(): string|null
    {
        if (!str_starts_with($this->attribute, 'scope:')) {
            return null;
        }

        return Str::camel(str_replace('scope:', '', $this->attribute));
    }
}
