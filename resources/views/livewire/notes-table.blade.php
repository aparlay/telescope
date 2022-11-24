<div class="notes-table user-profile-table">
    <div class="pb-2">
        <div class="row">
            <div class="col-2 col-lg-1 pt-sm-2 pl-sm-3">
                <span class="h5">Notes</span>
            </div>

            <div class="col-10 col-lg-11">
                <livewire:notes-create :userId="$userId"/>
            </div>
        </div>
    </div>

<!--
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link py-1 active" aria-current="page" href="#">All</a>
        </li>
        <li class="nav-item">
            <a class="nav-link py-1" href="#">Notes</a>
        </li>
        <li class="nav-item">
            <a class="nav-link py-1" href="#">Logs</a>
        </li>
    </ul>
-->

    <table class="table table-striped border">
        <thead>
            <tr class="d-flex">
                <th class="col-1 col-md-1 col-sm-2">
                    <x-sortable-column-header :sort="$sort" :fieldName="'created_by'" :fieldLabel="'Created By'"/>
                </th>
                <th class="col-8 col-md-8 col-sm-6">
                    <x-sortable-column-header :sort="$sort" :fieldName="'message'" :fieldLabel="'Notes'"/>
                </th>
                <th class="col-2 col-md-2 col-sm-3">
                    <x-sortable-column-header :sort="$sort" :fieldName="'created_at'" :fieldLabel="'Created At'"/>
                </th>
                <th class="col-1 col-md-1 text-right">
                    <label for="">Action</label>
                </th>
            </tr>
        </thead>

        <tbody>
            @foreach($notes as $note)
                <tr class="d-flex">
                    <td class="col-1 col-md-1 col-sm-2">
                        <a href="{{ $note->creatorObj->admin_url }}" target="_blank">
                            {{ $note->creator['username'] }}
                        </a>
                    </td>
                    <td class="col-8 col-md-8 col-sm-6">
                        <span class="badge bg-{{ \Aparlay\Core\Models\Enums\NoteType::from($note->type)->label() == 'other' ? 'warning' : 'secondary' }}">
                            {{ \Aparlay\Core\Models\Enums\NoteType::from($note->type)->label() == 'other' ? 'note' : 'log' }}
                        </span>
                        {!! $note->message !!}
                    </td>
                    <td class="col-2 col-md-2 col-sm-3">
                        {{ $note->created_at }}
                    </td>
                    <td class="col-1 text-right">
                        <a
                            class="text-red"
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
