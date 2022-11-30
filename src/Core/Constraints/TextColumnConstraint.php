<?php

namespace Modules\DataTable\Core\Constraints;

use Modules\DataTable\Core\Abstracts\DataTableConstraint;

/**
 * @package Modules\DataTable\Core\Filters
 */
class TextColumnConstraint extends DataTableConstraint
{
    /** @var string */
    public string $type = 'text';
}