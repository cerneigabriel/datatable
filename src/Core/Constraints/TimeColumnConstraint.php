<?php

namespace Modules\DataTable\Core\Constraints;

/**
 * @package Modules\DataTable\Core\Filters
 */
class TimeColumnConstraint extends DatetimeColumnConstraint
{
    /** @var string */
    public string $type = 'time';
}