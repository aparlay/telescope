<?php

namespace Aparlay\Core\Admin\Components;

use Illuminate\View\Component;

class WireTableFilterDropdown extends Component
{

    public function __construct(
        public string $wireModel,
        public array $options,
    ) {
    }


    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('default_view::admin.components.wire-filter-dropdown');
    }
}
