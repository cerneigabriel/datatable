<?php

namespace Modules\DataTable\Core\Actions;

use Modules\DataTable\Core\Abstracts\DataTableAction;

/**
 * Class DeleteAction
 *
 * @package Modules\DataTable\Core\Actions
 */
class DeleteAction extends DataTableAction
{
    /** @var string */
    public string $name = 'delete';

    /** @var string */
    public string $text = 'Delete';

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
    public function resolveData($entity)
    {
        return view('datatable::actions.delete', [
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
    public function setRequireConfirmation(bool $requireConfirmation)
    {
        $this->requireConfirmation = $requireConfirmation;

        return $this;
    }
}