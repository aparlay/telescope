@php
    use Aparlay\Payment\Admin\Models\CreditCard;
    use Aparlay\Payment\Models\Enums\CreditCardStatus;
@endphp

<div class="credit-card-table">
    <div class="filters pb-3">
        <div class="row">

            <div class="col-md-9 pt-4">
                <h4>Credit Cards</h4>
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
            <th class="col-md-2">
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'holder_name'" :fieldLabel="'Holder Name'" />
                    <input class="form-control" type="text" wire:model="filter.holder_name"/>
                </div>
            </th>
            <th class="col-md-2">
                <div>
                    <label for="">Brand</label>
                    <x-wire-dropdown-list :wire-model="'filter.card_brand'" :options="CreditCard::getCardBrands()"/>
                </div>
            </th>
            <th class="col-md-1">
                <div>
                    <label for="">Status</label>
                    <x-wire-dropdown-list :wire-model="'filter.status'" :options="CreditCard::getStatuses()"/>
                </div>
            </th>
            <th class="col-md-2">
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'expire_year'" :fieldLabel="'Exp. Year'" />
                    <x-wire-dropdown-list
                            :wire-model="'filter.expire_year'"
                            :options="['20' => '20', '21' => '21', '22' => '22', '23' => '23', '24' => '24', '25' => '25', '26' => '26', '27' => '27', '28' => '28', '29' => '29', '30' => '30', '31' => '31']"
                    />
                </div>
            </th>
            <th class="col-md-2">
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'expire_month'" :fieldLabel="'Exp. Month'" />
                    <x-wire-dropdown-list
                            :wire-model="'filter.expire_month'"
                            :options="['01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', '10' => '10', '11' => '11', '12' => '12']"
                    />
                </div>
            </th>

            <th class="col-md-2">
                <x-sortable-column-header :sort="$sort" :fieldName="'created_at'" :fieldLabel="'Reg. Date'" />
            </th>

            <th class="col-md-1">
                <div>
                    <label for="">Edit</label>
                </div>
            </th>
        </tr>

        @foreach($creditCards as $creditCard)
            <tr>
                <td>
                    {{ $creditCard->holder_name }}
                </td>
                <td>
                    {{ $creditCard->card_brand }}
                </td>
                <td>
                    <span class="badge bg-{{ CreditCardStatus::from($creditCard->status)->badgeColor() }}">
                        {{ CreditCardStatus::from($creditCard->status)->label() }}
                    </span>
                </td>
                <td>
                    {{ $creditCard->expire_year }}
                </td>
                <td>
                    {{ $creditCard->expire_month }}
                </td>

                <td>
                    {{ $creditCard->created_at }}
                </td>


                <td>
                    <div class="col-md-6">
                        <div>
                            <a class="btn btn-success" href="{{$creditCard->admin_url}}">
                                <i title="Profile" class="fa fa-user-circle"></i>
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $creditCards->links() }}
</div>
