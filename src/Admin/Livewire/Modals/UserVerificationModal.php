<?php

namespace Aparlay\Core\Admin\Livewire\Modals;

use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Repositories\UserRepository;
use Livewire\Component;

class UserVerificationModal extends Component
{
    public $selectedUser;
    public $action;
    public $modalTitle;
    public $reject_reason = '';

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function mount($userId, $action)
    {
        $this->selectedUser = $userId;
        $this->action = $action;
        $this->modalTitle = match ($action) {
            'markAsRejected' => 'Mark this user as rejected?',
            'markAsVerified' => 'Mark this user as verified?'
        };
    }

    public function markAsRejected()
    {
        $this->userRepository = new UserRepository(new User());
        $this->userRepository->markAsRejected($this->selectedUser, $this->reject_reason);
        $this->dispatchBrowserEvent('hideModal');
        $this->emit('updateParent');
    }

    /**
     * @param $userId
     * @return void
     */
    public function markAsVerified()
    {
        $this->userRepository = new UserRepository(new User());
        $this->userRepository->markAsVerified($this->selectedUser);
        $this->dispatchBrowserEvent('hideModal');
        $this->emit('updateParent');
    }

    public function render()
    {
        return view('default_view::livewire.modals.user-verification-modal');
    }
}
