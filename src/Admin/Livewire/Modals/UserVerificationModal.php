<?php

namespace Aparlay\Core\Admin\Livewire\Modals;


use Aparlay\Core\Models\Enums\UserDocumentStatus;
use Aparlay\Core\Models\Enums\UserVerificationStatus;
use App\Models\User;
use Livewire\Component;


class UserVerificationModal extends Component
{
    public $selectedUser;
    public $action;
    public $modalTitle;
    public $reason = '';


    public function mount($userId, $action)
    {
        $this->selectedUser = $userId;
        $this->action = $action;

        \Log::info($action);



        $this->modalTitle = match ($action) {
            'markAsRejected' => 'Mark this user as rejected?',
            'markAsVerified' => 'Mark this user as verified?'
        };
    }


    public function markAsRejected()
    {
        $user = User::query()->find($this->selectedUser);
        $user->verification_status = UserVerificationStatus::REJECTED->value;
        $user->save();

        $user->userDocumentObjs()->update([
            'status' => UserDocumentStatus::REJECTED
        ]);

        $this->dispatchBrowserEvent('hideModal');
        $this->emit('updateParent');
    }


    /**
     * @param $userId
     * @return void
     */
    public function markAsVerified()
    {
        /** @var User $user */
        $user = User::query()->find($this->selectedUser);
        $user->verification_status = UserVerificationStatus::VERIFIED->value;
        $user->userDocumentObjs()->update([
            'status' => UserDocumentStatus::APPROVED
        ]);
        $user->save();

        $this->dispatchBrowserEvent('hideModal');
        $this->emit('updateParent');
    }

    public function render()
    {
        return view('default_view::livewire.modals.user-verification-modal');
    }

}
