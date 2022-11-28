@php
    use Aparlay\Core\Models\Enums\NoteCategory;
@endphp

<div class="">
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
                        <span class="badge bg-{{ NoteCategory::from($note->category)->badgeColor() }}">
                            {{ NoteCategory::from($note->category)->label() }}
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
