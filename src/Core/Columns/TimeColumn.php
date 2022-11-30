<?php

namespace Modules\DataTable\Core\Columns;

/**
 * Class TimeColumn
 *
 * @package Modules\DataTable\Core\Columns
 */
class TimeColumn extends DatetimeColumn
{
    /** @var string */
    public string $type = 'time';

    /** @var string */
    public string $format = 'H:i:s';
}