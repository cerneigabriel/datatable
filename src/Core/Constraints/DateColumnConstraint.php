<?php

namespace Modules\DataTable\Core\Constraints;

/**
 * @package Modules\DataTable\Core\Filters
 */
class DateColumnConstraint extends DatetimeColumnConstraint
{
    /** @var string */
    public string $type = 'date';
}