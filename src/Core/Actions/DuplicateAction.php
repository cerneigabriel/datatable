<?php

namespace Modules\DataTable\Core\Actions;

use Modules\DataTable\Core\Abstracts\DataTableAction;
use Modules\DataTable\Core\Traits\HasRoute;

/**
 * Class DeleteAction
 *
 * @package Modules\DataTable\Core\Actions
 */
class DuplicateAction extends DataTableAction
{
    use HasRoute {
        getRoute as getRedirectRoute;
        setRoute as setRedirectRoute;
    }

    /** @var string */
    public string $name = 'duplicate';

    /** @var string */
    public string $text = 'Duplicate';

    /** @var string */
    public string $primaryKey = 'id';

    /** @var bool */
    public bool $requireConfirmation = true;

    /**
     * @param string|null $name
     */
    public function __construct(string $name = null)
    {
        parent::__construct($name ?? $this->name);
    }

    /**
     * @param $entity
     * @return string
     */
    public function resolveData($entity): string
    {
        return view('datatable::actions.duplicate', [
            'action' => $this,
            'entity' => $entity,
            'primaryKey' => $this->primaryKey
        ])->render();
    }

    /**
     * @param $primaryKey
     * @return $this
     */
    public function setPrimaryKey($primaryKey): self
    {
        $this->primaryKey = $primaryKey;

        return $this;
    }

    /**
     * @param bool $requireConfirmation
     * @return $this
     */
    public function setRequireConfirmation(bool $requireConfirmation): static
    {
        $this->requireConfirmation = $requireConfirmation;

        return $this;
    }

    /**
     * @param $entity
     * @return string
     * @throws \Exception
     */
    public function getRedirectRoute($entity): string
    {
        return $this->getRoute($entity);
    }

    /**
     * @param string $redirectRoute
     * @param array $redirectRouteParameters
     * @return $this
     * @throws \Exception
     */
    public function setRedirectRoute(string $redirectRoute, array $redirectRouteParameters = []): static
    {
        $this->setRoute($redirectRoute, $redirectRouteParameters);

        return $this;
    }
}