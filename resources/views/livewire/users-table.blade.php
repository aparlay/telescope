@php
    use App\Models\User;
    use Aparlay\Core\Models\Enums\UserGender;
    use Aparlay\Core\Models\Enums\UserStatus;
    use Aparlay\Core\Models\Enums\UserVerificationStatus;
    use \Aparlay\Core\Models\Country;
@endphp

<div class="user-table">
    <div class="filters pb-3">
        <div class="row">
            <div class="col-md-3">
                <label for="">Search</label>
                <input class="form-control" type="text" wire:model="filter.text_search"/>
            </div>


            <div class="col-md-2 offset-6">
                <div class="row">
                    <div class="col">
                        <label for="">Start Date</label>
                        <x-date-picker
                            wire:model.lazy="filter.created_at.start"
                            autocomplete="off"
                            placeholder="Start"
                        />
                    </div>
                    <div class="col">
                        <label for="">End Date</label>
                        <x-date-picker
                            wire:model.lazy="filter.created_at.end"
                            autocomplete="off"
                            placeholder="End"
                        />
                    </div>
                </div>
            </div>


            <div class="col-md-1 ml-auto">
                <label for="">Per Page</label>
                <x-wire-dropdown-list :wire-model="'perPage'" :show-any="false" :options="[5 => 5, 10 => 10, 15 => 15]"/>
            </div>

        </div>
    </div>

    <table class="table table-striped">
        <tbody>
        <tr>
            <th class="col-md-3">
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
                    <x-sortable-column-header :sort="$sort" :fieldName="'full_name'" :fieldLabel="'Full name'" />
                    <input class="form-control" type="text" wire:model="filter.full_name"/>
                </div>
            </th>
            <th class="col-md-1">
                <div>
                    <label for="">Gender</label>
                    <wire-dropdown-list :wire-model="'filter.gender'" :options="User::getGenders()"/>
                </div>
            </th>
            <th>
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'email_verified'" :fieldLabel="'Email Verified?'" />
                </div>
            </th>
            <th class="col-md-1">
                <div>
                    <label for="">Status</label>
                    <wire-dropdown-list :wire-model="'filter.status'" :options="User::getStatuses()"/>
                </div>
            </th>
            <th class="col-md-1">
                <div>
                    <label for="">Verification</label>
                    <wire-dropdown-list :wire-model="'filter.verification_status'" :options="User::getVerificationStatuses()"/>
                </div>
            </th>
            <th>Followers</th>
            <th>Likes</th>
            <th>Medias</th>

            <th class="col-md-2">
                <x-sortable-column-header :sort="$sort" :fieldName="'created_at'" :fieldLabel="'Registration Date'" />
            </th>

            <th class="col-md-1">
                <div>
                    <label for="">Action????ы</label>
                </div>
            </th>
        </tr>

        @foreach($users as $user)
            <tr>
                <td>
                    <x-username-avatar :user="$user"/>
                </td>
                <td>
                    <a href="{{$user->admin_url}}">{{ $user->email }}</a>
                </td>
                <td>
                    {{ $user->full_name }}
                </td>
                <td>
                    <span class="badge bg-{{ UserGender::from($user->gender)->badgeColor() }}">
                        {{ UserGender::from($user->gender)->label() }}
                    </span>
                </td>
                <td>
                    @if ($user->email_verified)
                        <i class="fa fa-check-circle text-success"></i>
                    @endif
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

                <td>
                    {{ $user->follower_count }}
                </td>

                <td>
                    {{ $user->media_count }}
                </td>

                <td>
                    {{ $user->likes_count }}
                </td>

                <td>
                    {{ $user->created_at }}
                </td>


                <td>
                    <div class="col-md-6">
                        <div>
                            <a class="btn btn-success" href="{{$user->admin_url}}">
                                <i title="Profile" class="fa fa-user-circle"></i>
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $users->links() }}
</div>
