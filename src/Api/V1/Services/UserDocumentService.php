<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Dto\UserDocumentDto;
use Aparlay\Core\Api\V1\Repositories\UserDocumentRepository;
use Aparlay\Core\Api\V1\Traits\HasUserTrait;
use Aparlay\Core\Jobs\UploadFileJob;
use Aparlay\Core\Models\Enums\UserDocumentStatus;
use Aparlay\Core\Models\UserDocument;
use MongoDB\BSON\ObjectId;

class UserDocumentService
{
    use HasUserTrait;

    /**
     * @param UserDocumentDto $documentDto
     */
    public function store(UserDocumentDto $documentDto)
    {
        $userDocumentRepository = app()->make(UserDocumentRepository::class);
        $documentDto->setUser($this->getUser());
        $userDocument = $userDocumentRepository->create($documentDto);

        if (! config('app.is_testing')) {
            $uploadFileService = app()->make(UploadFileService::class, [
                'filePrefix' => 'user_document_' . $this->getUser()->id,
            ]);
            $uploadFileService->setUser($this->getUser());
            $path = $uploadFileService->upload($documentDto->file);

            UploadFileJob::dispatch($userDocument, $path, $uploadFileService->getDisk())->delay(10);
        }

        return $userDocument;
    }
}
