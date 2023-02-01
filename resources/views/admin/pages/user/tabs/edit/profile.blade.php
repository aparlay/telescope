@php
    use Illuminate\Support\Arr;
    use Aparlay\Core\Models\Enums\UserVerificationStatus;
@endphp

<form action="{{ route('core.admin.user.update.profile', ['user' => $user->_id]) }}" class="form-horizontal" method="post" id="profile-form">
    @csrf()
    @method('PUT')
    <div class="card user-profile-card" id="user-profile">
        <div class="card-header py-0">
            <h3 class="card-title p-2">Profile</h3>
            <div class="card-tools">
                <button type="button" class="btn text-blue card-edit" data-edit="user-profile">Edit <i class="fas fa-pen"></i></button>
                <button type="submit" class="btn text-blue card-save d-none">Save <i class="fas fa-save"></i></button>
                <button
                    type="button"
                    class="btn btn-tool"
                    data-card-widget="collapse"
                    data-expand-icon="fa-chevron-down"
                    data-collapse-icon="fa-chevron-up"
                ><i class="fas fa-chevron-up"></i></button>
            </div>
        </div>
        <div class="card-body py-1">
            <div class="tab-pane active" id="user-info">
                <div class="form-group row m-0">
                    <label for="username" class="col-sm-2 col-form-label">Username</label>
                    <div class="col-sm-10 form-element">
                        <div class="mt-2 pl-4 data-show">
                            <p>{{ $user->username }}</p>
                        </div>
                        <input type="text" class="form-control data-edit d-none" id="username" name="username" value="{{ $user->username }}">
                    </div>
                </div>
                <div class="form-group row m-0">
                    <label for="bio" class="col-sm-2 col-form-label">Bio</label>
                    <div class="col-sm-10 form-element">
                        <div class="mt-2 pl-4 data-show">
                            <p>{{ $user->bio }}</p>
                        </div>
                        <textarea class="form-control w-100 data-edit d-none" name="bio" id="bio" rows="3">{{ $user->bio }}</textarea>
                    </div>
                </div>
                <div class="form-group row m-0">
                    <label for="promo_link" class="col-sm-2 col-form-label">Promo Link</label>
                    <div class="col-sm-10">
                        <div class="mt-2 pl-4 data-show">
                            <p>{{ $user->promo_link }}</p>
                        </div>
                        <input type="text" class="form-control data-edit d-none" id="promo_link" name="promo_link" value="{{ $user->promo_link }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
