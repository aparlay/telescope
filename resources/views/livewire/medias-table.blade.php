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
            <th class="col-md-2">
            <div>
                    <label
                    @class([
                        'col sort-col',
                            'sort-asc' => Arr::get($sort, 'file') === 1,
                            'sort-desc' => Arr::get($sort, 'file') === -1])
                        wire:model="sort.file"
                        wire:click="sort('file')">

                        Cover

                    </label>
                </div>
            </th>
            <td class="col-md-2">
                <div> 
                <label
                    @class([
                        'col sort-col',
                            'sort-asc' => Arr::get($sort, 'created_by') === 1,
                            'sort-desc' => Arr::get($sort, 'created_by') === -1])
                        wire:model="sort.created_by"
                        wire:click="sort('created_by')">

                        Created By

                    </label>
                    <input class="form-control" type="text" wire:model="filter.username"/>
                </div>  
            </td>

            <td class="col-md-2">
                <div> 
                <label
                    @class([
                        'col sort-col',
                            'sort-asc' => Arr::get($sort, 'description') === 1,
                            'sort-desc' => Arr::get($sort, 'description') === -1])
                        wire:model="sort.description"
                        wire:click="sort('description')">

                        Description

                    </label>
                </div>
            </td>

            <td class="col-md-2">
                <div> 
                <label
                    @class([
                        'col sort-col',
                            'sort-asc' => Arr::get($sort, 'status') === 1,
                            'sort-desc' => Arr::get($sort, 'status') === -1])
                        wire:model="sort.status"
                        wire:click="sort('status')">

                        Status

                    </label>
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
                <label
                    @class([
                        'col sort-col',
                            'sort-asc' => Arr::get($sort, 'like_count') === 1,
                            'sort-desc' => Arr::get($sort, 'like_count') === -1])
                        wire:model="sort.like_count"
                        wire:click="sort('like_count')">

                        Likes

                    </label>
                    <input class="form-control" type="text" wire:model="filter.like_count"/>
                </div>
            </td>
            <td class="col-md-2">
                <div> 
                <label
                    @class([
                        'col sort-col',
                            'sort-asc' => Arr::get($sort, 'visit_count') === 1,
                            'sort-desc' => Arr::get($sort, 'visit_count') === -1])
                        wire:model="sort.visit_count"
                        wire:click="sort('visit_count')">

                        Visits

                    </label>
                    <input class="form-control" type="text" wire:model="filter.visit_count"/>
                </div>
            </td>
            <td class="col-md-2">
                <div> 
                <label
                @class([
                        'col sort-col',
                            'sort-asc' => Arr::get($sort, 'sort_score') === 1,
                            'sort-desc' => Arr::get($sort, 'sort_score') === -1])
                        wire:model="sort.sort_score"
                        wire:click="sort('sort_score')">

                        Sort Scores

                    </label>
                    <input class="form-control" type="text" wire:model="filter.sort_score"/>
                </div>
            </td>
            <td class="col-md-2">
                <div> 
                <label
                @class([
                        'col sort-col',
                            'sort-asc' => Arr::get($sort, 'created_at') === 1,
                            'sort-desc' => Arr::get($sort, 'created_at') === -1])
                        wire:model="sort.created_at"
                        wire:click="sort('created_at')">

                        Created At

                    </label>
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
