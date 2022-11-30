<?php

namespace Modules\DataTable\Core\Actions;

use Modules\DataTable\Core\Abstracts\DataTableAction;
use Modules\DataTable\Core\Resolvers\EloquentAttributeResolver;
use Modules\DataTable\Core\Traits\HasRoute;

/**
 * Class EditAction
 *
 * @package Modules\DataTable\Core\Actions
 */
class EditAction extends DataTableAction
{
    use HasRoute;

    /** @var string */
    public string $name = 'edit';

    /** @var string */
    public string $text = 'Edit';

    /**
     * @param string $route
     * @param array $routeParameters
     * @param string|null $name
     * @throws \Exception
     */
    public function __construct(string $route, array $routeParameters = [], string $name = null)
    {
        $this->setRoute($route, $routeParameters);
        parent::__construct($name ?? $this->name);
    }

    /**
     * @param $entity
     * @return string
     */
    public function resolveData($entity): string
    {
        return view('datatable::actions.edit', [
            'action' => $this,
            'entity' => $entity,
        ])->render();
    }
}