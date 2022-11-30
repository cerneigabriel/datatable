<?php

namespace Modules\DataTable\Livewire\Traits;

use Modules\DataTable\Core\Abstracts\DataTableFilter;
use Modules\DataTable\Core\Collections\FiltersCollection;

/**
 * Trait HasFilters
 *
 * @package Modules\DataTable\Livewire\Traits
 */
trait HasFilters
{
    use SyncWithDatatable;

    /** @var array */
    public array $filters = [];

    /**
     * @return void
     */
    public function updated(string $property)
    {
        if (str_contains($property, 'filters')) {
            $exploded_property = collect(explode('.', $property));
//            $this->emitTo(
//                'datatable::datatable',
//                'setFilters',
//                [
//                    $exploded_property->skip(1)->first() => $this->getPropertyValue($exploded_property->take(2)->implode('.')),
//                ],
//                $this->datatable->id
//            );
//            $this->bootFilters();

//            dump([
//                $exploded_property->skip(1)->first() => $this->getPropertyValue($exploded_property->take(2)->implode('.'))
//            ]);

            $this->datatable->filters()->setValues([
                $exploded_property->skip(1)->first() => $this->getPropertyValue($exploded_property->take(2)->implode('.')),
            ]);
            $this->syncDatatableComponents();
        }
    }

    /**
     * @return void
     */
    protected function bootFilters(): void
    {
//        $this->syncWithDataTable('queryStringFilters');
//        $this->syncWithDataTable('filters');
    }

    /**
     * @param string $datatableId
     *
     * @return void
     */
    public function resetFilters(string $datatableId)
    {
        if ($datatableId === $this->datatable->id) {
            $this->removeFilters(array_keys($this->filters));
        }
    }

    /**
     * @param array $keys
     * @return void
     */
    public function removeFilters(array $keys)
    {
        foreach ($keys as $key) {
            $this->removeFilter($key, false);
        }

        $this->emitTo('datatable::datatable', 'setFilters', $this->filters, $this->datatable->id);
        $this->bootFilters();
    }

    /**
     * @param string $key
     * @param bool $bootFilters
     * @return void
     */
    public function removeFilter(string $key, bool $bootFilters = true)
    {
        $keys = explode('.', $key);

        $arr = &$this->filters;
        while (count($keys)) {
            # Get a reference to the inner element
            $arr = &$arr[array_shift($keys)];

            # Remove the most inner key
            if (count($keys) === 1) {
                unset($arr[$keys[0]]);
                break;
            }
        }

        if ($bootFilters) {
            $this->emitTo('datatable::datatable', 'setFilters', $this->filters, $this->datatable->id);
            $this->bootFilters();
        }
    }

    /**
     * @return array|string[]
     */
    protected function filtersListeners()
    {
        return [
            'resetFilters',
        ];
    }

    /**
     * @return void
     */
    protected function mountFilters()
    {
        dump($this->getQueryString());
        if (!empty($this->getQueryString()) && $this->queryStringFiltersHasValues()) {
            $this->filters = $this->getGroupFilters()
                ->setValues($this->filters)
                ->getValues()
                ->toArray();
        } else {
            $this->filters = $this->getGroupFilters()
                ->getValues()
                ->toArray();
        }
    }

    public function getGroupFilters(): FiltersCollection
    {
        return $this->datatable->filters()->whereGroup($this->filtersGroup ?? null);
    }

    public function queryStringFiltersHasValues()
    {
        return $this->filters !== $this->getGroupFilters()->getValues()->toArray();
    }
}