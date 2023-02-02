@php
    use App\Models\Media;
    use Aparlay\Core\Models\Enums\MediaStatus;
@endphp

<div class="medias-table user-profile-table mt-2">
    <table class="table table-striped">
        <tbody>
            <tr>
                <th class="col-md-1">
                    <x-sortable-column-header :sort="$sort" :fieldName="'file'" :fieldLabel="'Cover'" />
                </th>

                <th @class(['col-md-3', 'd-none' => $hiddenFields['creator_username']])>
                    <x-sortable-column-header :sort="$sort" :fieldName="'creator.username'" :fieldLabel="'Creator'" />
                </th>

                <th class="col-md-2">
                    <x-sortable-column-header :sort="$sort" :fieldName="'status'" :fieldLabel="'Status'" />
                </th>

                <th class="col-md-1">
                    <x-sortable-column-header :sort="$sort" :fieldName="'like_count'" :fieldLabel="'Likes'" />
                </th>

                <th class="col-md-1">
                    <x-sortable-column-header :sort="$sort" :fieldName="'visit_count'" :fieldLabel="'Visits'" />
                </th>

                <th class="col-md-2">
                    <x-sortable-column-header :sort="$sort" :fieldName="'created_at'" :fieldLabel="'Created At'" />
                </th>

                <th class="col-md-2 text-right">
                    <label for="">Actions</label>
                </th>
            </tr>

            @foreach($medias as $media)
                <tr>
                    <td>
                        <x-media-cover :media="$media"/>
                    </td>

                    <td @class(['col-md-2', 'd-none' => $hiddenFields['creator_username']])>
                        <x-username-avatar :user="$media->creatorObj"/>
                    </td>

                    <td class="col-md-1">
                        <span class="badge bg-{{ MediaStatus::from($media->status)->badgeColor() }}">
                            {{ MediaStatus::from($media->status)->label() }}
                        </span>
                    </td>

                    <td class="col-md-1">
                        {{$media->like_count}}
                    </td>

                    <td class="col-md-1">
                        {{$media->visit_count}}
                    </td>

                    <td class="col-md-1">
                        {{$media->created_at}}
                    </td>

                    <td class="col-md-1 text-right">
                        <a class="btn btn-primary btn-sm" href="{{$media->admin_url}}" title="View"><i class="fas fa-eye"></i></a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $medias->links() }}
    </div>
</div>
