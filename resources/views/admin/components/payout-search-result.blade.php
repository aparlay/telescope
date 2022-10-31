@if($payout)
    <div class="float-left pl-3 py-1 result-item">
        <a href="{{$payout->admin_url}}">
            <img src="{{$payout->creatorObj->avatar}}?aspect_ratio=1:1&width=150" alt="" class="img-circle img-size-32 mr-2">
            {{$payout->_id}}
            <i class="fa fa-file-invoice-dollar text-gray"></i>
        </a>
    </div>
@endif
