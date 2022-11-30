<?php

namespace Modules\DataTable\Core\Traits;

use Illuminate\Database\Eloquent\Builder;
use Modules\DataTable\Core\Resolvers\EloquentAttributeResolver;

trait HasRelationships
{
    /**
     * @return string
     */
    public function extractAttributeWithoutRelationship(): string
    {
        return EloquentAttributeResolver::make($this->attribute)->extractAttributeWithoutRelationship();
    }

    /**
     * @return array
     */
    public function extractRelationshipFromAttribute(): array
    {
        return EloquentAttributeResolver::make($this->attribute)->extractRelationshipFromAttribute();
    }

    /**
     * @return bool
     */
    public function attributeContainsRelationship(): bool
    {
        return EloquentAttributeResolver::make($this->attribute)->attributeContainsRelationship();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return bool
     */
    public function queryable(Builder $builder): bool
    {
        return EloquentAttributeResolver::make($this->attribute)->queryable($builder);
    }
}