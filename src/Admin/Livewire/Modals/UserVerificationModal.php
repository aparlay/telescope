<?php

namespace Aparlay\Core\Admin\Livewire\Modals;

use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Repositories\AlertRepository;
use Aparlay\Core\Admin\Repositories\UserRepository;
use Aparlay\Core\Admin\Models\Alert;
use Aparlay\Core\Models\Enums\AlertStatus;
use Aparlay\Core\Models\Enums\AlertType;
use Aparlay\Core\Models\Enums\UserDocumentStatus;
use Illuminate\Validation\Rule;
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
        $this->selectedUser = $userId;

        $this->userRepository = new UserRepository(new User());
        $user = $this->userRepository->find($userId);
        $this->user = $user;
        $this->documents = $user->userDocumentObjs ?? [];
        $this->verification_status = $user->verification_status;

        foreach ($this->documents as $document) {
            $alert = $document->alertObjs()->latest()->first();
            $this->documentsData[$document->_id]['is_approved'] = $document->status === UserDocumentStatus::APPROVED->value;
            $this->documentsData[$document->_id]['reason'] = $alert ? $alert->reason : '';
        }
    }

    protected function rules()
    {
        return [
            'verification_status' => Rule::in(array_keys(User::getVerificationStatuses())),
            'documentsData' => ['array'],
            'documentsData.*.reason' => [
                'required', 'min:5'
            ],
        ];
    }

    public function messages()
    {
        return [
            'documentsData.*.reason.required' => 'You must specify reject reason for this document',
        ];
    }

    public function save()
    {
        $this->validate();

        $this->userRepository = new UserRepository(new User());
        $alertRepository = new AlertRepository(new Alert());

        $this->userRepository->updateVerificationStatus(
            $this->selectedUser,
            $this->verification_status
        );

        $user = $this->userRepository->find($this->selectedUser);


        foreach ($this->documentsData ?? [] as $documentId => $datum) {
            $document = $user->userDocumentObjs()->find($documentId);

            $isApproved = $datum['is_approved'] ?? false;

            $status = match ($isApproved) {
                true => UserDocumentStatus::APPROVED->value,
                false => UserDocumentStatus::REJECTED->value
            };
            $document->status = $status;
            $reason = $datum['reason'] ?? '';

            if (!$isApproved && $reason) {
                $alertRepository->firstOrCreate([
                    'status' => AlertStatus::NOT_VISITED->value,
                    'type' => AlertType::USER_DOCUMENT_REJECTED->value,
                    'user_document_id' => $document->_id,
                    'reason' => $reason,
                ]);
            }

            $document->save();
        }

        $this->dispatchBrowserEvent('hideModal');
        $this->emit('updateParent');
    }

    public function render()
    {
        return view('default_view::livewire.modals.user-verification-modal');
    }
}
