<?php

namespace Aparlay\Core\Admin\Components;

use Aparlay\Chat\Admin\Models\Chat;
use Illuminate\View\Component;

class ChatSearchResult extends Component
{
    public function __construct(
        public Chat $chat
    ) {
    }

    public function render()
    {
        return view('default_view::admin.components.chat-search-result');
    }
}
