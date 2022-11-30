<?php

namespace Modules\DataTable\View\DataTable;

use Illuminate\View\Component;
use Modules\DataTable\Core\Abstracts\DataTable;

/**
 * Class Layout
 *
 * @package Modules\DataTable\View\DataTable
 */
class Layout extends Component
{
    /** @var \Modules\DataTable\Core\Abstracts\DataTable */
    public DataTable $datatable;

    /** @var string|null */
    public ?string $title;

    /** @var bool */
    public bool $showRemoveFiltersButton;

    /** @var string|null */
    public ?string $mobile_breakpoint;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(DataTable $datatable, string $title = null, bool $showRemoveFiltersButton = true, string $mobileBreakpoint = null)
    {
        $this->datatable = $datatable;
        $this->title = $title;
        $this->showRemoveFiltersButton = $showRemoveFiltersButton;
        $this->mobile_breakpoint = $mobileBreakpoint;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('datatable::components.layout');
    }
}
