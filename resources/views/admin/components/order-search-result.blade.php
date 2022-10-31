@if($order)
    <div class="float-left pl-3 py-1 result-item">
        <a href="{{$order->admin_url}}">
            <img src="{{$order->creatorObj->avatar}}?aspect_ratio=1:1&width=150" alt="" class="img-circle img-size-32 mr-2">
            {{$order->_id}}
            <i class="fa fa-money-bill-wave text-gray"></i>
        </a>
    </div>
@endif
