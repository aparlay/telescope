<div class="tab-pane" id="medias">
    <div class="content">
        <div class="container-fluid mt-3">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#media-tab-avatar">Avatar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#media-tab-media">Media</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane container active" id="media-tab-avatar">
                    @include('default_view::admin.pages.user.tabs.media-categories.avatar', ['user' => $user])
                </div>
                <div class="tab-pane container" id="media-tab-media">
                    <livewire:user-media :creatorId="$user->id" />
                </div>
            </div>
        </div>
    </div>
</div>
