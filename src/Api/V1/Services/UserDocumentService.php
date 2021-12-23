<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Dto\UserDocumentDto;
use Aparlay\Core\Api\V1\Repositories\UserDocumentRepository;
use Aparlay\Core\Api\V1\Traits\HasUserTrait;
use Aparlay\Core\Constants\StorageType;
use Aparlay\Core\Jobs\DeleteTempFileAfterUpload;
use Aparlay\Core\Jobs\UploadFileJob;
use Aparlay\Core\Models\UserDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;


class UserDocumentService
{
    use HasUserTrait;

    /**
     * @var $userDocumentRepository UserDocumentRepository
     */
    private $userDocumentRepository;

    /**
     * @var $uploadFileService UploadFileService
     */
    private $uploadFileService;


    public function __construct(
        UserDocumentRepository $userDocumentRepository,
        UploadFileService $uploadFileService
    )
    {
        $this->userDocumentRepository = $userDocumentRepository;
        $this->uploadFileService = $uploadFileService;
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
        $filePrefix = 'user_document_' . $this->getUser()->id;

        $this->uploadFileService->setFilePrefix($filePrefix);
        $path = $this->uploadFileService->upload($file);

        $fileDisk = $this->uploadFileService->getDisk();

        $documentData = collect([
            'file' => $this->uploadFileService->getBaseFileName(),
            'md5' => $this->uploadFileService->getMd5(),
            'size' => $this->uploadFileService->getSize(),
        ]);

        Bus::chain([
            new UploadFileJob(
                $path,
                $fileDisk,
                collect([StorageType::B2_AVATARS]) //@todo change storages to B2_DOCUMENTS storage
            ),
            function () use ($userDocument, $documentData) {
                $userDocument->fill($documentData->all())->save();
            },
            new DeleteTempFileAfterUpload($fileDisk, $path),
        ])->dispatch();

    }
}
