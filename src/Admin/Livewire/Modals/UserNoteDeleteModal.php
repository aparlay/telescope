<?php

namespace Aparlay\Core\Admin\Livewire\Modals;

use Aparlay\Core\Admin\Livewire\Traits\CurrentUserTrait;
use Aparlay\Core\Admin\Models\Alert;
use Aparlay\Core\Admin\Models\Note;
use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Repositories\AlertRepository;
use Aparlay\Core\Admin\Repositories\UserRepository;
use Aparlay\Core\Constants\Roles;
use Aparlay\Core\Models\Enums\AlertStatus;
use Aparlay\Core\Models\Enums\AlertType;
use Aparlay\Core\Models\Enums\UserDocumentStatus;
use Illuminate\Validation\Rule;
use Livewire\Component;

class UserNoteDeleteModal extends Component
{
    use CurrentUserTrait;

    public $selectedItem;
    public $note;


    public function mount($noteId)
    {
        $this->selectedItem = $noteId;
        $this->note = Note::find($noteId);
    }

    public function delete()
    {
        $currentUser = $this->currentUser();
        if ($currentUser->can('delete notes')) {
            $userNote = Note::find($this->selectedItem);
            $userNote->delete();
        }

        $this->dispatchBrowserEvent('hideModal');
        $this->emit('updateParent');
    }

    public function render()
    {
        return view('default_view::livewire.modals.user-notes-delete-modal');
    }
}
