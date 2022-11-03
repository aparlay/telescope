@php
    use Illuminate\Support\Arr;
    use Aparlay\Core\Models\Enums\UserVerificationStatus;
@endphp

<div class="tab-pane active" id="user-info">
    <form action="" class="form-horizontal" method="POST" enctype="multipart/form-data">
        @csrf()
        @method('PUT')
        <div class="form-group row">
            <label for="avatar" class="col-sm-2 col-form-label">Avatar</label>
            <div class="col-sm-10">
                <input type="file" class="form-control-file" id="avatar" name="avatar" accept="image/*">
            </div>
        </div>
        <div class="form-group row">
            <label for="username" class="col-sm-2 col-form-label">Username</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="username" name="username" value="{{ $user->username }}" disabled>
            </div>
        </div>
        <div class="form-group row">
            <label for="country_alpha2" class="col-sm-2 col-form-label">Country</label>
            <div class="col-sm-10">
                <select name="country_alpha2" id="country_alpha2" class="form-control">
                    @foreach(\Aparlay\Core\Helpers\Country::getAlpha2AndNames() as $alpha2 => $country)
                        <option value="{{$alpha2}}" {!! $user->country_alpha2 == $alpha2 ? 'selected' : '' !!}>{{$country}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-2 col-form-label" for="payout_country">Payout Country</label>
            <div class="col-sm-10">
                <select name="payout_country_alpha2" class="form-control" id="payout_country">
                    <option value="">Select a country</option>
                    @foreach($countries as $country)
                        <option {!! $user->payout_country_alpha2 == $country->alpha2 ? 'selected' : '' !!} value="{{ $country->alpha2 }}">
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label for="email" class="col-sm-2 col-form-label">Email</label>
            <div class="col-sm-10">
                <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}">
            </div>
        </div>
        <div class="form-group row">
            <label for="email_verified" class="col-sm-2 col-form-label">Email Verified</label>
            <div class="col-sm-10">
                <div class="custom-control custom-switch mt-2">
                    <input type="checkbox" value="1" name="email_verified" class="custom-control-input" id="email_verified" {!! $user->email_verified ? 'checked' : '' !!}>
                    <label class="custom-control-label" for="email_verified"></label>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label for="bio" class="col-sm-2 col-form-label">Bio</label>
            <div class="col-sm-10">
                <textarea name="bio" id="bio" cols="30" rows="3" class="form-control"></textarea>
            </div>
        </div>
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
        <div class="form-group row">
            <label for="gender" class="col-sm-2 col-form-label">Verification status</label>
            <div class="col-sm-10">
                <select name="verification_status" id="verification_status" class="form-control">
                    @foreach(UserVerificationStatus::getAllCases() as $key => $label)
                        <option value="{{ $key }}" {!! $user->verification_status == $key ? 'selected' : '' !!}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label for="gender" class="col-sm-2 col-form-label">Gender</label>
            <div class="col-sm-10">
                <select name="gender" id="gender" class="form-control">
                    @foreach($user->getGenders() as $key => $gender)
                        <option value="{{ $key }}" {!! $user->gender == $key ? 'selected' : '' !!}>{{ $gender }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label for="interested_in" class="col-sm-2 col-form-label">Interested In</label>
            <div class="col-sm-10">
                @foreach($user->getInterestedIns() as $key => $interested_in)
                    <div>
                        <input type="checkbox" value="{{ $key }}" id="{{$interested_in}}" name="interested_in[]" {!! in_array($key, $user->interested_in) ? 'checked' : '' !!}>
                        <label for="{{$interested_in}}">{{$interested_in}}</label>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="form-group row">
            <label for="type" class="col-sm-2 col-form-label">Type</label>
            <div class="col-sm-10">
                <select name="type" id="type" class="form-control">
                    @foreach($user->getTypes() as $key => $type)
                        <option value="{{ $key }}" {!! $user->type == $key ? 'selected' : '' !!}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @role('super-administrator')
        <div class="form-group row">
            <label for="role" class="col-sm-2 col-form-control">Role</label>
            <div class="col-sm-10">
                <select name="role" class="form-control" id="role">
                    <option value=""></option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ $user->role_ids && in_array($role->_id, $user->role_ids) ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endrole
        <div class="form-group row">
            <label for="status" class="col-sm-2 col-form-label">Status</label>
            <div class="col-sm-10">
                <select name="status" id="status" class="form-control">
                    @foreach($user->getStatuses() as $key => $status)
                        <option value="{{ $key }}" {!! $user->status == $key ? 'selected' : '' !!}>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label for="visibility" class="col-sm-2 col-form-label">Visibility</label>
            <div class="col-sm-10">
                <select name="visibility" id="visibility" class="form-control">
                    @foreach($user->getVisibilities() as $key => $visibility)
                        <option value="{{ $key }}" {!! $user->visibility == $key ? 'selected' : '' !!}>{{ $visibility }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label for="referral_id" class="col-sm-2 col-form-label">Referral User ID</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="referral_id" name="referral_id" value="{{ $user->referral_id }}">
            </div>
        </div>
        <div class="form-group row">
            <label for="promo_link" class="col-sm-2 col-form-label">Promo Link</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="promo_link" name="promo_link" value="{{ $user->promo_link }}">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Created At</label>
            <div class="col-sm-10 mt-2">
                <p>{{ $user->created_at }}</p>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Updated At</label>
            <div class="col-sm-10 mt-2">
                <p>{{ $user->updated_at }}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 mt-3">
                <button type="submit" class="btn btn-md btn-primary col-md-1 float-right">Submit</button>
            </div>
        </div>
    </form>
</div><?php
