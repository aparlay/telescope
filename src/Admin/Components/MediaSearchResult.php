<?php

namespace Aparlay\Core\Admin\Components;

use Aparlay\Core\Admin\Models\Media;
use Illuminate\View\Component;

class MediaSearchResult extends Component
{
    public function __construct(public Media $media)
    {
    }

    public function render()
    {
        return view('default_view::admin.components.media-search-result');
    }
}
