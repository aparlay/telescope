@php
    use App\Models\User;
    use Aparlay\Core\Models\Enums\UserGender;
    use Aparlay\Core\Models\Enums\UserStatus;
    use Aparlay\Core\Models\Enums\UserVerificationStatus;
@endphp

<div class="user-table">
    <div class="filters pb-3">
        <div class="row">
            <div class="col-md-3">
                <label for="">Text Search</label>
                <input class="form-control" type="text" wire:model="filter.text_search"/>
            </div>


            <div class="col-md-1  ml-auto">
                <label for="">Per Page</label>
                <select class="form-control" wire:model="perPage">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                </select>
            </div>

        </div>
    </div>

    <table class="table table-striped">
        <tbody>
        <tr>
            <td class="col-md-2">
                <div> 
                    <label for="">Cover</label>
                </div>
            </td>
            <td class="col-md-2">
                <div>
                    <label for="">Created By</label>
                    <input class="form-control" type="text" wire:model="filter.email"/>
                </div>
            </td>
            <td class="col-md-2">
                <div>
                    <label for="">Description</label>
                   
                </div>
            </td>
            <td class="col-md-1">
                <div>
                    <label for="">Status</label>
                    <select class="form-control" wire:model="filter.status">
                        <option value="">Any</option>
                        @foreach(User::getGenders() as $value => $label)
                            <option value="{{$value}}">{{$label}}</option>
                        @endforeach
                    </select>
                </div>
            </td>
            <td class="col-md-1">
                <div>
                    <label for="">Likes</label>
                    <select class="form-control" wire:model="filter.status">
                        <option value="">Any</option>
                        @foreach(User::getStatuses() as $value => $label)
                            <option value="{{$value}}">{{$label}}</option>
                        @endforeach
                    </select>
                </div>
            </td>
            <td class="col-md-1">
                <div>
                    <label for="">Visits</label>
                   
                </div>
            </td>
            <td class="col-md-1">
                <div>
                    <label for="">Sort Score</label>
                   
                </div>
            </td>
            <td class="col-md-2">
                <div>
                    <label for="">Created at</label>
                </div>
            </td>
            <td></td>
        </tr>

        @foreach($medias as $media)
            <tr>
                <td>
                    <a href="{{$media->admin_url}}">
                    <img src="{{ $media->avatar }}?aspect_ratio=1:1&width=150" alt="" class="img-circle img-size-50 mr-2">
                     {{$media->username }}
                    </a>
                    
                </td>
                <td>
                    <a href="{{$media->admin_url}}">{{ $media->created_by }}</a>
                </td>
                <td>
                    <a href="{{$media->admin_url}}">{{ $media->description }}</a>
                </td>
                <td>
                    <a href="{{$media->admin_url}}">{{ $media->status }}</a>
                </td>
                <td>
                    <a href="{{$media->admin_url}}">{{ $media->likes }}</a>
                </td>
                <td>
                    <a href="{{$media->admin_url}}">{{ $media->sort_score }}</a>
                </td>
                <td>
                    <a href="{{$media->admin_url}}">{{ $media->created_at }}</a>
                </td>
               
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $users->links() }}
</div>
