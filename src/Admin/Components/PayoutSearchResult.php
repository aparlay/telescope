<?php

namespace Aparlay\Core\Admin\Components;

use Aparlay\Payout\Models\UserPayout;
use Illuminate\View\Component;

class PayoutSearchResult extends Component
{
    public function __construct(
        public UserPayout $payout
    ) {
    }

    public function render()
    {
        return view('default_view::admin.components.payout-search-result');
    }
}
