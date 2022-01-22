@php
    use App\Models\User;
    use Aparlay\Core\Models\Enums\UserGender;
    use Aparlay\Core\Models\Enums\UserStatus;
    use Aparlay\Core\Models\Enums\UserVerificationStatus;
@endphp

<div class="user-table">
    <div class="filters pb-3">
        <div class="row">
            <div class="col-md-3">
                <label for="">Text Search</label>
                <input class="form-control" type="text" wire:model="filter.text_search"/>
            </div>


            <div class="col-md-1  ml-auto">
                <label for="">Per Page</label>
                <select class="form-control" wire:model="perPage">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                </select>
            </div>

        </div>
    </div>

    <table class="table table-striped">
        <tbody>
        <tr>
            <td class="col-md-2">
                <div>
                    <label for="">Username</label>
                    <input class="form-control" type="text" wire:model="filter.username"/>
                </div>
            </td>
            <td class="col-md-2">
                <div>
                    <label for="">Email</label>
                    <input class="form-control" type="text" wire:model="filter.email"/>
                </div>
            </td>
            <td class="col-md-2">
                <div>
                    <label for="">Country</label>
                    <select class="form-control" wire:model="filter.country">
                        <option value="">Any</option>
                        @foreach(\Aparlay\Core\Models\Country::get() as $country)
                            <option value="{{$country->alpha2}}">{{$country->name}}</option>
                        @endforeach
                    </select>
                </div>
            </td>
            <td class="col-md-1">
                <div>
                    <label for="">Gender</label>
                    <select class="form-control" wire:model="filter.gender">
                        <option value="">Any</option>
                        @foreach(User::getGenders() as $value => $label)
                            <option value="{{$value}}">{{$label}}</option>
                        @endforeach
                    </select>
                </div>
            </td>
            <td class="col-md-1">
                <div>
                    <label for="">Status</label>
                    <select class="form-control" wire:model="filter.status">
                        <option value="">Any</option>
                        @foreach(User::getStatuses() as $value => $label)
                            <option value="{{$value}}">{{$label}}</option>
                        @endforeach
                    </select>
                </div>
            </td>
            <td class="col-md-1">
                <div>
                    <label for="">Verification</label>
                    <select class="form-control" wire:model="filter.verification_status">
                        <option value="">Any</option>
                        @foreach(User::getVerificationStatuses() as $value => $label)
                            <option value="{{$value}}">{{$label}}</option>
                        @endforeach
                    </select>
                </div>
            </td>
            <td class="col-md-2">
                <div>
                    <label for="">Created at</label>
                </div>
            </td>
            <td></td>
        </tr>

        @foreach($users as $user)
            <tr>
                <td>
                    <a href="{{$user->admin_url}}">
                    <img src="{{ $user->avatar }}?aspect_ratio=1:1&width=150" alt="" class="img-circle img-size-50 mr-2">
                     {{$user->username }}
                    </a>
                    <span class="ml-1 {{ $user->is_online ? 'text-info' : 'text-gray' }} text-sm far fa-circle"></span>
                </td>
                <td>
                    <a href="{{$user->admin_url}}">{{ $user->email }}</a>
                </td>
                <td>
                    <img src="{{ $user->country_flag_24 }}" alt="{{ $user->country_alpha3 }}" class="mr-1 align-bottom">{{ $user->country_label }}
                </td>
                <td>
                    <span class="badge bg-{{ UserGender::from($user->gender)->badgeColor() }}">
                        {{ UserGender::from($user->gender)->label() }}
                    </span>
                </td>
                <td>
                    <span class="badge bg-{{ UserStatus::from($user->status)->badgeColor() }}">
                        {{ UserStatus::from($user->status)->label() }}
                    </span>
                </td>

                <td>
                    <div class="row">
                        <div class="col-md-6">
                            @if ($user->verification_status)
                                <span class="badge bg-{{ UserVerificationStatus::from($user->verification_status)->badgeColor() }}">
                            {{ UserVerificationStatus::from($user->verification_status)->label() }}
                        </span>
                            @else
                                {{$user->omg}}
                                <span class="badge bg-info">None</span>
                            @endif
                        </div>
                    </div>
                </td>

                <td>
                    {{ $user->created_at }}
                </td>

                <td>
                    <div class="col-md-6">
                        <div>
                            <button
                                class="btn btn-sm btn-success"
                                type="button"
                                wire:key="verify_button_{{ $user->_id }}}"
                                wire:click="$emit('showModal', 'modals.user-verification-modal', '{{ $user->_id }}')"
                            >
                                <i class="fa fa-edit"></i>
                            </button>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $users->links() }}
</div>