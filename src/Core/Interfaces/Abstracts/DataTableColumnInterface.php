<?php

namespace Modules\DataTable\Core\Interfaces\Abstracts;


/**
 * Class DataTableColumn
 *
 * @package Modules\DataTable\Core\Abstracts
 */
interface DataTableColumnInterface
{
    /**
     * @param $entity
     * @return null
     */
    public function render($entity);
}