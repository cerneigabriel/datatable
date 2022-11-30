<?php

namespace Modules\DataTable\Core\Traits;

use Closure;
use Exception;
use Modules\DataTable\Core\Resolvers\RouteResolver;

/**
 * Trait HasRoute
 *
 * @package Modules\DataTable\Core\Actions\Traits
 */
trait HasRoute
{
    /** @var string */
    public string $route = '';

    /** @var array */
    public array $routeParameters = [];

    /** @var string */
    public string $target = '_self';

    /** @var array|string[] */
    protected array $targets = ['_blank', '_self', '_parent', '_top'];

    /**
     * @param string $target
     * @return $this
     * @throws \Exception
     */
    public function setTarget(string $target): static
    {
        if (!in_array($target, $this->targets)) {
            throw new Exception("Target {$target} not defined. Expected one of these targets: '" . implode("', '", $this->targets) . "'");
        }

        $this->target = $target;

        return $this;
    }

    /**
     * @return string
     */
    public function getTarget(): string
    {
        return $this->target;
    }

    /**
     * @param $entity
     * @return string
     * @throws \Exception
     */
    public function getRoute($entity): string
    {
        return RouteResolver::make($this->route, $this->routeParameters, $entity)->getRoute();
    }

    /**
     * @param string $route
     * @param array $routeParameters
     * @param string $target
     * @return $this
     * @throws \Exception
     */
    public function setRoute(string $route, array $routeParameters = [], string $target = '_self'): static
    {
        $this->route = $route;
        $this->routeParameters = $routeParameters;
        $this->setTarget($target);

        return $this;
    }

    /**
     * @param bool|\Closure $condition
     * @param string $route
     * @param array $routeParameters
     * @return $this
     * @throws \Exception
     */
    public function setRouteIf(bool|Closure $condition, string $route, array $routeParameters = []): static
    {
        if (is_callable($condition) ? $condition() : $condition) {
            $this->setRoute($route, $routeParameters);
        }

        return $this;
    }

    /**
     * @param array $routeParameters
     * @return $this
     */
    public function addRouteParameters(array $routeParameters = []): static
    {
        $this->routeParameters = array_merge($this->routeParameters, $routeParameters);

        return $this;
    }
}