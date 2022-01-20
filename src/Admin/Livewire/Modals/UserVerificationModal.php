<?php

namespace Aparlay\Core\Admin\Livewire\Modals;

use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Repositories\UserRepository;
use Aparlay\Core\Models\Enums\UserDocumentStatus;
use Aparlay\Core\Models\Enums\UserVerificationStatus;
use AWS\CRT\Log;
use Livewire\Component;

class UserVerificationModal extends Component
{
    public $selectedUser;
    public $verification_status;
    public $documents = [];
    public $user;
    public $documentsData;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function mount($userId)
    {
        $this->userRepository = new UserRepository(new User());
        $user = $this->userRepository->find($userId);
        $this->user = $user;
        $this->documents = $user->userDocumentObjs ?? [];
        $this->selectedUser = $userId;
        $this->verification_status = $user->verification_status;

        foreach ($this->documents as $document) {
            $this->documentsData[$document->_id]['is_approved'] = $document->status === UserDocumentStatus::APPROVED->value;
            $this->documentsData[$document->_id]['reject_reason'] = $document->reject_reason;
        }
    }

    public function save()
    {
        $this->userRepository = new UserRepository(new User());
        $this->userRepository->updateVerificationStatus(
            $this->selectedUser,
            $this->verification_status
        );

        $user = $this->userRepository->find($this->selectedUser);

        foreach ($this->documentsData ?? [] as $documentId => $datum) {
            $document = $user->userDocumentObjs()->find($documentId);
            $rejectReason = $datum['reject_reason'] ?? '';
            $isApproved = $datum['is_approved'] ?? false;
            $status = match ($isApproved) {
                true => UserDocumentStatus::APPROVED->value,
                false => UserDocumentStatus::REJECTED->value
            };
            $document->reject_reason = $rejectReason;
            $document->status = $status;
            $document->save();
        }

        $this->dispatchBrowserEvent('hideModal');
        $this->emit('updateParent');

        session()->flash('message', 'User was successfully updated.');
    }

    public function render()
    {
        return view('default_view::livewire.modals.user-verification-modal');
    }
}
