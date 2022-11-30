<?php

namespace Modules\DataTable\Core\Traits;

use Modules\DataTable\Core\Abstracts\DataTableAction;
use Modules\DataTable\Core\Collections\ActionsCollection;
use Modules\DataTable\Core\Columns\TextColumn;

/**
 * Trait HasActions
 *
 * @package Modules\DataTable\Core\Traits
 */
trait HasActions
{
    /** @var bool */
    public bool $enableActions = true;

    /** @var \Modules\DataTable\Core\Collections\ActionsCollection */
    public ActionsCollection $actions;

    /**
     * @return \Modules\DataTable\Core\Collections\ActionsCollection
     */
    public function actions()
    {
        return $this->actions;
    }

    /**
     * @param \Modules\DataTable\Core\Abstracts\DataTableAction ...$actions
     * @return $this
     */
    public function setActions(DataTableAction ...$actions): static
    {
        $this->actions = ActionsCollection::make($actions);

        $this->checkActionsColumn();

        return $this;
    }

    /**
     * @return void
     */
    private function checkActionsColumn(): void
    {
        if ($this->hasActions() && !$this->columns()->get('actions')) {
            $this->columns()->add(
                (new TextColumn('actions'))
                    ->setLabel('')
                    ->setSortable(false)
                    ->setFilterable(false)
                    ->setMinWidth('100px')
                    ->setMaxWidth('250px')
            );
        }
    }

    /**
     * @return bool
     */
    public function hasActions(): bool
    {
        return $this->enableActions && $this->actions->isNotEmpty();
    }

    /**
     * @param \Modules\DataTable\Core\Abstracts\DataTableAction ...$actions
     * @return $this
     */
    public function addActions(DataTableAction ...$actions): static
    {
        $this->actions->push(...$actions);

        $this->checkActionsColumn();

        return $this;
    }
}