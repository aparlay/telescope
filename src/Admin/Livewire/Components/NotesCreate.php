<?php

namespace Aparlay\Core\Admin\Livewire\Components;

use Aparlay\Core\Admin\Livewire\Traits\CurrentUserTrait;
use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Services\NoteService;
use Livewire\Component;

class NotesCreate extends Component
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
            'message' => ['required', 'min:5'],
        ];
    }

    public function create()
    {
        $this->validate();

        if ($this->currentUser()->can('create notes')) {
            /** @var NoteService $noteService */
            $noteService = app()->make(NoteService::class);
            $user = User::find($this->userId);
            $noteService->addCustomNote($this->currentUser(), $user, $this->message);
            $this->message = '';
        }

        $this->emit('updateParent');
    }

    public function render()
    {
        return view('default_view::livewire.components.notes-create');
    }
}
