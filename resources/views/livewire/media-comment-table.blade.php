@php
    use Aparlay\Payout\Models\Wallet;
    use Aparlay\Payout\Models\Enums\WalletStatus
@endphp

<div class="user-table">
    <div class="filters pb-3">
        <div class="row">
            <div class="col-md-6 pt-4">
                <h4>{{ $headerText }}</h4>
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
                <x-wire-dropdown-list :wire-model="'perPage'" :show-any="false"
                                      :options="[5 => 5, 10 => 10, 15 => 15]"/>
            </div>
        </div>
    </div>

    <table class="table table-striped">
        <thead>
        <tr>
            <th @class(['col-md-3', 'd-none' => $hiddenFields['creator_username']])>
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'creator_username'" :fieldLabel="'Creator'"/>
                    <input class="form-control" type="text" wire:model="filter.creator_username"/>
                </div>
            </th>

            <th>
                Text
            </th>

            <th class="col-md-2">
                Created At
            </th>

            <th class="col-md-1">
                Action
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($models as $model)
            <tr>
                <td @class(['d-none' => $hiddenFields['creator_username']])>
                    @if ($model->creatorObj)
                        <x-username-avatar :user="$model->creatorObj"/>
                    @endif
                </td>

                <td>
                    {{ $model->text }}
                </td>

                <td>
                    {{ $model->created_at  }}
                </td>

                <td>
                    <button class="btn btn-sm btn-danger" type="button" data-toggle="modal" data-target="#delete-media-comment-modal">
                        Delete
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $models->links() }}
</div>
