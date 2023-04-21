<?php

namespace Aparlay\Core\Admin\Components;

use Closure;
use Illuminate\View\Component;

class SortableColumnHeader extends Component
{
    public function __construct(
        public string $fieldName,
        public string $fieldLabel,
        public array $sort
    ) {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|Closure|string
     */
    public function render()
    {
        return view('default_view::admin.components.sortable-column-header');
    }
}
