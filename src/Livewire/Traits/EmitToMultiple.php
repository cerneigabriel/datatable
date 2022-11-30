<?php

namespace Modules\DataTable\Livewire\Traits;

trait EmitToMultiple
{
    /**
     * @param string|array $components
     * @param string            $event
     * @param                   ...$arguments
     * @return void
     */
    public function emitToMultiple(string|array $components = [], string $event = 'refresh', ...$arguments): void
    {
        if (is_string($components)) {
            $this->emit($event, ...$arguments)->component($components);
        } else {
            foreach ($components as $filterComponent) {
                $this->emit($event, ...$arguments)->component($filterComponent);
            }
        }
    }
}