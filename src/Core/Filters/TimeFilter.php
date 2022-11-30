<?php

namespace Modules\DataTable\Core\Filters;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

/**
 * Class TimeFilter
 *
 * @package Modules\DataTable\Core\Filters
 */
class TimeFilter extends DatetimeFilter
{
    /** @var string */
    public string $type = 'time';

    /** @var string */
    public string $format = 'H:i:S';

    /** @var bool */
    public bool $range = false;
}