<div class="notes-table">

    <div class="filters pb-3">
        <div class="row">
            <div class="col-md-2 text-left">
                <label for="" style="color: #fff">
                    Create a new note
                </label>
                <button
                    class="btn btn-md btn-success"
                    type="button"
                    wire:click="$emit('showModal', 'modals.user-notes-create-modal', '{{ $userId }}')"
                >
                    Add New
                    <i class="fas fa-plus fa-xs"></i>
                </button>
            </div>

            <div class="col-md-3 ml-auto">
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
                    <div class="col">
                        <label for="">Per Page</label>
                        <x-wire-dropdown-list :wire-model="'perPage'" :show-any="false" :options="[5 => 5, 10 => 10, 15 => 15]"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<table class="table table-striped">
    <tbody>
    <tr>
        <th class="col-md-2">
            <div>

                <x-sortable-column-header :sort="$sort" :fieldName="'created_by'" :fieldLabel="'Created By'"/>
                <input class="form-control" type="text" wire:model="filter.creator_username"/>
            </div>
        </th>
        <th class="col-md-6">
            <div>
                <x-sortable-column-header :sort="$sort" :fieldName="'message'" :fieldLabel="'Notes'"/>
                <input class="form-control" type="text" wire:model="filter.message"/>
            </div>
        </th>
        <th class="col-md-3">
            <div>
                <x-sortable-column-header :sort="$sort" :fieldName="'created_at'" :fieldLabel="'Created At'"/>
            </div>
        </th>
        <th class="col-md-1">
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
                {!! $note->message !!}
            </td>
            <td>
                {{ $note->created_at }}
            </td>
            <td>
                <div>
                    <button
                        class="btn btn-sm btn-danger"
                        type="button"
                        wire:key="delete_note_{{ $note->_id }}}"
                        wire:click="$emit('showModal', 'modals.user-notes-delete-modal', '{{ $note->_id }}')"
                    >
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>

    @endforeach
    </tbody>
</table>
{{ $notes->links() }}
</div>
