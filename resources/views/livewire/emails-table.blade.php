@php
    use Aparlay\Core\Models\Email;
    use Aparlay\Core\Models\Enums\EmailStatus;
    use Aparlay\Core\Models\Enums\EmailType;
@endphp

<div class="user-table user-profile-table">
    <div class="filters">
        <div class="row">
            <div @class(['col-md-12 mb-2', 'd-none' => $hiddenFields['username']])>
                <input class="form-control" type="text" placeholder="filter creator" wire:model="filter.username"/>
            </div>
        </div>
    </div>

    <table class="table table-striped border bg-white">
        <thead>
            <tr>
                <th @class(['col-md-3', 'd-none' => $hiddenFields['username']])>
                    <x-sortable-column-header :sort="$sort" :fieldName="'username'" :fieldLabel="'Username'" />
                </th>

                <th class="col-md-4">
                    <x-sortable-column-header :sort="$sort" :fieldName="'email'" :fieldLabel="'Email'" />
                </th>

                <th class="col-md-2">
                    <x-sortable-column-header :sort="$sort" :fieldName="'type'" :fieldLabel="'Type'" />
                </th>

                <th class="col-md-1">
                    <label for="">Status</label>
                </th>

                <th class="col-md-2">
                    <x-sortable-column-header :sort="$sort" :fieldName="'created_at'" :fieldLabel="'Created At'" />
                </th>
            </tr>
        </thead>
        <tbody>
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
                                {{ $model->to }}
                            </a>
                        @else
                            <a href="mailto:{{$model->to}}">
                                {{ $model->to }}
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
