@php
    use Aparlay\Core\Models\Email;
    use Aparlay\Core\Models\Enums\EmailStatus;
    use Aparlay\Core\Models\Enums\EmailType;
@endphp

<div class="user-table">
    <div class="filters pb-3">
        <div class="row">
            <div class="col-md-9 pt-4">
                <h4>Emails</h4>
            </div>
            <div class="col-md-1">
                <label for="">Start Date</label>
                <x-date-picker
                        wire:model.lazy="filter.created_at.start"
                        autocomplete="off"
                        placeholder="Start"
                />
            </div>
            <div class="col-md-1">
                <label for="">End Date</label>
                <x-date-picker
                        wire:model.lazy="filter.created_at.end"
                        autocomplete="off"
                        placeholder="End"
                />
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
            <th @class(['col-md-3', 'd-none' => $hiddenFields['username']])>
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'username'" :fieldLabel="'Username'" />
                    <input class="form-control" type="text" wire:model="filter.username"/>
                </div>
            </th>

            <th class="col-md-2">
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'email'" :fieldLabel="'Email'" />

                </div>
            </th>

            <th class="col-md-2">
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'type'" :fieldLabel="'Type'" />
                    <x-wire-dropdown-list
                        :wire-model="'filter.type'"
                        :options="Email::getTypes()"
                    />
                </div>
            </th>
            <th class="col-md-1">
                <div>
                    <label for="">Status</label>

                    <x-wire-dropdown-list
                        :wire-model="'filter.status'"
                        :options="Email::getStatuses()"
                    />
                </div>
            </th>

            <th class="col-md-2">
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'created_at'" :fieldLabel="'Created At'" />
                </div>
            </th>
        </tr>

        @foreach($models as $model)
            <tr>
                <td @class(['col-md-3', 'd-none' => $hiddenFields['username']])>
                    @if ($model->userObj)
                        <x-username-avatar :user="$model->userObj"/>
                    @endif
                </td>
                <td>
                    @if ($model->userObj)
                    <a href="{{$model->userObj->admin_url}}">
                        {{ $model->userObj->email }}
                    </a>
                    @endif
                </td>

                <td>
                    <span class="badge bg-{{ EmailType::from($model->type)->badgeColor() }}">
                        {{ EmailType::from($model->type)->label() }}
                    </span>
                </td>

                <td>
                    <span class="badge bg-{{ EmailStatus::from($model->status)->badgeColor() }}">
                        {{ EmailStatus::from($model->status)->label() }}
                    </span>
                </td>

                <td>
                    {{ $model->created_at }}
                </td>

            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $models->links() }}
</div>