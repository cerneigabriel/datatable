<?php

namespace Modules\DataTable\Core\Traits;

use Modules\DataTable\Core\Abstracts\DataTableConstraint;
use Modules\DataTable\Core\Collections\ConstraintsCollection;

/**
 * @package Modules\DataTable\Core\Traits
 */
trait HasConstraints
{
    /** @var bool */
    public bool $enableConstraints = false;

    /** @var \Modules\DataTable\Core\Abstracts\DataTableConstraint|null */
    public ?DataTableConstraint $constraint = null;

    /** @var \Modules\DataTable\Core\Collections\ConstraintsCollection */
    public ConstraintsCollection $constraints;

    /**
     * @return bool
     */
    public function hasConstraints(): bool
    {
        return $this->enableConstraints && $this->constraints()->isNotEmpty();
    }

    /**
     * @return bool
     */
    public function hasVisibleConstraints(): bool
    {
        return $this->enableConstraints && $this->constraints()->visible()->isNotEmpty();
    }

    /**
     * @return ConstraintsCollection
     */
    public function constraints(): ConstraintsCollection
    {
        return $this->constraints;
    }

    /**
     * @param \Modules\DataTable\Core\Abstracts\DataTableConstraint ...$constraints
     *
     * @return \Modules\DataTable\Core\Traits\HasConstraints|\Modules\DataTable\Core\Abstracts\DataTable
     */
    public function setConstraints(DataTableConstraint ...$constraints): self
    {
        $this->constraints = ConstraintsCollection::make($constraints)->unique('name');

        return $this;
    }

    /**
     * @return array
     */
    public function constraintsOptions(): array
    {
        return $this->constraints()->visible()->pluck('label', 'name')->toArray();
    }

    /**
     * @param string $constraint
     *
     * @return \Modules\DataTable\Core\Traits\HasConstraints|\Modules\DataTable\Core\Abstracts\DataTable
     */
    public function setConstraint(string $constraint): self
    {
        $this->constraint = $this->constraints()->where('name', $constraint)->first();

        return $this;
    }
}