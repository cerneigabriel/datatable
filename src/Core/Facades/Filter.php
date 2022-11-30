<?php

namespace Modules\DataTable\Core\Facades;

use Modules\DataTable\Core\Filters;
use Modules\DataTable\Core\Abstracts\DataTableFactory;

/**
 * Class Filter
 *
 * @method static Filters\IDFilter ID(?string $name = null, ?string $attribute = null, ?string $label = null)
 * @method static Filters\TextFilter text(string $name, ?string $attribute = null, ?string $label = null)
 * @method static Filters\ToggleFilter toggle(string $name, ?string $attribute = null, ?string $label = null)
 * @method static Filters\DatetimeFilter datetime(string $name, ?string $attribute = null, ?string $label = null)
 * @method static Filters\DateFilter date(string $name, ?string $attribute = null, ?string $label = null)
 * @method static Filters\TimeFilter time(string $name, ?string $attribute = null, ?string $label = null)
 * @method static Filters\SelectFilter select(string $name, ?string $attribute = null, ?string $label = null)
 *
 * @package Modules\DataTable\Core\Facades
 */
class Filter extends DataTableFactory
{
    /** @var array|string[] */
    public static array $factory_classes = [
        'ID' => Filters\IDFilter::class,
        'text' => Filters\TextFilter::class,
        'toggle' => Filters\ToggleFilter::class,
        'datetime' => Filters\DatetimeFilter::class,
        'date' => Filters\DateFilter::class,
        'time' => Filters\TimeFilter::class,
        'select' => Filters\SelectFilter::class,
    ];
}