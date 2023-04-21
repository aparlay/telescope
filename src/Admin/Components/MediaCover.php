<?php

namespace Aparlay\Core\Admin\Components;

use Closure;
use Illuminate\View\Component;

class MediaCover extends Component
{
    public function __construct(public $media)
    {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|Closure|string
     */
    public function render()
    {
        return view('default_view::admin.components.media-cover');
    }
}
