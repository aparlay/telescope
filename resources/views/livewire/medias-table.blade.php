@php
    use App\Models\Media;
    use Aparlay\Core\Models\Enums\MediaStatus;
  
@endphp

<div class="medias-table">
    <div class="filters pb-3">
        <div class="row">
            <div class="col-md-3 pt-4">
                <h4>Medias</h4>
            </div>

            <div class="col-md-2 offset-6">
                <div class="row">
                    <div class="col">
                        <label for="">Start Date</label>
                        <x-date-picker
                            wire:model.lazy="filter.created_at.start"
                            autocomplete="off"
                            placeholder="Start"
                        />
                    </div>
                    <div class="col">
                        <label for="">End Date</label>
                        <x-date-picker
                            wire:model.lazy="filter.created_at.end"
                            autocomplete="off"
                            placeholder="End"
                        />
                    </div>
                </div>
            </div>


            <div class="col-md-1  ml-auto">
                <label for="">Per Page</label>
                <x-wire-dropdown-list :wire-model="'perPage'" :show-any="false" :options="[5 => 5, 10 => 10, 15 => 15]"/>
            </div>

        </div>
    </div>

    <table class="table table-striped">
        <tbody>
        <tr>
            <th class="col-md-2">
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'file'" :fieldLabel="'Cover'" />
                </div>
                </th>
            <td class="col-md-2">
                <div> 
                    <x-sortable-column-header :sort="$sort" :fieldName="'creator.username'" :fieldLabel="'Creator'" />
                    <input class="form-control" type="text" wire:model="filter.creator_username"/>
                </div>  
            </td>

            <td class="col-md-1">
                <div> 
                <x-sortable-column-header :sort="$sort" :fieldName="'status'" :fieldLabel="'Status'" />
                    <select class="form-control" wire:model="filter.status">
                        <option value="">Any</option>
                        @foreach(Media::getStatuses() as $value => $label)
                            <option value="{{$value}}">{{$label}}</option>
                        @endforeach
                    </select>
                </div>
            </td>
            <td class="col-md-1">
                <div> 
                    <x-sortable-column-header :sort="$sort" :fieldName="'like_count'" :fieldLabel="'Likes'" />
                    <input class="form-control" type="text" wire:model="filter.like_count"/>
                </div>
            </td>
            <td class="col-md-1">
                <div> 
                    <x-sortable-column-header :sort="$sort" :fieldName="'visit_count'" :fieldLabel="'Visits'" />
                    <input class="form-control" type="text" wire:model="filter.visit_count"/>
                </div>
            </td>
            <td class="col-md-1">
                <div> 
                    <x-sortable-column-header :sort="$sort" :fieldName="'sort_score'" :fieldLabel="'Scores'" />
                    <input class="form-control" type="text" wire:model="filter.sort_score"/>
                </div>
            </td>
            <td class="col-md-2">
                <div> 
                    <x-sortable-column-header :sort="$sort" :fieldName="'created_at'" :fieldLabel="'Created At'" />
                </div>
            </td>
            <td class="col-md-1">
                <div>
                    <label for="">Details</label>
                </div>
            </td>
            
        </tr>

        @foreach($medias as $media)
            <tr>
                <td>
                    <x-media-cover :media="$media"/>
                </td>
                <td>
                    <x-username-avatar :user="$media->creatorObj"/>
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
                            <a class="btn btn-primary btn-sm" href="{{$media->admin_url}}" title="View"><i class="fas fa-eye"></i></a>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $medias->links() }}
</div>
