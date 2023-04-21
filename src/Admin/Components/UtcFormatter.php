<?php

namespace Aparlay\Core\Admin\Components;

use Closure;
use Illuminate\View\Component;
use MongoDB\BSON\UTCDateTime;

class UtcFormatter extends Component
{
    public function __construct(public UTCDateTime $date, public string $format)
    {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|Closure|string
     */
    public function render()
    {
        return view('default_view::admin.components.utc-formatter');
    }
}
