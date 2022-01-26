@php
    use Aparlay\Payment\Models\Order;
    use Aparlay\Payment\Models\Enums\OrderEntity;
    use Aparlay\Payment\Models\Enums\OrderStatus;
@endphp

<div class="user-table">
    <div class="filters pb-3">
        <div class="row">
            <div class="col-md-3">
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
                    <x-sortable-column-header :sort="$sort" :fieldName="'creator_username'" :fieldLabel="'Creator'" />
                    <input class="form-control" type="text" wire:model="filter.creator_username"/>
                </div>
            </th>
            <th class="col-md-2">
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'entity'" :fieldLabel="'Entity'" />

                    <x-wire-dropdown-list
                        :wire-model="'filter.entity'"
                        :options="Order::getEntities()"
                    />
                </div>
            </th>
            <th class="col-md-2">
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'status'" :fieldLabel="'Status'" />

                    <x-wire-dropdown-list
                        :wire-model="'filter.status'"
                        :options="Order::getStatuses()"
                    />
                </div>
            </th>
            <th class="col-md-2">
                <x-sortable-column-header :sort="$sort" :fieldName="'currency'" :fieldLabel="'Currency'" />
            </th>
            <th class="col-md-2">
                <x-sortable-column-header :sort="$sort" :fieldName="'amount'" :fieldLabel="'Amount'" />
            </th>
            <th class="col-md-2">
                <x-sortable-column-header :sort="$sort" :fieldName="'created_at'" :fieldLabel="'Created at'" />
            </th>
            <th>
                Action
            </th>
        </tr>

        @foreach($orders as $order)
            <tr>
                <td>
                    <x-username-avatar :user="$order->creatorObj"/>
                </td>
                <td>
                    <span class="badge badge-{{ OrderEntity::from($order->entity)->badgeColor() }}">
                        {{ OrderEntity::from($order->entity)->label() }}
                    </span>
                </td>
                <td>
                    <span class="badge badge-{{ OrderStatus::from($order->status)->badgeColor() }}">
                        {{ OrderStatus::from($order->status)->label() }}
                    </span>
                </td>
                <td>
                    @if ($order->currency)
                        {{ $order->currency }}
                    @else
                        <span class="badge badge-danger">(not set)</span>
                    @endif
                </td>

                <td>
                    {{ $order->amount ?? 0 }}
                </td>
                <td>
                    {{ $order->created_at  }}
                </td>
                <td>
                    <a class="btn btn-info" href="{{$order->getAdminUrlAttribute()}}">
                        <i class="fa fa-eye"></i> View
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $orders->links() }}
</div>
