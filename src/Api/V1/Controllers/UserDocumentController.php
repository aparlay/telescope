<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Dto\UserDocumentDto;
use Aparlay\Core\Api\V1\Requests\UserDocumentRequest;
use Aparlay\Core\Api\V1\Resources\MediaLikeResource;
use Aparlay\Core\Api\V1\Resources\UserDocumentResource;
use Aparlay\Core\Api\V1\Services\UserDocumentService;

class UserDocumentController extends Controller
{
    /**
     * @var UserDocumentService
     */
    private $userDocumentService;

    public function __construct(UserDocumentService $documentService)
    {
        $this->userDocumentService = $documentService;

        if (auth()->check()) {
            $this->userDocumentService->setUser(auth()->user());
        }
    }

    /**
     * @param UserDocumentRequest $request
     * @return UserDocumentResource
     * @throws \Spatie\DataTransferObject\Exceptions\UnknownProperties
     */
    public function store(UserDocumentRequest $request)
    {
        $userDocument = $this->userDocumentService->store(UserDocumentDto::fromRequest($request));

        return $this->response(new UserDocumentResource($userDocument), '', 201);
    }
}
