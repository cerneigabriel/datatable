<?php

namespace Modules\DataTable\View\Table\Body;

use Illuminate\View\Component;

/**
 * Class Cell
 *
 * @package Modules\DataTable\View\Table\Body
 */
class Cell extends Component
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
        return view('table::components.body.cell');
    }
}
