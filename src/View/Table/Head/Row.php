<?php

namespace Modules\DataTable\View\Table\Head;

use Illuminate\View\Component;

/**
 * Class Row
 *
 * @package Modules\DataTable\View\Table\Head
 */
class Row extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('table::components.head.row');
    }
}
