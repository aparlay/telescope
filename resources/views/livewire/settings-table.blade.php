@php
    use Aparlay\Core\Helpers\ActionButtonBladeComponent;
@endphp
<div class="settings-table">
    <div class="filters pb-3">
        <div class="row">
            <div class="col-md-3">
                <label for="">Text Search</label>
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


            <div class="col-md-1  ml-auto">
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
                    <x-sortable-column-header :sort="$sort" :fieldName="'group'" :fieldLabel="'Group'" />
                    <input class="form-control" type="text" wire:model="filter.group"/>
                </div>
            </th>
            <th class="col-md-2">
                <div> 
                    <x-sortable-column-header :sort="$sort" :fieldName="'title'" :fieldLabel="'Title'" />
                    <input class="form-control" type="text" wire:model="filter.title"/>
                </div>  
            </th>

            <th class="col-md-2">
                <div> 
                    <label>Value</label>
                </div>
            </th>

            <th class="col-md-2">
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'created_at'" :fieldLabel="'Created At'" />
                </div>
            </th>
            
            <th class="col-md-6">
            </th>
            
        </tr>

        @foreach($settings as $setting)
            <tr>
              
                <td>
                   {{$setting->group}}
                </td>

                <td>
                    {{$setting->title}}
                </td>

                <td>
                    {{isset($setting->value) ? json_encode($setting->value) : 'not set'}}
                </td>

                <td>
                    {{$setting->created_at}}
                </td>
                <td>
                    <div>
                        {!! ActionButtonBladeComponent::getViewDeleteActionButton($setting->_id,'setting')!!}
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $settings->links() }}
</div>
