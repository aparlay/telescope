<div class="notes-table user-profile-table">
    <div class="filters pb-2">
        <div class="row">
            <div class="col-md-1 pt-md-2 pl-md-3">
                <span class="h5">Notes</span>
            </div>

            <div class="col-md-10">
                <input class="form-control" type="text" placeholder="filter notes" wire:model="filter.message"/>
            </div>
            <div class="col-md-1">
                <button class="btn btn-success w-100" type="button"
                    wire:click="$emit('showModal', 'modals.user-notes-create-modal', '{{ $userId }}')">
                    New
                </button>
            </div>
        </div>
    </div>

    <table class="table table-striped">
        <tbody>
            <tr>
                <th class="col-md-1">
                    <x-sortable-column-header :sort="$sort" :fieldName="'created_by'" :fieldLabel="'Created By'"/>
                </th>
                <th class="col-md-8">
                    <x-sortable-column-header :sort="$sort" :fieldName="'message'" :fieldLabel="'Notes'"/>
                </th>
                <th class="col-md-2">
                    <x-sortable-column-header :sort="$sort" :fieldName="'created_at'" :fieldLabel="'Created At'"/>
                </th>
                <th class="col-md-1">
                    <label for="">Action</label>
                </th>
            </tr>

            @foreach($notes as $note)
                <tr>
                    <td>
                        <a href="{{ $note->creatorObj->admin_url }}" target="_blank">
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
                            <a
                                class=""
                                wire:key="delete_note_{{ $note->_id }}}"
                                wire:click="$emit('showModal', 'modals.user-notes-delete-modal', '{{ $note->_id }}')"
                            >
                                <i class="fa fa-fw fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>

            @endforeach
        </tbody>
    </table>
    {{ $notes->links() }}
</div>
