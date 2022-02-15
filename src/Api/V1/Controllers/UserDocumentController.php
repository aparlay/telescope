<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Dto\UserDocumentDto;
use Aparlay\Core\Api\V1\Requests\UserDocumentRequest;
use Aparlay\Core\Api\V1\Resources\UserDocumentCollection;
use Aparlay\Core\Api\V1\Resources\UserDocumentResource;
use Aparlay\Core\Api\V1\Services\UserDocumentService;
use Aparlay\Core\Models\UserDocument;
use Illuminate\Http\Response;

class UserDocumentController extends Controller
{
    /**
     * @var UserDocumentService
     */
    private $userDocumentService;

    public function __construct(UserDocumentService $documentService)
    {
        $this->userDocumentService = $documentService;
    }

    /**
     * @return UserDocumentCollection
     */
    public function index()
    {
        if (auth()->check()) {
            $this->userDocumentService->setUser(auth()->user());
        }
        $userDocuments = $this->userDocumentService->index();
        $collection = new UserDocumentCollection($userDocuments);

        return $this->response($collection, '', Response::HTTP_OK);
    }

    /**
     * @param UserDocument $userDocument
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
        if (auth()->check()) {
            $this->userDocumentService->setUser(auth()->user());
        }
        $userDocument = $this->userDocumentService->fetchById($id);
        $this->authorize('view', $userDocument);
        return $this->response(new UserDocumentResource($userDocument), '', Response::HTTP_OK);
    }

    /**
     * @param UserDocumentRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserDocumentRequest $request)
    {
        $dto = UserDocumentDto::fromRequest($request);
        if (auth()->check()) {
            $this->userDocumentService->setUser(auth()->user());
        }
        $userDocument = $this->userDocumentService->store($dto);
        $resource = (new UserDocumentResource($userDocument))->except('url');

        return $this->response($resource, '', Response::HTTP_CREATED);
    }
}
