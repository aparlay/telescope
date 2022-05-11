<?php

namespace Aparlay\Core\Admin\Livewire\Modals;

use Aparlay\Core\Admin\Livewire\Traits\CurrentUserTrait;
use Aparlay\Core\Admin\Models\Alert;
use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Repositories\UserRepository;
use Aparlay\Core\Models\Country;
use Aparlay\Core\Models\Enums\AlertStatus;
use Aparlay\Core\Models\Enums\AlertType;
use Aparlay\Core\Models\Enums\UserDocumentStatus;
use Aparlay\Core\Models\Enums\UserDocumentType;
use Aparlay\Core\Models\Enums\UserVerificationStatus;
use Aparlay\Core\Models\UserDocument;
use Aparlay\Core\Notifications\CreatorAccountApprovementNotification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Livewire\Component;
use MongoDB\BSON\ObjectId;

class UserVerificationModal extends Component
{
    use CurrentUserTrait;

    public $user;
    public $documents = [];

    public $documentsData;
    public $payoutCountry;


    public function mount($userId)
    {
        $this->user = User::find($userId);
        $this->payoutCountry = (string) $this->user->payout_country_id;

        $this->loadDocuments();

        foreach ($this->documents as $document) {
            $alert = $document->alertObjs()->latest()->first();
            $isRejected = $document->status === UserDocumentStatus::REJECTED->value;
            $this->documentsData[(string) $document->_id]['status'] = ! $isRejected ? UserDocumentStatus::APPROVED->value : UserDocumentStatus::REJECTED->value;
            $this->documentsData[(string) $document->_id]['reason'] = $alert ? $alert->reason : '';
        }
    }

    protected function rules()
    {
        return [
            'payoutCountry' => [
                'required'
            ],
            'documentsData.*.status' => [
                'required',
                Rule::in([UserDocumentStatus::REJECTED->value, UserDocumentStatus::APPROVED->value]),
            ],
            'documentsData.*.reason' => [
                'required_if:documentsData.*.status,'.UserDocumentStatus::REJECTED->value,
                'min:5',
            ],
        ];
    }

    public function messages()
    {
        return [
            'payoutCountry.required' => 'You must specify payout country',
            'documentsData.*.reason.required_if' => 'You must specify reject reason for this document',
            'documentsData.*.reason.min' => 'You must specify at least 5 characters',
        ];
    }

    private function loadDocuments()
    {
        $user = $this->user;
        $selfie = $user->userDocumentObjs()
            ->type(UserDocumentType::SELFIE->value)
            ->latest()
            ->first();

        $creditCard = $user->userDocumentObjs()
            ->type(UserDocumentType::ID_CARD->value)
            ->latest()
            ->first();

        $collection = new Collection();

        if ($creditCard) {
            $collection->push($creditCard);
        }

        if ($selfie) {
            $collection->push($selfie);
        }

        $this->documents = $collection;
    }

    public function save()
    {
        $this->validate();

        $user = $this->user;
        $payload = $approvedTypes = [];
        $docsApprovedCounter = 0;

        $user->payout_country_id = new ObjectId($this->payoutCountry);
        $user->save();

        foreach ($this->documentsData ?? [] as $documentId => $datum) {
            $document = $user->userDocumentObjs()->find($documentId);
            $document->status = (int) $datum['status'];
            $reason = $datum['reason'] ?? '';
            $isApproved = (int) $datum['status'] === UserDocumentStatus::APPROVED->value;

            if (! $isApproved) {
                Alert::create([
                    'created_by' => new ObjectId($this->currentUser()->_id),
                    'entity._id' => new ObjectId($document->_id),
                    'entity._type' => UserDocument::shortClassName(),
                    'status' => AlertStatus::NOT_VISITED->value,
                    'type' => AlertType::USER_DOCUMENT_REJECTED->value,
                    'reason' => $reason,
                ]);
            } else {
                $docsApprovedCounter++;
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

        if ($docsApprovedCounter === 2) {
            $newVerificationStatus = UserVerificationStatus::VERIFIED->value;
        } else {
            $newVerificationStatus = UserVerificationStatus::REJECTED->value;
        }

        $shouldSendNotification = false;

        $userRepository = new UserRepository(new User());

        if ($user->verification_status !== $newVerificationStatus) {
            $userRepository->updateVerificationStatus(
                $this->currentUser(),
                $this->user,
                $newVerificationStatus
            );
            $shouldSendNotification = true;
        }

        // remove approved types document from payload and send payload only if there is not any approved doc
        foreach ($approvedTypes as $type => $isApproved) {
            if ($isApproved && isset($payload[$type])) {
                unset($payload[$type]);
            }
        }

        if ($shouldSendNotification) {
            $message = match ((int) $newVerificationStatus) {
                UserVerificationStatus::REJECTED->value => 'Your Creator application has been reject! 😔',
                UserVerificationStatus::VERIFIED->value => 'Your Creator application has been approved! 🎉',
                default => ''
            };

            if ($message) {
                $user->notify(new CreatorAccountApprovementNotification($user, $message, $payload));
            }
        }

        $this->dispatchBrowserEvent('hideModal');
        $this->emit('updateParent');
    }

    public function render()
    {
        return view(
            'default_view::livewire.modals.user-verification-modal', [
                'countries' => Country::query()->get()
            ]
        );
    }
}
