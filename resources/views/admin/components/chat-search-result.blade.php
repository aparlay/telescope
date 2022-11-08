@if($chat)
    <div class="float-left pl-3 py-1 result-item">
        <a href="{{$chat->admin_url}}">
            <img src="{{$chat->creatorObj->avatar}}?aspect_ratio=1:1&width=150" alt="" class="img-circle img-size-32 mr-2">
            {{$chat->_id}}
            <i class="fas fa-comments ml-1 far text-gray"></i>
        </a>
    </div>
@endif
