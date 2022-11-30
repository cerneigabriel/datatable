<?php

namespace Modules\DataTable\Core\Facades;

use Modules\DataTable\Core\Abstracts\DataTableFactory;
use Modules\DataTable\Core\Actions;

/**
 * Class Action
 *
 * @method static Actions\CustomAction custom(string $name = null)
 * @method static Actions\LinkAction link(string $name)
 * @method static Actions\EditAction edit(string $route, array $routeParameters = [], string $name = null)
 * @method static Actions\DeleteAction delete(string $name = null)
 * @method static Actions\SelectAction select(string $name = null, string $label = null)
 * @method static Actions\DuplicateAction duplicate(string $name = null)
 *
 * @package Modules\DataTable\Core\Facades
 */
class Action extends DataTableFactory
{
    /** @var array|string[] */
    public static array $factory_classes = [
        'custom' => Actions\CustomAction::class,
        'link' => Actions\LinkAction::class,
        'edit' => Actions\EditAction::class,
        'delete' => Actions\DeleteAction::class,
        'select' => Actions\SelectAction::class,
        'duplicate' => Actions\DuplicateAction::class,
    ];
}