<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Dto\UserDocumentDto;
use Aparlay\Core\Api\V1\Traits\HasUserTrait;
use Aparlay\Core\Api\V1\Traits\ValidationErrorTrait;
use Aparlay\Core\Constants\StorageType;
use Aparlay\Core\Jobs\DeleteFileJob;
use Aparlay\Core\Jobs\UploadFileJob;
use Aparlay\Core\Models\Enums\UserDocumentStatus;
use Aparlay\Core\Models\Enums\UserDocumentType;
use Aparlay\Core\Models\Enums\UserVerificationStatus;
use Aparlay\Core\Models\UserDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use MongoDB\BSON\ObjectId;

class UserDocumentService extends AbstractService
{
    use HasUserTrait;
    use ValidationErrorTrait;


    /**
     * @var UploadFileService
     */
    private $uploadFileService;

    public function __construct(
        UploadFileService $uploadFileService
    ) {
        $this->uploadFileService = $uploadFileService;
    }

    public function index()
    {
        $documents = UserDocument::creator($this->getUser()->_id)
            ->with('alertObjs')
            ->oldest('created_at')
            ->get();

        $output = [];

        foreach ($documents as $item) {
            $output[$item['type']] = $item;
        }

        return array_values($output);
    }

    public function changeToPending()
    {
        $idCard = UserDocument::query()
            ->creator($this->getUser()->_id)
            ->type(UserDocumentType::ID_CARD->value)
            ->status(UserDocumentStatus::CREATED->value)
            ->first();

        $videoSelfie = UserDocument::query()
            ->creator($this->getUser()->_id)
            ->type(UserDocumentType::VIDEO_SELFIE->value)
            ->status(UserDocumentStatus::CREATED->value)
            ->first();

        if ($this->getUser()->is_tier3 && (!$videoSelfie || !$idCard)) {
            abort(423, __('You need to upload both documents: video selfie and id card photo at first'));
        }

        if (!$idCard) {
            abort(423, __('You need to upload id card at first'));
        }

        UserDocument::query()
            ->creator($this->getUser()->_id)
            ->status(UserDocumentStatus::CREATED->value)
            ->update(['status' => UserDocumentStatus::PENDING->value]);

        $this->getUser()->verification_status = UserVerificationStatus::PENDING->value;
        $this->getUser()->save();

        return $this->getUser();
    }


    /**
     * @param  UserDocumentDto  $documentDto
     * @return \Aparlay\Core\Api\V1\Models\UserDocument|\Illuminate\Database\Eloquent\Model
     * @throws \Exception
     */
    public function store(UserDocumentDto $documentDto)
    {
        $user = $this->getUser();

        $userDocument =  UserDocument::create([
            'type' => $documentDto->type,
            'status' => UserDocumentStatus::CREATED->value,
            'creator' => [
                '_id' => new ObjectId($user->_id),
                'username' => $user->username,
                'avatar' => $user->avatar,
            ],
        ]);

        if (\App::environment('testing')) {
            return $userDocument;
        }

        $this->uploadDocument($documentDto->file, $userDocument);

        if ($user->verification_status === UserVerificationStatus::VERIFIED->value) {
            $user->verification_status = UserVerificationStatus::UNVERIFIED->value;
            $user->save();
        }

        return $userDocument;
    }

    private function uploadDocument(UploadedFile $file, UserDocument $userDocument)
    {
        $filePrefix = match ($userDocument->type) {
            UserDocumentType::VIDEO_SELFIE->value => 'selfie_',
            UserDocumentType::ID_CARD->value => 'id_card_',
        };

        $this->uploadFileService->setFilePrefix($filePrefix);

        $tempFilePath = $this->uploadFileService->upload($file);
        $storageDisk = $this->uploadFileService->getDisk();

        $documentData = collect([
            'file' => $this->uploadFileService->getBaseFileName(),
            'md5' => $this->uploadFileService->getMd5(),
            'size' => $this->uploadFileService->getSize(),
        ]);

        $storageFilePath = $userDocument->creatorObj->_id.'/'.basename($tempFilePath);

        Bus::chain([
            new UploadFileJob(
                $tempFilePath,
                $storageDisk,
                collect([StorageType::B2_DOCUMENTS]),
                $storageFilePath,
            ),
            function () use ($userDocument, $documentData) {
                $userDocument->fill($documentData->all())->save();
            },
            new DeleteFileJob($storageDisk, $tempFilePath),
        ])
        ->onQueue(config('app.server_specific_queue'))
        ->dispatch();
    }
}
