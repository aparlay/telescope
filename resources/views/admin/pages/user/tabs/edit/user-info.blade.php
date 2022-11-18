@php
    use Illuminate\Support\Arr;
    use Aparlay\Core\Models\Enums\UserVerificationStatus;
@endphp

<div class="card">
    <form action="{{ route('core.admin.user.update.userinfo', ['user' => $user->_id]) }}" class="form-horizontal" method="post">
        @csrf()
        @method('PUT')
        <div class="card-header">
            <h3 class="card-title">User Information</h3>
            <div class="card-tools">
                <button type="submit" class="btn text-blue">Edit <i class="fas fa-pen"></i></button>
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
                    <label for="id" class="col-sm-2 col-form-label">User ID</label>
                    <div class="col-sm-10 mt-2 pl-4">
                        <p>{{ $user->_id }}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="gender" class="col-sm-2 col-form-label">Verification Status</label>
                    <div class="col-sm-10">
                        <select name="verification_status" id="verification_status" class="form-control">
                            @foreach(UserVerificationStatus::getAllCases() as $key => $label)
                                <option value="{{ $key }}" {!! $user->verification_status == $key ? 'selected' : '' !!}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
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
                    <label class="col-sm-2 col-form-label">Created At</label>
                    <div class="col-sm-10 mt-2 pl-4">
                        <p>{{ $user->created_at }}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Updated At</label>
                    <div class="col-sm-10 mt-2 pl-4">
                        <p>{{ $user->updated_at }}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Last Online</label>
                    <div class="col-sm-10 mt-2 pl-4">
                        <p>{{ $user->last_online_at }}</p>
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
                    <div class="col-sm-10 mt-2 pl-4">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" value="1" name="email_verified" class="custom-control-input" id="email_verified" disabled="disabled" readonly="readonly" {!! $user->email_verified ? 'checked' : '' !!}>
                            <label class="custom-control-label" for="email_verified"></label>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="full_name" class="col-sm-2 col-form-label">Full Name</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="full_name" name="full_name" value="{{ $user->full_name }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="birthday" class="col-sm-2 col-form-label">Date of Birth</label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="birthday" name="birthday" value="{{ $user->birthday }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Fraud Tier</label>
                    <div class="col-sm-10 mt-2 pl-4">
                        <p>--</p>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="country_alpha2" class="col-sm-2 col-form-label">Registered Country</label>
                    <div class="col-sm-10">
                        <select name="country_alpha2" id="country_alpha2" class="form-control">
                            @foreach(\Aparlay\Core\Helpers\Country::getAlpha2AndNames() as $alpha2 => $country)
                                <option value="{{$alpha2}}" {!! $user->country_alpha2 == $alpha2 ? 'selected' : '' !!}>{{$country}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="ip_country_alpha2" class="col-sm-2 col-form-label">IP Country</label>
                    <div class="col-sm-10 mt-2 pl-4">
                        <p>--</p>
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
                    <label class="col-sm-2 col-form-label">City</label>
                    <div class="col-sm-10 mt-2 pl-4">
                        <p>--</p>
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
                    <label class="col-sm-2 col-form-label">Blocked Users</label>
                    <div class="col-sm-10 mt-2 pl-4">
                        <p>--</p>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="interested_in" class="col-sm-2 col-form-label">Interested In</label>
                    <div class="col-sm-10">
                        <select name="interested_in" id="interested_in" class="form-control">
                            @foreach($user->getInterestedIns() as $key => $interested_in)
                                <option value="{{ $key }}" {!! $user->interested_in == $key ? 'selected' : '' !!}>{{ $interested_in }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
