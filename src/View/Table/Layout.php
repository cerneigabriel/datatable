<?php

namespace Modules\DataTable\View\Table;

use Illuminate\View\Component;

/**
 * Class Layout
 *
 * @package Modules\DataTable\View\Table
 */
class Layout extends Component
{
    /** @var string|null */
    public ?string $title;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(string $title = null)
    {
        $this->title = $title;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('table::components.layout');
    }
}
