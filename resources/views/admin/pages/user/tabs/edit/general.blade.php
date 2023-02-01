@php
    use Illuminate\Support\Arr;
    use Aparlay\Core\Models\Enums\UserVerificationStatus;
@endphp

<div class="card">
    <form action="{{ route('core.admin.user.updateGeneral', ['user' => $user->id]) }}" class="form-horizontal" method="post">
        @csrf()
        @method('PUT')
        <div class="card-header">
            <h3 class="card-title">General</h3>
            <div class="card-tools">
                <button type="submit" class="btn btn-tool">Edit <i class="fas fa-pen"></i></button>
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
            <div class="tab-pane active" id="user-info">
                <div class="form-group row">
                    <label for="feature_tips" class="col-sm-2 col-form-label">Feature Tips</label>
                    <div class="col-sm-10">
                        <div class="custom-control custom-switch mt-2">
                            <input type="checkbox" value="1" class="custom-control-input" name="features[tips]" id="feature_tips" {!! Arr::get($user->features, 'tips') ? 'checked' : '' !!}>
                            <label class="custom-control-label" for="feature_tips"></label>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="feature_demo" class="col-sm-2 col-form-label">Feature Demo User</label>
                    <div class="col-sm-10">
                        <div class="custom-control custom-switch mt-2">
                            <input type="checkbox" class="custom-control-input" value="1" name="features[demo]" id="feature_demo" {!! Arr::get($user->features, 'demo') ? 'checked' : '' !!}>
                            <label class="custom-control-label" for="feature_demo"></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
