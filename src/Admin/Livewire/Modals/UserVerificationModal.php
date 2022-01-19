<?php

namespace Aparlay\Core\Admin\Livewire\Modals;

use Aparlay\Core\Models\Enums\UserVerificationStatus;
use App\Models\User;
use Livewire\Component;

class UserVerificationModal extends Component
{
    public $selectedUser = null;
    public $action;
    public $modalTitle;

    public function mount($userId, $action)
    {
        $this->selectedUser = $userId;
        $this->action = $action;

        $this->modalTitle = 'Mark this user as approved?';
    }

    public function markAsRejected()
    {
        $user = User::query()->find($this->selectedUser);
        $user->verification_status = UserVerificationStatus::REJECTED->value;
        $user->save();
        $this->dispatchBrowserEvent('resetModal');
        $this->dispatchBrowserEvent('hideModal');
        $this->dispatchBrowserEvent('hidden.bs.modal');
    }

    public function markAsVerified()
    {
        $user = User::query()->find($this->selectedUser);
        $user->verification_status = UserVerificationStatus::VERIFIED->value;
        $user->save();
        $this->dispatchBrowserEvent('resetModal');
        $this->dispatchBrowserEvent('hideModal');
        $this->dispatchBrowserEvent('hidden.bs.modal');
    }

    public function render()
    {
        return view('default_view::livewire.modals.user-verification-modal');
    }
}
