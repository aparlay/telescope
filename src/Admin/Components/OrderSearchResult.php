<?php

namespace Aparlay\Core\Admin\Components;

use Aparlay\Payment\Models\Order;
use Illuminate\View\Component;

class OrderSearchResult extends Component
{
    public function __construct(
        public Order $order
    ) {
    }

    public function render()
    {
        return view('default_view::admin.components.order-search-result');
    }
}
