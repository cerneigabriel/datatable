<?php

namespace Modules\DataTable\Core\Resolvers;

use Exception;
use Illuminate\Support\Collection;

/**
 * Class RouteResolver
 *
 * @package Modules\DataTable\Core\Resolvers
 */
class RouteResolver
{
    /** @var object|null */
    protected ?object $entity;

    /** @var string */
    public string $route;

    /** @var Collection */
    public Collection $routeParameters;

    /**
     * @param string $route
     * @param array $routeParameters
     * @param array|object|null $entity
     */
    public function __construct(string $route, array $routeParameters = [], array|object|null $entity = null)
    {
        $this->setRoute($route, $routeParameters);
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
     * @param string $route
     * @param array $routeParameters
     * @param array|object|null $entity
     * @return \Modules\DataTable\Core\Resolvers\RouteResolver
     */
    public static function make(string $route, array $routeParameters = [], array|object|null $entity = null): RouteResolver
    {
        return new static($route, $routeParameters, $entity);
    }


    /**
     * @param array|object|null $entity
     * @return string
     * @throws \Exception
     */
    public function getRoute(array|object|null $entity = null): string
    {
        if (!is_null($entity)) {
            $this->setEntity($entity);
        }

        $parameters = $this->getParameters();

        try {
            return route($this->route, $parameters->toArray());
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * @return \Illuminate\Support\Collection
     * @throws \Exception
     */
    private function getParameters(): Collection
    {
        return $this->routeParameters->mapWithKeys(function ($attribute, $parameter) {
            return $this->mapParametersRecursive($attribute, $parameter);
        });
    }

    /**
     * @param $attribute
     * @param $parameter
     * @return array
     * @throws \Exception
     */
    private function mapParametersRecursive($attribute, $parameter): array
    {
        if ($attribute instanceof Collection) {
            return [$parameter => $attribute->mapWithKeys(fn ($attr, $param) => $this->mapParametersRecursive($attr, $param))];
        }
        return [$parameter => EloquentAttributeResolver::make($attribute, $this->entity)->extractData() ?? null];
    }

    /**
     * @param string $route
     * @param string[] $routeParameters
     * @return $this
     */
    public function setRoute(string $route, array $routeParameters = []): static
    {
        $this->route = $route;
        $this->routeParameters = r_collect($routeParameters);

        return $this;
    }
}