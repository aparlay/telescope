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

            <th class="col-md-2">
                Created At
            </th>

            <th>
                Text
            </th>

            <th @class(['d-none' => !$hiddenFields['creator_username']])>
                Media
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
                    {{ $model->created_at  }}
                </td>

                <td>
                    {{ $model->text }}
                </td>

                <td @class(['d-none' => !$hiddenFields['creator_username']])>
                    <a href="{{ route('core.admin.media.view', (string) $model->_id) }}" class="btn btn-primary"><i class="fas fa-eye"></i></a>
                </td>

                <td>
                    <button class="btn btn-md btn-danger" type="button"
                            wire:click="$emit('showModal', 'modals.media-comment-delete-modal', '{{ (string)$model->id }}')">
                        Delete
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $models->links() }}
    </div>
</div>
