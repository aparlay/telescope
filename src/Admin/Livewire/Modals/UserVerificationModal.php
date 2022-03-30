<?php

namespace Aparlay\Core\Admin\Livewire\Modals;

use Aparlay\Core\Admin\Livewire\Traits\CurrentUserTrait;
use Aparlay\Core\Admin\Models\Alert;
use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Repositories\UserRepository;
use Aparlay\Core\Models\Enums\AlertStatus;
use Aparlay\Core\Models\Enums\AlertType;
use Aparlay\Core\Models\Enums\UserDocumentStatus;
use Aparlay\Core\Models\Enums\UserVerificationStatus;
use Aparlay\Core\Models\UserDocument;
use Aparlay\Core\Notifications\CreatorAccountApprovementNotification;
use Illuminate\Validation\Rule;
use Livewire\Component;
use MongoDB\BSON\ObjectId;

class UserVerificationModal extends Component
{
    use CurrentUserTrait;

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
        $this->documents = $user->userDocumentObjs()->latest()->get() ?? [];
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
            'documentsData.*.is_approved' => ['required', 'boolean'],
            'documentsData.*.reason' => [
                'required_if:documentsData.*.is_approved,false',
                'min:5',
            ],
        ];
    }

    public function messages()
    {
        return [
            'documentsData.*.reason.required_if' => 'You must specify reject reason for this document',
            'documentsData.*.reason.min' => 'You must specify at least 5 characters',
        ];
    }

    public function save()
    {
        $this->validate();

        $this->userRepository = new UserRepository(new User());
        $shouldSendNotification = $this->user->verification_status != $this->verification_status;
        $this->userRepository->updateVerificationStatus(
            $this->currentUser(),
            $this->user,
            (int) $this->verification_status
        );

        $user = $this->userRepository->find($this->selectedUser);

        $payload = $approvedTypes = [];
        foreach ($this->documentsData ?? [] as $documentId => $datum) {
            $document = $user->userDocumentObjs()->find($documentId);

            $isApproved = $datum['is_approved'] ?? false;

            $status = match ($isApproved) {
                true => UserDocumentStatus::APPROVED->value,
                false => UserDocumentStatus::REJECTED->value
            };
            $document->status = $status;
            $reason = $datum['reason'] ?? '';

            if (! $isApproved) {
                Alert::create([
                    'created_by' => new ObjectId($this->currentUser()->_id),
                    'entity._id' => new ObjectId($document->_id),
                    'entity._type' => UserDocument::shortClassName(),
                    'status' => AlertStatus::NOT_VISITED->value,
                    'type' => AlertType::USER_DOCUMENT_REJECTED->value,
                    'reason' => $reason,
                ]);
            }

            // check if the given type has any approved document
            $approvedTypes[$document->type] = ($approvedTypes[$document->type] ?? $isApproved) || $isApproved;

            $payload[$document->type] = [
                'user_document_id' => (string) $document->_id,
                'reason' => $reason,
                'type' => $document->type,
                'type_label' => $document->type_label,
            ];

            $document->save();
        }

        // remove approved types document from payload and send payload only if there is not any approved doc
        foreach ($approvedTypes as $type => $isApproved) {
            if ($isApproved && isset($payload[$type])) {
                unset($payload[$type]);
            }
        }

        if ($shouldSendNotification) {
            $message = match ((int) $this->verification_status) {
                UserVerificationStatus::UNDER_REVIEW->value => 'We have received your application and will review it shortly.',
                UserVerificationStatus::REJECTED->value => 'Your Creator application has been reject! 😔',
                UserVerificationStatus::VERIFIED->value => 'Your Creator application has been approved! 🎉',
                default => ''
            };

            $user->notify(new CreatorAccountApprovementNotification($user, $message, $payload));
        }

        $this->dispatchBrowserEvent('hideModal');
        $this->emit('updateParent');
    }

    public function render()
    {
        return view('default_view::livewire.modals.user-verification-modal');
    }
}
