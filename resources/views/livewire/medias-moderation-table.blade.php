@php
    use App\Models\Media;
    use Aparlay\Core\Models\Enums\MediaStatus;
@endphp

<div class="medias-table">
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
                </div>
            </td>
            <td class="col-md-1">
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'visit_count'" :fieldLabel="'Visits'" />
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
