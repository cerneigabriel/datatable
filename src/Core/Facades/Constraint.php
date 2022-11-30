<?php

namespace Modules\DataTable\Core\Facades;

use Modules\DataTable\Core\Abstracts\DataTableFactory;
use Modules\DataTable\Core\Constraints;

/**
 * @method static Constraints\TextColumnConstraint text(string $name, string $attribute, string $label)
 * @method static Constraints\DatetimeColumnConstraint datetime(string $name, string $attribute, string $label)
 * @method static Constraints\DateColumnConstraint date(string $name, string $attribute, string $label)
 * @method static Constraints\TimeColumnConstraint time(string $name, string $attribute, string $label)
 * @method static Constraints\ListColumnConstraint list(string $name, string $attribute, string $label)
 * @method static Constraints\BooleanColumnConstraint boolean(string $name, string $attribute, string $label)
 * @method static Constraints\CountColumnConstraint count(string $name, string $attribute, string $label)
 *
 * @package Modules\DataTable\Core\Facades
 */
class Constraint extends DataTableFactory
{
    /** @var array|string[] */
    public static array $factory_classes = [
        'text' => Constraints\TextColumnConstraint::class,
        'datetime' => Constraints\DatetimeColumnConstraint::class,
        'date' => Constraints\DateColumnConstraint::class,
        'time' => Constraints\TimeColumnConstraint::class,
        'list' => Constraints\ListColumnConstraint::class,
        'boolean' => Constraints\BooleanColumnConstraint::class,
        'count' => Constraints\CountColumnConstraint::class,
    ];
}