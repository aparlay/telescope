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

    public function view($id)
    {
        return $this->userDocumentRepository->view($id);
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

    /**
     * @param UserDocument $userDocument
     * @return string
     * @throws \ErrorException
     */
    private function getFilePrefix(UserDocument $userDocument)
    {
        switch ($userDocument->type) {
            case UserDocumentType::SELFIE->value:
                $filePrefix = 'selfie_';
                break;
            case UserDocumentType::ID_CARD->value:
                $filePrefix = 'id_card_';
                break;
            default:
                throw new \ErrorException('Unknown document type');
        }

        return $filePrefix;
    }

    private function uploadDocument(UploadedFile $file, UserDocument $userDocument)
    {
        $filePrefix = $this->getFilePrefix($userDocument);
        $this->uploadFileService->setFilePrefix($filePrefix);

        $tempFilePath = $this->uploadFileService->upload($file);
        $storageDisk = $this->uploadFileService->getDisk();

        $documentData = collect([
            'file' => $this->uploadFileService->getBaseFileName(),
            'md5' => $this->uploadFileService->getMd5(),
            'size' => $this->uploadFileService->getSize(),
        ]);

        $storageFilePath = $userDocument->userObj->_id.'/'.basename($tempFilePath);

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
