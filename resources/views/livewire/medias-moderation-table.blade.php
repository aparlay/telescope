@php
    use App\Models\Media;
    use Aparlay\Core\Models\Enums\MediaStatus;

@endphp

<div class="medias-table">
    <div class="filters pb-3">
        <div class="row">
            <div class="col-md-6 pt-4">
                <h4>Medias Moderation</h4>
            </div>
            <div class="col-md-2">
                <label for="">Start Date</label>
                <x-date-picker
                        wire:model.lazy="filter.created_at.start"
                        autocomplete="off"
                        placeholder="Start"
                />
            </div>
            <div class="col-md-2">
                <label for="">End Date</label>
                <x-date-picker
                        wire:model.lazy="filter.created_at.end"
                        autocomplete="off"
                        placeholder="End"
                />
            </div>
            <div class="col-md-2 ml-auto">
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

            <td class="col-md-3">
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'description'" :fieldLabel="'Description'" />
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
                    {{$media->description }}
                </td>
                <td>
                    {{$media->like_count}}
                </td>
                <td>
                    {{$media->visit_count}}
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
    <div class="d-flex justify-content-center">
        {{ $medias->links() }}
    </div>
</div>
