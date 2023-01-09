@php
    use Aparlay\Core\Models\Email;
    use Aparlay\Core\Models\Enums\EmailStatus;
    use Aparlay\Core\Models\Enums\EmailType;
@endphp

<div class="user-table user-profile-table">
    <div class="filters">
        <div class="row">
            <div @class(['col-md-6 mb-2', 'd-none' => $hiddenFields['username']])>
                <input class="form-control" type="text" placeholder="filter creator" wire:model="filter.username"/>
            </div>
            <div @class(['col-md-6 mb-2'])>
                <input class="form-control" type="text" placeholder="filter email address" wire:model="filter.to"/>
            </div>
        </div>
    </div>

    <table class="table table-striped border bg-white">
        <thead>
        <tr>
            <th @class(['col-md-3', 'd-none' => $hiddenFields['username']])>
                <x-sortable-column-header :sort="$sort" :fieldName="'username'" :fieldLabel="'Username'"/>
            </th>

            <th class="col-md-2">
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'to'" :fieldLabel="'to'"/>
                </div>
            </th>

            <th class="col-md-1">
                <label for="">Type</label>
                <x-wire-dropdown-list
                        :wire-model="'filter.type'"
                        :options="\Aparlay\Core\Admin\Models\Email::getTypes()"
                />
            </th>

            <th class="col-md-1">
                <span for="">Server</span>
            </th>

            <th class="col-md-1">
                <label for="">Status</label>
            </th>

            <th class="col-md-2">
                <x-sortable-column-header :sort="$sort" :fieldName="'created_at'" :fieldLabel="'Created At'"/>
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
                    @endif
                </td>

                <td>
                        <span class="badge bg-{{ EmailType::from($model->type)->badgeColor() }}">
                            {{ EmailType::from($model->type)->label() }}
                        </span>
                </td>

                <td>
                    <span @class(['badge', 'bg-success' => $model->server === 'mail1', 'bg-warning' => $model->server === 'mail2']) >
                        {{ $model->server }}
                    </span>
                </td>

                <td>
                        <span class="badge bg-{{ EmailStatus::from($model->status)->badgeColor() }}"
                              data-toggle="tooltip" data-placement="left" title="{{ $model->error }}">
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
    <div class="d-flex justify-content-center">
        {{ $models->links() }}
    </div>
</div>
@push('js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip()
        });
    </script>
@endpush
