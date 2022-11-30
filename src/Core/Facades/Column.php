<?php

namespace Modules\DataTable\Core\Facades;

use Modules\DataTable\Core\Columns;
use Modules\DataTable\Core\Abstracts\DataTableFactory;

/**
 * Class Column
 *
 * @method static Columns\IDColumn ID(?string $name = null, ?string $attribute = null, ?string $label = null)
 * @method static Columns\TextColumn text(string $name, ?string $attribute = null, ?string $label = null)
 * @method static Columns\EmailColumn email(string $name, ?string $attribute = null, ?string $label = null)
 * @method static Columns\BooleanColumn boolean(string $name, ?string $attribute = null, ?string $label = null)
 * @method static Columns\DatetimeColumn datetime(string $name, ?string $attribute = null, ?string $label = null)
 * @method static Columns\DateColumn date(string $name, ?string $attribute = null, ?string $label = null)
 * @method static Columns\TimeColumn time(string $name, ?string $attribute = null, ?string $label = null)
 * @method static Columns\ListColumn list(string $name, ?string $attribute = null, ?string $label = null)
 * @method static Columns\CountColumn count(string $name, ?string $attribute = null, ?string $label = null)
 * @method static Columns\PriceColumn price(string $name, ?string $attribute = null, ?string $label = null)
 *
 * @package Modules\DataTable\Core\Facades
 */
class Column extends DataTableFactory
{
    /** @var array|string[] */
    public static array $factory_classes = [
        'ID' => Columns\IDColumn::class,
        'text' => Columns\TextColumn::class,
        'email' => Columns\EmailColumn::class,
        'boolean' => Columns\BooleanColumn::class,
        'datetime' => Columns\DatetimeColumn::class,
        'date' => Columns\DateColumn::class,
        'time' => Columns\TimeColumn::class,
        'list' => Columns\ListColumn::class,
        'count' => Columns\CountColumn::class,
        'price' => Columns\PriceColumn::class,
    ];
}