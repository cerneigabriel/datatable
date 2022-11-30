<?php

namespace Modules\DataTable\Core\Actions;

use Closure;
use Exception;
use Modules\DataTable\Core\Abstracts\DataTableAction;
use Modules\DataTable\Core\Actions\Traits\RequireConfirmation;

/**
 * Class DeleteAction
 *
 * @package Modules\DataTable\Core\Actions
 */
class CustomAction extends DataTableAction
{
    use RequireConfirmation;

    /** @var string */
    public string $name = 'custom';

    /** @var string */
    public string $text = 'Custom';

    /** @var string */
    public string $primaryKey = 'id';

    /**
     * @var \Closure|null
     */
    public ?Closure $actionHandler = null;

    /**
     * @param string|null $name
     */
    public function __construct(string $name = null)
    {
        parent::__construct($name ?? $this->name);
    }

    /**
     * @param $entity
     *
     * @return string
     */
    public function resolveData($entity)
    {
        return view('datatable::actions.custom', [
            'action'     => $this,
            'entity'     => $entity,
            'primaryKey' => $this->primaryKey,
        ])->render();
    }

    /**
     * @param $primaryKey
     *
     * @return $this
     */
    public function setPrimaryKey($primaryKey): self
    {
        $this->primaryKey = $primaryKey;

        return $this;
    }

    /**
     * @param \Closure $handler
     *
     * @return $this
     */
    public function setActionHandler(Closure $handler): self
    {
        $this->actionHandler = $handler;

        return $this;
    }

    /**
     * @return \Closure
     * @throws \Exception
     */
    public function getActionHandler(): Closure
    {
        if (is_null($this->actionHandler)) {
            throw new Exception("Handler for {$this->name} is missing.");
        }

        return $this->actionHandler;
    }
}