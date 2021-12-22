<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Dto\UserDocumentDto;
use Aparlay\Core\Api\V1\Requests\UserDocumentRequest;
use Aparlay\Core\Api\V1\Resources\MediaLikeResource;
use Aparlay\Core\Api\V1\Resources\UserDocumentResource;
use Aparlay\Core\Api\V1\Services\UserDocumentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserDocumentController extends Controller
{
    /**
     * @var $userDocumentService UserDocumentService
     */
    private $userDocumentService;

    public function __construct(UserDocumentService $documentService)
    {
        $this->userDocumentService = $documentService;

        if (auth()->check()) {
            $this->userDocumentService->setUser(auth()->user());
        }
    }


    public function store(UserDocumentRequest $request)
    {
        $userDocument = $this->userDocumentService->store(UserDocumentDto::fromRequest($request));
        return new UserDocumentResource($userDocument);
    }
}
