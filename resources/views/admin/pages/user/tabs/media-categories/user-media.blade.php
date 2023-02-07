@php
    use Aparlay\Core\Models\Media;
    use Aparlay\Core\Models\Enums\MediaStatus;
    use Illuminate\Support\Str;
    use Carbon\CarbonInterval;
@endphp
<div class="row">
    <div class="col-12">
        {{ $medias->links() }}
    </div>
    @foreach($medias as $media)
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card">
                <a href="{{ route('core.admin.media.view', $media) }}">
                    <img src="{{ $media->cover_url }}" alt="{{ $user->username }}" class="card-img-top" style="width: 100%; height: 15vw; object-fit: cover;">
                </a>
                <div class="card-body">
                    <span class="mr-2 badge bg-{{ MediaStatus::from($media->status)->badgeColor() }}">
                        {{ MediaStatus::from($media->status)->label() }}
                    </span>
                    <span class="badge badge-primary">{{ $media->created_at->diffForHumans() }}</span>
                    <div class="row">
                        <div class="col-6">
                            <p class="text-muted small"><b>Description:</b> <br>{{ Str::excerpt($media->description, '', ['radius' => 40]) }}</p>
                        </div>
                        <div class="col-6 border-left">
                            <span class="text-muted small"><b>Stats:</b></span><br>
                            <span class="text-muted small">Visits: <span class="float-right">{{ $media->visit_count }}</span></span><br>
                            <span class="text-muted small">Likes: <span class="float-right">{{ $media->like_count }}</span></span><br>
                            <span class="text-muted small">Comments: <span class="float-right">{{ $media->comment_count }}</span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    <div class="col-12">
        {{ $medias->links() }}
    </div>
    <div class="col-12">
        @include('default_view::admin.pages.user.tabs.upload', ['user' => $user])
    </div>
</div>
