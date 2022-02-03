<?php

namespace Aparlay\Core\Admin\Livewire\Modals;

use Aparlay\Core\Admin\Livewire\Traits\CurrentUserTrait;
use Aparlay\Core\Admin\Models\Note;
use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Services\NoteService;
use Aparlay\Core\Models\Enums\NoteType;
use Illuminate\Validation\Rule;
use Livewire\Component;

class UserNoteCreateModal extends Component
{
    use CurrentUserTrait;

    public $userId;
    public $message;

    public function mount($userId)
    {
        $this->userId = $userId;
    }

    protected function rules()
    {
        return [
            'message' => ['required', 'min:10'],
        ];
    }

    public function create()
    {
        $this->validate();

        /** @var NoteService $noteService */
        $noteService = app()->make(NoteService::class);
        $user = User::find($this->userId);
        $create = $noteService->addCustomNote($this->currentUser(), $user, $this->message);

        $this->dispatchBrowserEvent('hideModal');
        $this->emit('updateParent');
    }

    public function render()
    {
        return view('default_view::livewire.modals.user-notes-create-modal');
    }
}
