<?php

namespace Modules\DataTable\Core\Interfaces\Abstracts;


use Closure;
use Modules\DataTable\Core\Abstracts\DataTableAction;

/**
 * Class DataTableAction
 *
 * @package Modules\DataTable\Core\Abstracts
 */
interface DataTableActionInterface
{
    /**
     * @param $entity
     * @return null
     */
    public function render($entity);
}