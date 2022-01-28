@php
    use Aparlay\Core\Helpers\ActionButtonBladeComponent;
@endphp
<div class="notes-table">
    <div class="filters pb-3">
        <div class="row">
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
                <select class="form-control" wire:model="perPage">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                </select>
            </div>

        </div>
    </div>

    <table class="table table-striped">
        <tbody>
        <tr>
            <th class="col-md-3">
                <div>
                   
                    <x-sortable-column-header :sort="$sort" :fieldName="'created_by'" :fieldLabel="'Created By'" />
                    <input class="form-control" type="text" wire:model="filter.created_by"/>    
                </div>
            </th>
            <th class="col-md-3">
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'created_at'" :fieldLabel="'Created At'" />
                    <input class="form-control" type="text" wire:model="filter.created_at"/>
                </div>
            </th>
        
            <th class="col-md-3">
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'message'" :fieldLabel="'Notes'" />
                </div>
            </th>
            <th class="col-md-3">
                <div>
                    <label for="">Action</label>

                </div>
            </th>
            <th></th>
        </tr>

        @foreach($notes as $note)
            <tr>
                <td>
                    <a href="">
                      
                    {{ $note->creator['username'] }}
                       
                    </a>
                </td>
                <td>
                    {{ $note->created_at }}
                </td>
                <td>
                    {{ $note->message }}
                </td>
                <td>
                    <div>
                        {!! ActionButtonBladeComponent::deleteActionButton($note->_id,'note')!!}
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $notes->links() }}
</div>
