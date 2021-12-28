<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Dto\UserDocumentDto;
use Aparlay\Core\Api\V1\Requests\UserDocumentRequest;
use Aparlay\Core\Api\V1\Resources\MediaLikeResource;
use Aparlay\Core\Api\V1\Resources\UserDocumentCollection;
use Aparlay\Core\Api\V1\Resources\UserDocumentResource;
use Aparlay\Core\Api\V1\Services\UserDocumentService;
use Aparlay\Core\Models\UserDocument;

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
     * @return UserDocumentCollection
     */
    public function index()
    {
        $userDocuments = $this->userDocumentService->index();

        return new UserDocumentCollection($userDocuments);
    }

    /**
     * @param UserDocument $userDocument
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
        $userDocument = $this->userDocumentService->view($id);

        return new UserDocumentResource($userDocument);
    }

    /**
     * @param UserDocumentRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserDocumentRequest $request)
    {
        $dto = UserDocumentDto::fromRequest($request);
        $userDocument = $this->userDocumentService->store($dto);

        return new UserDocumentResource($userDocument);
    }
}
