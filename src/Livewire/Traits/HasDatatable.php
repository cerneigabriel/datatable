<?php

namespace Modules\DataTable\Livewire\Traits;

use ReflectionClass;
use Illuminate\Support\Facades\Log;
use Modules\DataTable\Core\Abstracts\DataTable;

/**
 * Trait HasDatatable
 *
 * @package Modules\DataTable\Livewire\Traits
 */
trait HasDatatable
{
    use EmitToMultiple;

    /** @var \Modules\DataTable\Core\Abstracts\DataTable */
    public DataTable $datatable;

    /** @var bool */
    public bool $datatableChanged = false;

    /** @var array|string[] */
    protected array $datatableComponents = [
        'datatable::search',
        'datatable::filters',
        'datatable::datatable',
        'datatable::filters.remove',
        'datatable::datatable-top-right-filters',
    ];

    /**
     * Datatable Listeners
     *
     * @return string[]
     */
    public function datatableListeners(): array
    {
        return [
            'datatable:update' => 'updateDatatable',
        ];
    }

    /**
     * Set Query Strings
     *
     * @param array $queryString
     *
     * @return array
     */
    public function setQueryString(array $queryString): array
    {
        if ($this->datatable->hasQueryStrings) {
            $this->queryString = array_merge($this->getQueryString(), $queryString);
        }

        return $this->queryString;
    }

    /**
     * Mount HasDatatable Trait
     *
     * @param \Modules\DataTable\Core\Abstracts\DataTable|null $datatable
     *
     * @return void
     */
    public function mountDatatable(?DataTable $datatable = null): void
    {
        if ($datatable) {
            $this->datatable = $datatable;
        }
    }

    /**
     * Listen to datatable updated event
     *
     * @return void
     */
    public function updatedDatatable(): void
    {
        $this->datatableChanged = true;
    }

    /**
     * Sync Datatable between components
     *
     * @param \Modules\DataTable\Core\Abstracts\DataTable $datatable
     *
     * @return void
     */
    public function updateDatatable($datatable): void
    {
        $this->datatable = DataTable::fromLivewire($datatable);

        if (config('datatable.debug')) {
            $this->dispatchBrowserEvent('datatable-updated');
        }
    }

    /**
     * Sync Datatable Components
     *
     * @param array $ignoreComponents
     *
     * @return void
     */
    public function syncDatatableComponents(array $ignoreComponents = []): void
    {
        collect($this->datatableComponents)
            ->reject(fn ($item) => in_array($item, array_merge(
                $ignoreComponents,
                [$this->renderToView()?->name()]
            ), true))
            ->each(function ($item) {
                $this->emit('datatable:update', $this->datatable->toLivewire())->component($item);
            });
    }

    /**
     * Dehydrate Datatable
     *
     * @param $value
     *
     * @return void
     */
    public function dehydrateDatatable($value)
    {
//        if ($this->datatableChanged) {
//            collect($this->datatableComponents)
//                ->reject(fn ($item) => $item === $this->renderToView()?->name())
//                ->each(function ($item) use ($value) {
//                    $this->emit('datatable:update', $value)->component($item);
//                });
//
//            $this->datatableChanged = false;
//        }
    }
}