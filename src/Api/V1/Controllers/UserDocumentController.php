<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Dto\UserDocumentDto;
use Aparlay\Core\Api\V1\Models\UserDocument;
use Aparlay\Core\Api\V1\Requests\UserDocumentRequest;
use Aparlay\Core\Api\V1\Resources\UserDocumentCollection;
use Aparlay\Core\Api\V1\Resources\UserDocumentResource;
use Aparlay\Core\Api\V1\Services\UserDocumentService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

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
     * @return Response
     */
    public function index(): Response
    {
        if (auth()->check()) {
            $this->userDocumentService->setUser(auth()->user());
        }
        $userDocuments = $this->userDocumentService->index();
        $collection = new UserDocumentCollection($userDocuments);

        return $this->response($collection, '', Response::HTTP_OK);
    }

    /**
     * @param  UserDocument  $userDocument
     * @return Response
     * @throws AuthorizationException
     */
    public function view(UserDocument $userDocument): Response
    {
        $this->injectAuthUser($this->userDocumentService);
        $this->authorize('view', [UserDocument::class, $userDocument]);

        return $this->response(new UserDocumentResource($userDocument), '', Response::HTTP_OK);
    }

    /**
     * @return Response
     */
    public function sendToVerification(): Response
    {
        $this->injectAuthUser($this->userDocumentService);
        $this->userDocumentService->changeToPending();

        return $this->response('', '', Response::HTTP_NO_CONTENT);
    }

    /**
     * @param  UserDocumentRequest  $request
     * @return Response
     * @throws UnknownProperties
     */
    public function store(UserDocumentRequest $request): Response
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
