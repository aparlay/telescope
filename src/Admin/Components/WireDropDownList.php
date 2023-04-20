<?php

namespace Aparlay\Core\Admin\Components;

use Closure;
use Illuminate\View\Component;

class WireDropDownList extends Component
{
    public function __construct(
        public string $wireModel,
        public array $options,
        public bool $showAny = true
    ) {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|Closure|string
     */
    public function render()
    {
        return view('default_view::admin.components.wire-dropdown-list');
    }
}
