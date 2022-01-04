<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Dto\UserDocumentDto;
use Aparlay\Core\Api\V1\Repositories\UserDocumentRepository;
use Aparlay\Core\Api\V1\Traits\HasUserTrait;
use Aparlay\Core\Constants\StorageType;
use Aparlay\Core\Jobs\DeleteFileJob;
use Aparlay\Core\Jobs\UploadFileJob;
use Aparlay\Core\Models\Enums\UserDocumentType;
use Aparlay\Core\Models\UserDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;

class UserDocumentService
{
    use HasUserTrait;

    /**
     * @var UserDocumentRepository
     */
    private $userDocumentRepository;

    /**
     * @var UploadFileService
     */
    private $uploadFileService;

    public function __construct(
        UserDocumentRepository $userDocumentRepository,
        UploadFileService $uploadFileService
    ) {
        $this->userDocumentRepository = $userDocumentRepository;
        $this->uploadFileService = $uploadFileService;
    }

    public function index()
    {
        return $this->userDocumentRepository->index($this->getUser());
    }

    public function fetchById($id)
    {
        return $this->userDocumentRepository->fetchById($id);
    }

    /**
     * @param UserDocumentDto $documentDto
     */
    public function store(UserDocumentDto $documentDto)
    {
        $documentDto->setUser($this->getUser());
        $userDocument = $this->userDocumentRepository->create($documentDto);

        if (\App::environment('testing')) {
            return $userDocument;
        }
        $this->uploadDocument($documentDto->file, $userDocument);

        return $userDocument;
    }

    private function uploadDocument(UploadedFile $file, UserDocument $userDocument)
    {
        $filePrefix = match ($userDocument->type) {
            UserDocumentType::SELFIE->value => 'selfie_',
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
            ->onQueue('low')
            ->dispatch();
    }
}
