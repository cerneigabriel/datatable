<?php

namespace Modules\DataTable;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\DataTable\Commands\MakeDataTable;
use Modules\DataTable\Commands\MakeDataTableFilter;
use Modules\DataTable\Livewire as DataTableComponents;

/**
 * Class DataTableServiceProvider
 *
 * @package Modules\DataTable
 */
class DataTableServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/config/datatable.php', 'datatable');

        $this->commands([
            MakeDataTable::class,
            MakeDataTableFilter::class,
        ]);

        $this->loadViewsFrom(__DIR__.'/views/datatable', 'datatable');
        $this->loadViewsFrom(__DIR__.'/views/table', 'table');
        $this->loadViewsFrom(resource_path('views/vendor/datatable'), 'datatable');
        $this->loadViewsFrom(resource_path('views/vendor/table'), 'table');
    }

    /**
     * @return void
     */
    public function register()
    {
        Livewire::component('datatable::datatable', DataTableComponents\Datatable::class);
        Livewire::component('datatable::search', DataTableComponents\Search::class);
        Livewire::component('datatable::filters', DataTableComponents\Filters::class);
        Livewire::component('datatable::notifications', DataTableComponents\Notifications::class);
        Livewire::component('datatable::datatable-top-right-filters', DataTableComponents\DatatableTopRightFilters::class);
        Livewire::component('datatable::filters.remove', DataTableComponents\FiltersRemove::class);

        Blade::componentNamespace('Modules\\DataTable\\View\\DataTable', 'datatable');
        Blade::componentNamespace('Modules\\DataTable\\View\\Table', 'table');
    }
}