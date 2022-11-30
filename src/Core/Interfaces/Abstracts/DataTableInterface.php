<?php

namespace Modules\DataTable\Core\Interfaces\Abstracts;

use App\Models\Billing;
use App\Models\Order;
use Closure;
use Modules\DataTable\Core\Abstracts\DataTable;

/**
 * Class DataTable
 *
 * @package Modules\DataTable\Core
 */
interface DataTableInterface
{
    /**
     * @param \Closure $customQuery
     * @return $this
     */
    public function setQuery(Closure $customQuery): \Modules\DataTable\Core\Abstracts\DataTable;
}
