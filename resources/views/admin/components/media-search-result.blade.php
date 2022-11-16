@if($media)
    <div class="float-left pl-3 py-1 result-item">
        <a href="{{$media->admin_url}}">
            <img src="{{$media->cover_url}}?aspect_ratio=1:1&width=100" alt="" class="img-size-32 mr-2">
            {{$media->_id}}
            <i class="fas fa-film ml-1 far text-gray"></i>
        </a>
    </div>
@endif
