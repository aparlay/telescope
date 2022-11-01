@php
    use Illuminate\Support\Arr;
    use Aparlay\Core\Models\Enums\UserVerificationStatus;
@endphp

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Profile</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool">Edit <i class="fas fa-pen"></i></button>
            <button
                type="button"
                class="btn btn-tool"
                data-card-widget="collapse"
                data-expand-icon="fa-chevron-down"
                data-collapse-icon="fa-chevron-up"
            ><i class="fas fa-chevron-up"></i></button>
        </div>
    </div>
    <div class="card-body">
        <form action="" class="form-horizontal" enctype="multipart/form-data">
            @csrf()
            @method('PUT')
            <div class="tab-pane active" id="user-info">
                <div class="form-group row">
                    <label for="username" class="col-sm-2 col-form-label">Username</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="username" name="username" value="{{ $user->username }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="bio" class="col-sm-2 col-form-label">Bio</label>
                    <div class="col-sm-10">
                        <textarea name="bio" id="bio" cols="30" rows="3" class="form-control"></textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="promo_link" class="col-sm-2 col-form-label">Promo Link</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="promo_link" name="promo_link" value="{{ $user->promo_link }}">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
