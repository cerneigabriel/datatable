<?php

namespace Modules\DataTable\Core\Resolvers;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

/**
 * Class EloquentAttributeResolver
 *
 * @package Modules\DataTable\Core\Resolvers
 */
class EloquentAttributeResolver
{
    /** @var object|null */
    protected ?object $entity;

    /** @var string */
    protected string $attribute;

    /**
     * @param string $attribute
     * @param array|object|null $entity
     */
    public function __construct(string $attribute, array|object|null $entity = null)
    {
        $this->attribute = $attribute;
        $this->setEntity($entity);
    }

    /**
     * @param array|object|null $entity
     * @return $this
     */
    public function setEntity(array|object|null $entity = null): static
    {
        $this->entity = is_array($entity) ? (object)$entity : $entity;

        return $this;
    }

    /**
     * @static
     * @param string $attribute
     * @param array|object|null $entity
     * @return \Modules\DataTable\Core\Resolvers\EloquentAttributeResolver
     */
    public static function make(string $attribute, array|object|null $entity = null): EloquentAttributeResolver
    {
        return new static($attribute, $entity);
    }

    /**
     * @param array|object|null $entity
     * @return mixed
     * @throws \Exception
     */
    public function extractData(array|object|null $entity = null): mixed
    {
        if (!is_null($entity)) {
            $this->setEntity($entity);
        }

        if (is_null($this->entity)) {
            throw new Exception("In order to extract data from '$this->attribute' you should provide an entity.");
        }

        if ($this->attributeContainsRelationship()) {
            /** @var \Illuminate\Database\Eloquent\Model $result */
            $result = $this->entity;

            foreach ($this->extractRelationshipFromAttribute() as $relationship) {
                if ($result instanceof Model) {
                    if ($result->isRelation($relationship)) {
                        $relation = $result->getRelationValue($relationship);

                        if ($relation instanceof Collection) {
                            $result = $relation->count() > 1 ? $relation : $relation->first();
                        } elseif ($relation instanceof Model) {
                            $result = $relation;
                        } elseif (is_null($relation)) {
                            $result = null;
                            break;
                        }
                    } elseif ($result->hasAttributeMutator($relationship)) {
                        $mutator = $result->{$relationship};

                        if ($mutator instanceof Collection) {
                            $result = $mutator->count() > 1 ? $mutator : $mutator->first();
                            break;
                        } elseif ($mutator instanceof Model || is_object($mutator)) {
                            $result = $mutator;
                        } elseif (is_array($mutator)) {
                            $result = (object)$mutator;
                        }
                    }
                } elseif (is_array($result)) {
                    $result = $result[$relationship];
                } elseif (is_object($result)) {
                    $result = $result->{$relationship};
                } elseif ($result instanceof Collection) {
                    break;
                } else {
                    break;
                }
            }

            if ($result) {
                return ($result instanceof Collection && $result->count() > 1
                    ? $result->pluck($this->extractAttributeWithoutRelationship())->unique()->toArray()
                    : ($result instanceof Model || is_object($result)
                        ? $result->{$this->extractAttributeWithoutRelationship()}
                        : (is_array($result)
                            ? $result[$this->extractAttributeWithoutRelationship()]
                            : null
                        )
                    )
                );
            }
        } elseif (($result = $this->entity->{$this->attribute}) instanceof Attribute) {
            return ($result->get)();
        }
        return $this->entity->{$this->attribute};
    }

    /**
     * @return bool
     */
    public function attributeContainsRelationship(): bool
    {
        return Str::contains($this->attribute, '.');
    }

    /**
     * @return array
     */
    public function extractRelationshipFromAttribute(): array
    {
        if ($this->attributeContainsRelationship()) {
            $relationship = collect(explode('.', $this->attribute));
            $relationship->pop();

            if ($relationship->count()) {
                return $relationship->toArray();
            }
        }

        return [];
    }

    /**
     * @return string
     */
    public function extractAttributeWithoutRelationship(): string
    {
        return collect(explode('.', $this->attribute))->last();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return bool
     */
    public function queryable(Builder $builder): bool
    {
        $relationship = $this->extractRelationshipFromBuilder($builder, collect($this->extractRelationshipFromAttribute()));
        $attributeIsRelationship = $this->extractRelationshipFromBuilder($builder, collect([$this->extractAttributeWithoutRelationship()]));

        if (($this->attributeContainsRelationship() && $relationship instanceof Relation) || ($attributeIsRelationship instanceof Relation && $relationship = $attributeIsRelationship)) {
            return Schema::connection($relationship->getModel()->getConnectionName())->hasColumn(
                $relationship->getModel()->getTable(),
                ($attributeIsRelationship instanceof Relation ? $attributeIsRelationship->getModel()->getKeyName() : $this->extractAttributeWithoutRelationship())
            );
        }

        return Schema::connection($builder->getModel()->getConnectionName())->hasColumn($builder->getModel()->getTable(), $this->extractAttributeWithoutRelationship());
    }

    /**
     * @param $builder
     * @param \Illuminate\Support\Collection $relationships
     * @return mixed|null
     */
    private function extractRelationshipFromBuilder($builder, Collection $relationships): mixed
    {
        try {
            $relation = $builder->getRelation($relationships->shift());
        } catch (Throwable $throwable) {
            return null;
        }

        if ($relationships->count()) {
            return $this->extractRelationshipFromBuilder($relation, $relationships);
        }

        return $relation;
    }
}