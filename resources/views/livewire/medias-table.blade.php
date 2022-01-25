@php
    use App\Models\Media;
    use Aparlay\Core\Models\Enums\MediaStatus;
  
@endphp

<div class="medias-table">
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
                    <input class="form-control" type="text" wire:model="filter.username"/>
                </div>
            </td>

            <td class="col-md-2">
                <div> 
                    <label for="">Description</label>
                </div>
            </td>

            <td class="col-md-2">
                <div> 
                    <label for="">Status</label>
                    <select class="form-control" wire:model="filter.status">
                        <option value="">Any</option>
                        @foreach(Media::getStatuses() as $value => $label)
                            <option value="{{$value}}">{{$label}}</option>
                        @endforeach
                    </select>
                </div>
            </td>
            <td class="col-md-2">
                <div> 
                    <label for="">Likes</label>
                    <input class="form-control" type="text" wire:model="filter.like_count"/>
                </div>
            </td>
            <td class="col-md-2">
                <div> 
                    <label for="">Visits</label>
                    <input class="form-control" type="text" wire:model="filter.visit_count"/>
                </div>
            </td>
            <td class="col-md-2">
                <div> 
                    <label for="">Sort Score</label>
                    <input class="form-control" type="text" wire:model="filter.sort_score"/>
                </div>
            </td>
            <td class="col-md-2">
                <div> 
                    <label for="">Created At</label>
                </div>
            </td>
            <td>
            </td>
            
        </tr>

        @foreach($medias as $media)
            <tr>
              
                <td>
                    <img src="{{$media->file}}"></img>
                </td>

                <td>
                    <a href="{{$media->admin_url}}">
                     {{$media->creator['username'] }}
                    </a>
                </td>

                <td>
                     {{$media->description }}
                </td>
                
                <td>
                <span class="badge bg-{{ MediaStatus::from($media->status)->badgeColor() }}">
                        {{ MediaStatus::from($media->status)->label() }}
                    </span>
                </td>

                <td>
                     {{$media->like_count}}
                </td>
                <td>
                     {{$media->visit_count}}
                </td>
                <td>
                     {{$media->sort_score}}
                </td>
                <td>
                     {{$media->created_at}}
                </td>
                <td>
                    <div class="col-md-6">
                        <div>
                            <a class="btn btn-primary btn-sm" href="{{$media->admin_url}}" title="View"><i class="fas fa-eye"></i> View</a>
                        </div>
                    </div>
                </td>
              
               
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $medias->links() }}
</div>
