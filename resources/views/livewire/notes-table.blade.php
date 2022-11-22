<div class="notes-table user-profile-table">
    <div class="pb-2">
        <div class="row">
            <div class="col-md-1 pt-md-2 pl-md-3">
                <span class="h5">Notes</span>
            </div>

            <div class="col-md-11">
                <livewire:notes-create :userId="$userId"/>
            </div>
        </div>
    </div>

    <table class="table table-striped">
        <thead>
            <tr class="d-flex">
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
        </thead>
        <tbody>

            @foreach($notes as $note)
                <tr class="d-flex">
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
                        <a
                            class=""
                            wire:key="delete_note_{{ $note->_id }}}"
                            wire:click="$emit('showModal', 'modals.user-notes-delete-modal', '{{ $note->_id }}')"
                        >
                            <i class="fa fa-fw fa-trash"></i>
                        </a>
                    </td>
                </tr>

            @endforeach
        </tbody>
    </table>
    {{ $notes->links() }}
</div>
