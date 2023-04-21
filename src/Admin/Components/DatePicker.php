<?php

namespace Aparlay\Core\Admin\Components;

use Closure;
use Illuminate\View\Component;

class DatePicker extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|Closure|string
     */
    public function render()
    {
        return view('default_view::admin.components.date-picker');
    }
}
