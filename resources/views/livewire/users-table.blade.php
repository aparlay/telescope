@php
    use Aparlay\Core\Models\User;
    use Aparlay\Core\Models\Enums\UserGender;
    use Aparlay\Core\Models\Enums\UserStatus;
    use Aparlay\Core\Models\Enums\UserVerificationStatus;
    use Aparlay\Core\Models\Country;
@endphp

<div class="user-table table-responsive">
    <div class="filters pb-3">
        <div class="row">
            <div class="col-md-6">
                <label for="">Search</label>
                <input class="form-control" type="text" wire:model="filter.text_search"/>
            </div>


            <div class="col-md-2">
                <label for="">Start Date</label>
                <x-date-picker
                        wire:model.lazy="filter.created_at.start"
                        autocomplete="off"
                        placeholder="Start"
                />
            </div>
            <div class="col-md-2">
                <label for="">End Date</label>
                <x-date-picker
                        wire:model.lazy="filter.created_at.end"
                        autocomplete="off"
                        placeholder="End"
                />
            </div>
            <div class="col-md-2 ml-auto">
                <label for="">Per Page</label>
                <x-wire-dropdown-list :wire-model="'perPage'" :show-any="false" :options="[5 => 5, 10 => 10, 15 => 15]"/>
            </div>

        </div>
    </div>

    <table class="table table-striped bg-white border">
        <thead>
        <tr class="d-flex">
            <th class="col-md-2">
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'username'" :fieldLabel="'Username'" />
                    <input class="form-control" type="text" wire:model="filter.username"/>
                </div>
            </th>
            <th class="col-md-2">
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'email'" :fieldLabel="'Email'" />
                    <input class="form-control" type="text" wire:model="filter.email"/>
                </div>
            </th>
            <th class="col-md-2">
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'country'" :fieldLabel="'Country'" />

                    <x-wire-dropdown-list
                            :wire-model="'filter.country'"
                            :options="\Aparlay\Core\Models\Country::query()->get()->pluck('name', 'alpha2')->all()"
                    />
                </div>
            </th>
            <th class="col-md-1">
                <div>
                    <label for="">Gender</label>
                    <x-wire-dropdown-list :wire-model="'filter.gender'" :options="User::getGenders()"/>
                </div>
            </th>
            <th class="col-md-1">
                <div>
                    <label for="">Status</label>
                    <x-wire-dropdown-list :wire-model="'filter.status'" :options="User::getStatuses()"/>
                </div>
            </th>
            <th class="col-md-1">
                <div>
                    <label for="">Verif. Status</label>
                    <x-wire-dropdown-list :wire-model="'filter.verification_status'" :options="User::getVerificationStatuses()"/>
                </div>
            </th>

            <th class="col-md-2">
                <x-sortable-column-header :sort="$sort" :fieldName="'created_at'" :fieldLabel="'Reg. Date'" />
            </th>

            <th class="col-md-1">
                <div>
                    <label for="">Profile</label>
                </div>
            </th>
        </tr>
        </thead>
        <tbody>

        @foreach($users as $user)
            <tr class="d-flex">
                <td class="col-md-2">
                    <x-username-avatar :user="$user"/>
                </td>
                <td class="col-md-2">
                    <a href="{{$user->admin_url}}">{{ $user->email }}</a>
                </td>
                <td class="col-md-2">
                    <img src="{{ $user->country_flags['24'] }}" alt="{{ $user->country_alpha3 }}"
                         class="mr-1 align-bottom">{{ $user->country_label }}
                </td>
                <td class="col-md-1">
                    <span class="badge bg-{{ UserGender::from($user->gender)->badgeColor() }}">
                        {{ UserGender::from($user->gender)->label() }}
                    </span>
                </td>
                <td class="col-md-1">
                    <span class="badge bg-{{ UserStatus::from($user->status)->badgeColor() }}">
                        {{ UserStatus::from($user->status)->label() }}
                    </span>
                </td>

                <td class="col-md-1">
                    <div class="row">
                        <div class="col-md-6">
                            @if ($user->verification_status)
                                <span
                                    class="badge bg-{{ UserVerificationStatus::from($user->verification_status)->badgeColor() }}">
                            {{ UserVerificationStatus::from($user->verification_status)->label() }}
                        </span>
                            @else
                                <span class="badge bg-info">None</span>
                            @endif
                        </div>
                    </div>
                </td>

                <td class="col-md-2">
                    {{ $user->created_at }}
                </td>

                <td class="col-md-1">
                    <div class="col-md-6">
                        <div>
                            <a class="" href="{{$user->admin_url}}">
                                <i title="Profile" class="fa fa-fw fa-eye"></i>
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $users->links() }}
    </div>
</div>
