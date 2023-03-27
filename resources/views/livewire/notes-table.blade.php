@php
    use Aparlay\Core\Models\Enums\NoteCategory;
@endphp

<div>
    <table class="table table-responsive w-100 d-table table-striped border">
        <tbody>
            @foreach($notes as $note)
                <tr>
                    <td class="col-8 col-md-8 col-sm-6">
                        <span class="badge bg-{{ NoteCategory::from($note->category)->badgeColor() }}">
                            {{ NoteCategory::from($note->category)->label() }}
                        </span>
                        {!! $note->message !!}
                    </td>
                    <td class="d-none d-md-table-cell col-md-2 col-sm-3">
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
