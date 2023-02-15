@php
    use Illuminate\Support\Arr;
    use Aparlay\Core\Models\Enums\UserVerificationStatus;
    use Aparlay\Core\Models\Block;
    use Maklad\Permission\Models\Role;
    use Aparlay\Core\Constants\Roles;
@endphp

<form action="{{ route('core.admin.user.update.userinfo', ['user' => $user->_id]) }}" class="form-horizontal" method="post">
    @csrf()
    @method('PUT')
    <div class="card user-profile-card" id="user-info">
        <div class="card-header py-0">
            <h3 class="card-title p-2">User Information</h3>
            <div class="card-tools">
                <button type="button" class="btn text-blue card-edit" data-edit="user-info">Edit <i class="fas fa-pen"></i></button>
                <button type="button" class="btn text-danger card-cancel d-none" data-edit="user-info">Cancel <i class="fas fa-times"></i></button>
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
            <div class="tab-pane active">
                <div class="form-group row m-0">
                    <input type="hidden" id="user_id" name="user_id" value="{{ (string)$user->_id }}">
                    <label for="id" class="col-sm-2 col-form-label">User ID</label>
                    <div class="col-sm-10">
                        <div class="mt-2 pl-4">
                            <p>{{ $user->_id }}</p>
                        </div>
                    </div>
                </div>
                <div class="form-group row m-0">
                    <label for="gender" class="col-sm-2 col-form-label">Verification Status</label>
                    <div class="col-sm-10">
                        <div class="mt-2 pl-4 data-show">
                            @foreach(UserVerificationStatus::getAllCases() as $key => $label)
                                @if( $key == $user->verification_status )
                                    <p>{{ Str::ucfirst($label) }}</p>
                                @endif
                            @endforeach
                        </div>
                        <select name="verification_status" id="verification_status" class="form-control data-edit d-none">
                            @foreach(UserVerificationStatus::getAllCases() as $key => $label)
                                <option value="{{ $key }}" {!! $user->verification_status == $key ? 'selected' : '' !!}>{{ Str::ucfirst($label) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row m-0">
                    <label for="status" class="col-sm-2 col-form-label">Status</label>
                    <div class="col-sm-10">
                        <div class="mt-2 pl-4 data-show">
                            @foreach($user->getStatuses() as $key => $label)
                                @if( $key == $user->status )
                                    <p>{{ Str::ucfirst($label) }}</p>
                                @endif
                            @endforeach
                        </div>
                        <select name="status" id="status" class="form-control data-edit d-none">
                            @foreach($user->getStatuses() as $key => $label)
                                <option value="{{ $key }}" {!! $user->status == $key ? 'selected' : '' !!}>{{ Str::ucfirst($label) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @if(auth()->user()->hasRole(Roles::SUPER_ADMINISTRATOR))
                    <div class="form-group row m-0">
                        <label for="role" class="col-sm-2 col-form-label">Role</label>
                        <div class="col-sm-10">
                            <div class="mt-2 pl-4 data-show">
                                <p>{{ ucfirst($user->roles()->first()?->name) }}</p>
                            </div>
                            <select name="role" id="role" class="form-control data-edit d-none">
                                <option value="">None</option>
                                @foreach(Role::all() as $role)
                                    <option value="{{ $role->name }}" {!! $user->hasRole($role) ? 'selected' : '' !!}>{{ Str::ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
                <div class="form-group row m-0">
                    <label for="visibility" class="col-sm-2 col-form-label">Public Profile</label>
                    <div class="col-sm-10">
                        <div class="mt-2 pl-4 data-show">
                            <p>{{ $user->visibility ? 'Yes' : 'No' }}</p>
                        </div>
                        <div class="custom-control custom-switch mt-2 ml-2 data-edit d-none">
                            <input type="checkbox" value="1" name="visibility" class="custom-control-input" id="visibility" {!! $user->visibility ? 'checked' : '' !!}>
                            <label class="custom-control-label" for="visibility"></label>
                        </div>
                    </div>
                </div>
                <div class="form-group row m-0">
                    <label class="col-sm-2 col-form-label">Created At</label>
                    <div class="col-sm-10">
                        <div class="mt-2 pl-4">
                            <p>{{ $user->created_at }}</p>
                        </div>
                    </div>
                </div>
                <div class="form-group row m-0">
                    <label class="col-sm-2 col-form-label">Updated At</label>
                    <div class="col-sm-10">
                        <div class="mt-2 pl-4">
                            <p>{{ $user->updated_at }}</p>
                        </div>
                    </div>
                </div>
                <div class="form-group row m-0">
                    <label class="col-sm-2 col-form-label">Last Online</label>
                    <div class="col-sm-10">
                        <div class="mt-2 pl-4">
                            <p>{{ $user->last_online_at }}</p>
                        </div>
                    </div>
                </div>
                <div class="form-group row m-0">
                    <label for="email" class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                        <div class="mt-2 pl-4 data-show">
                            <p>{{ $user->email }}</p>
                        </div>
                        <input type="email" class="form-control data-edit d-none" id="email" name="email" value="{{ $user->email }}">
                    </div>
                </div>
                <div class="form-group row m-0">
                    <label for="email_verified" class="col-sm-2 col-form-label">Email Verified</label>
                    <div class="col-sm-10">
                        <div class="mt-2 pl-4 data-show">
                            <p>{{ $user->email_verified ? 'Yes' : 'No' }}</p>
                        </div>
                        <div class="custom-control custom-switch mt-2 ml-2 data-edit d-none">
                            <input type="checkbox" value="1" name="email_verified" class="custom-control-input" id="email_verified" {!! $user->email_verified ? 'checked' : '' !!}>
                            <label class="custom-control-label" for="email_verified"></label>
                        </div>
                    </div>
                </div>
                <div class="form-group row m-0">
                    <label for="full_name" class="col-sm-2 col-form-label">Full Name</label>
                    <div class="col-sm-10">
                        <div class="mt-2 pl-4 data-show">
                            <p>{{ $user->full_name }}</p>
                        </div>
                        <input type="text" class="form-control data-edit d-none" id="full_name" name="full_name" value="{{ $user->full_name }}">
                    </div>
                </div>
                <div class="form-group row m-0">
                    <label for="birthday" class="col-sm-2 col-form-label">Date of Birth</label>
                    <div class="col-sm-10">
                        <div class="mt-2 pl-4 data-show">
                            <p>{{ $user->birthday }}</p>
                        </div>
                        <input type="date" class="form-control data-edit d-none" id="birthday" name="birthday" value="{{ $user->birthday }}">
                    </div>
                </div>
                <div class="form-group row m-0">
                    <label class="col-sm-2 col-form-label">Fraud Tier</label>
                    <div class="col-sm-10">
                        <div class="mt-2 pl-4">
                            <p>{{ \Aparlay\Core\Helpers\Country::getTier($user->country_alpha2) }}</p>
                        </div>
                    </div>
                </div>
                <div class="form-group row m-0">
                    <label for="country_alpha2" class="col-sm-2 col-form-label">Registered Country</label>
                    <div class="col-sm-10">
                        <div class="mt-2 pl-4 data-show">
                            @foreach(\Aparlay\Core\Helpers\Country::getAlpha2AndNames() as $key => $label)
                                @if ($key == $user->country_alpha2)
                                    <p>{{ Str::ucfirst($label) }}</p>
                                @endif
                            @endforeach
                        </div>
                        <select name="country_alpha2" id="country_alpha2" class="form-control data-edit d-none">
                            <option value="">Select a country</option>
                            @foreach(\Aparlay\Core\Helpers\Country::getAlpha2AndNames() as $key => $label)
                                <option value="{{ $key }}" {!! $user->country_alpha2 == $key ? 'selected' : '' !!}>{{ Str::ucfirst($label) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row m-0">
                    <label for="ip_country_alpha2" class="col-sm-2 col-form-label">IP Country</label>
                    <div class="col-sm-10">
                        <div class="mt-2 pl-4">
                            {{ $user->country_label }}
                        </div>
                    </div>
                </div>
                <div class="form-group row m-0">
                    <label class="col-sm-2 col-form-label" for="payout_country">Payout Country</label>
                    <div class="col-sm-10">
                        <div class="mt-2 pl-4 data-show">
                            @foreach(\Aparlay\Core\Helpers\Country::getAlpha2AndNames() as $key => $label)
                                @if ($key == $user->payout_country_alpha2)
                                    <p>{{ Str::ucfirst($label) }}</p>
                                @endif
                            @endforeach
                        </div>
                        <select name="payout_country_alpha2" class="form-control data-edit d-none" id="payout_country">
                            <option value="">Select a country</option>
                            @foreach(\Aparlay\Core\Helpers\Country::getAlpha2AndNames() as $key => $label)
                                <option value="{{ $key }}" {!! $user->payout_country_alpha2 == $key ? 'selected' : '' !!}>{{ Str::ucfirst($label) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row m-0">
                    <label class="col-sm-2 col-form-label">City</label>
                    <div class="col-sm-10">
                        <div class="mt-2 pl-4">
                            <p>--</p>
                        </div>
                    </div>
                </div>
                <div class="form-group row m-0">
                    <label for="gender" class="col-sm-2 col-form-label">Gender</label>
                    <div class="col-sm-10">
                        <div class="mt-2 pl-4 data-show">
                            @foreach($user->getGenders() as $key => $label)
                                @if ($key == $user->gender)
                                    <p>{{ Str::ucfirst($label) }}</p>
                                @endif
                            @endforeach
                        </div>
                        <select name="gender" id="gender" class="form-control data-edit d-none">
                            @foreach($user->getGenders() as $key => $label)
                                <option value="{{ $key }}" {!! $user->gender == $key ? 'selected' : '' !!}>{{ Str::ucfirst($label) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row m-0">
                    <label class="col-sm-2 col-form-label">Blocked Users</label>
                    <div class="col-sm-10">
                        <div class="mt-2 pl-4">
                            @if($user->stats['counters']['blocks'])
                                @foreach(Block::query()->creator((string) $user->_id)->get() as $block)
                                    <a class="badge badge-danger mr-1" href="{{ route('core.admin.user.view', $block->userObj) }}">{{ $block->userObj->username }}</a>
                                @endforeach
                            @else
                                <p>None</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
