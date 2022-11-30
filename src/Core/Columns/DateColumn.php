<?php

namespace Modules\DataTable\Core\Columns;

/**
 * Class DateColumn
 *
 * @package Modules\DataTable\Core\Columns
 */
class DateColumn extends DatetimeColumn
{
    /** @var string */
    public string $type = 'date';

    /** @var string */
    public string $format = 'Y-m-d';
}