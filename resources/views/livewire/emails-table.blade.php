@php
    use Aparlay\Core\Models\Email;
    use Aparlay\Core\Models\Enums\EmailStatus;
    use Aparlay\Core\Models\Enums\EmailType;
@endphp

<div class="user-table">
    <div class="filters pb-3">
        <div class="row">
            <div class="col-md-6 pt-4">
                <h4>Emails</h4>
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

    <table class="table table-striped border bg-white">
        <thead>
        <tr>
            <th @class(['col-md-3', 'd-none' => $hiddenFields['username']])>
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'username'" :fieldLabel="'Username'" />
                    <input class="form-control" type="text" wire:model="filter.username"/>
                </div>
            </th>

            <th class="col-md-2">
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'to'" :fieldLabel="'to'" />
                    <input class="form-control" type="text" wire:model="filter.to"/>
                </div>
            </th>

            <th class="col-md-2">
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'type'" :fieldLabel="'Type'" />
                    <x-wire-dropdown-list
                        :wire-model="'filter.type'"
                        :options="\Aparlay\Core\Admin\Models\Email::getTypes()"
                    />
                </div>
            </th>
            <th class="col-md-1">
                <div>
                    <label for="">Status</label>

                    <x-wire-dropdown-list
                        :wire-model="'filter.status'"
                        :options="\Aparlay\Core\Admin\Models\Email::getStatuses()"
                    />
                </div>
            </th>

            <th class="col-md-2">
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'created_at'" :fieldLabel="'Created At'" />
                </div>
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
                    <span class="badge bg-{{ EmailType::from($model->type)->badgeColor() }}"
                          data-toggle="tooltip" data-placement="left" title="{{$model->error}}">
                        {{ EmailType::from($model->type)->label() }}
                    </span>
                </td>

                <td>
                    <span class="badge bg-{{ EmailStatus::from($model->status)->badgeColor() }}" data-slider-tooltip="">
                        {{ EmailStatus::from($model->status)->label() }}
                    </span>
                </td>

                <td>
                    {{ $model->created_at }}
                </td>

            </tr>
        @endforeach
        </thead>
    </table>
    {{ $models->links() }}
</div>
