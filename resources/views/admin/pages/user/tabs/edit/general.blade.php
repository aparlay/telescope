@php
    use Illuminate\Support\Arr;
    use Aparlay\Core\Models\Enums\UserVerificationStatus;
@endphp

<form action="{{ route('core.admin.user.update.general', ['user' => $user->_id]) }}" class="form-horizontal" method="post">
    @csrf()
    @method('PUT')
    <div class="card collapsed-card user-profile-card" id="user-general">
        <div class="card-header py-0">
            <h3 class="card-title p-2">General</h3>
            <div class="card-tools">
                <button type="button" class="btn text-blue card-edit" data-edit="user-general">Edit <i class="fas fa-pen"></i></button>
                <button type="button" class="btn text-danger card-cancel d-none" data-edit="user-general">Cancel <i class="fas fa-times"></i></button>
                <button type="submit" class="btn text-blue card-save d-none">Save <i class="fas fa-save"></i></button>
                <button
                    type="button"
                    class="btn btn-tool"
                    data-card-widget="collapse"
                    data-expand-icon="fa-chevron-down"
                    data-collapse-icon="fa-chevron-up"
                ><i class="fas fa-chevron-down"></i></button>
            </div>
        </div>
        <div class="card-body py-1">
            <div class="tab-pane active">
                <div class="form-group row m-0">
                    <label for="banned_countries" class="col-12 col-lg-2 col-form-label">Banned Countries</label>
                    <div class="col-12 col-lg-10 form-element">
                        <div class="pl-0 pl-lg-4 mt-0 mt-lg-2">
                            <p>--</p>
                        </div>
                    </div>
                </div>
                <div class="form-group row m-0">
                    <label for="feature_tips" class="col-12 col-lg-2 col-form-label">Feature Tips</label>
                    <div class="col-12 col-lg-10 form-element">
                        <div class="pl-0 pl-lg-4 mt-0 mt-lg-2 data-show">
                            <p>{{ Arr::get($user->features, 'tips') ? 'Yes' : 'No' }}</p>
                        </div>
                        <div class="custom-control custom-switch mt-2 ml-2 data-edit d-none">
                            <input type="checkbox" value="1" class="custom-control-input" name="features[tips]" id="feature_tips" {!! Arr::get($user->features, 'tips') ? 'checked' : '' !!}>
                            <label class="custom-control-label" for="feature_tips"></label>
                        </div>
                    </div>
                </div>

                <div class="form-group row m-0">
                    <label for="feature_subscriptions" class="col-sm-2 col-form-label">Feature Subscriptions</label>
                    <div class="col-sm-10">
                        <div class="mt-2 pl-4 data-show">
                            <p>{{ Arr::get($user->features, 'subscriptions') ? 'Yes' : 'No' }}</p>
                        </div>
                        <div class="custom-control custom-switch mt-2 ml-2 data-edit d-none">
                            <input type="checkbox" value="1" class="custom-control-input" name="features[subscriptions]" id="feature_subscriptions" {!! Arr::get($user->features, 'subscriptions') ? 'checked' : '' !!}>
                            <label class="custom-control-label" for="feature_subscriptions"></label>
                        </div>
                    </div>
                </div>
<!--
                <div class="form-group row m-0">
                    <label for="feature_demo" class="col-sm-2 col-form-label">Feature Demo User</label>
                    <div class="col-sm-10">
                        <div class="custom-control custom-switch mt-2">
                            <input type="checkbox" class="custom-control-input" value="1" name="features[demo]" id="feature_demo" {!! Arr::get($user->features, 'demo') ? 'checked' : '' !!}>
                            <label class="custom-control-label" for="feature_demo"></label>
                        </div>
                    </div>
                </div>
-->
            </div>
        </div>
    </div>
</form>
