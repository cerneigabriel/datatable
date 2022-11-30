<?php

namespace Modules\DataTable\Core\Actions;

use Modules\DataTable\Core\Abstracts\DataTable;
use Modules\DataTable\Core\Abstracts\DataTableAction;
use Modules\DataTable\Core\Traits\HasRoute;

/**
 * Class LinkAction
 *
 * @package Modules\DataTable\Core\Facades
 */
class LinkAction extends DataTableAction
{
    use HasRoute;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->resetClasses();
        $this->addClass('link-info');
    }

    /**
     * @param $entity
     * @return string
     * @throws \Exception
     */
    public function resolveData($entity): string
    {
        return view('datatable::actions.link', [
            'action' => $this,
            'entity' => $entity
        ])->render();
    }

    /**
     * @param string $route
     * @param array $routeParameters
     * @param string $datatableClass
     * @param array $filters
     * @return $this
     * @throws \Exception
     */
    public function setDataTableRoute(string $route, array $routeParameters = [], string $datatableClass = '', array $filters = []): static
    {
        $this->setRoute($route, $routeParameters);

        if ($datatableClass && class_exists($datatableClass) && is_subclass_of($datatableClass, DataTable::class)) {
            /** @var DataTable $datatable */
            $datatable = new $datatableClass;
            $this->addRouteParameters(["{$datatable->name}_filters" => $filters]);
        }

        return $this;
    }
}