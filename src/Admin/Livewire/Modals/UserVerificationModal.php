<?php

namespace Aparlay\Core\Admin\Livewire\Modals;

use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Repositories\UserRepository;
use Aparlay\Core\Models\Enums\UserVerificationStatus;
use Livewire\Component;

class UserVerificationModal extends Component
{
    public $selectedUser;
    public $verification_status;
    public $documents;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function mount($userId)
    {
        $this->userRepository = new UserRepository(new User());
        $user = $this->userRepository->find($userId);
        $this->documents = $user->userDocumentObj;
        $this->selectedUser = $userId;
        $this->verification_status = $user->verification_status;
    }

    public function save()
    {
        $this->userRepository = new UserRepository(new User());
        $this->userRepository->updateVerificationStatus(
            $this->selectedUser,
            $this->verification_status
        );
        $this->dispatchBrowserEvent('hideModal');
        $this->emit('updateParent');
    }

    public function render()
    {
        return view('default_view::livewire.modals.user-verification-modal');
    }
}
