<?php

namespace Modules\DataTable\Core\Actions\Traits;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Compilers\BladeCompiler;

/**
 * Trait RequireConfirmation
 *
 * @package Modules\DataTable\Core\Actions\Traits
 */
trait RequireConfirmation
{
    /** @var string */
    public string $modalTitle = '';

    /** @var \Closure|null */
    public ?Closure $modalTitleResolver = null;

    /** @var string */
    public string $modalText = '';

    /** @var \Closure|null */
    public ?Closure $modalTextResolver = null;

    /** @var bool */
    public bool $requireConfirmation = true;

    /** @var \Closure|null */
    public ?Closure $requireConfirmationResolver = null;

    /** @var array */
    public array $modalActions = [];

    /** @var \Closure|null */
    public ?Closure $modalActionsResolver = null;

    /** @var bool */
    public bool $overrideDefaultModalActions = false;

    /**
     * @param \Illuminate\Database\Eloquent\Model $entity
     *
     * @return array
     */
    public function getModalActions(Model $entity): array
    {
        $defaultModalActions = [
            [
                'text' => 'Cancel',
                'classes' => 'link-secondary',
                'close_modal_onclick' => true,
            ],
            [
                'close_modal_onclick' => true,
                'text' => 'Yes',
                'attributes' => [
                    'wire:click' => 'customAction(' . $entity->{$this->primaryKey} . ', \'' . $this->name . '\', true)'
                ]
            ]
        ];

        if (!is_null($this->modalActionsResolver)) {
            $modalActions = $this->modalActionsResolver->call($this, $entity);
        }

        return array_merge($defaultModalActions, $modalActions ?? $this->modalActions);
    }

    /**
     * @param array|\Closure $value
     * @param bool $overrideDefaultModalActions
     * @return $this
     */
    public function setModalActions(array|Closure $value, bool $overrideDefaultModalActions = false): static
    {
        if ($value instanceof Closure) {
            $this->modalActionsResolver = $value;
        } else {
            $this->modalActions = $value;
        }

        $this->overrideDefaultModalActions = $overrideDefaultModalActions;

        return $this;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $entity
     *
     * @return bool
     */
    public function getRequireConfirmation(Model $entity): bool
    {
        if (!is_null($this->requireConfirmationResolver)) {
            return $this->requireConfirmationResolver->call($this, $entity);
        }

        return $this->requireConfirmation;
    }

    /**
     * @param bool|\Closure $value
     *
     * @return $this
     */
    public function setRequireConfirmation(bool|Closure $value): static
    {
        if ($value instanceof Closure) {
            $this->requireConfirmationResolver = $value;
        } else {
            $this->requireConfirmation = $value;
        }

        return $this;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $entity
     *
     * @return string
     */
    public function getModalTitle(Model $entity): string
    {
        if (!is_null($this->modalTitleResolver)) {
            return $this->modalTitleResolver->call($this, $entity);
        }

        return $this->modalTitle;
    }

    /**
     * @param string|\Closure $value
     *
     * @return $this
     */
    public function setModalTitle(string|Closure $value): static
    {
        if ($value instanceof Closure) {
            $this->modalTitleResolver = $value;
        } else {
            $this->modalTitle = $value;
        }

        return $this;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $entity
     *
     * @return string
     */
    public function getModalText(Model $entity): string
    {
        if (!is_null($this->modalTextResolver)) {
            return BladeCompiler::render($this->modalTextResolver->call($this, $entity), ['entity' => $entity, 'action' => $this]);
        }

        return $this->modalText;
    }

    /**
     * @param string|\Closure $value
     *
     * @return $this
     */
    public function setModalText(string|Closure $value): static
    {
        if ($value instanceof Closure) {
            $this->modalTextResolver = $value;
        } else {
            $this->modalText = $value;
        }

        return $this;
    }
}