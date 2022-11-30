<?php

namespace Modules\DataTable\Core\Filters;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

/**
 * Class DateColumn
 *
 * @package Modules\DataTable\Core\Filters
 */
class DateFilter extends DatetimeFilter
{
    /** @var string */
    public string $type = 'date';

    /** @var string */
    public string $format = 'm/d/Y';

    /** @var bool */
    public bool $range = false;
}