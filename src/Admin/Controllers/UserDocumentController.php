<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Dto\AdminUserDocumentDTO;
use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Resources\UserDocumentResource;
use Aparlay\Core\Admin\Resources\UserResource;
use Aparlay\Core\Admin\Services\UserDocumentService;
use Aparlay\Core\Api\V1\Models\UserDocument;
use Illuminate\Http\Request;

class UserDocumentController extends Controller
{
    protected $userDocumentService;

    public function __construct(UserDocumentService $userDocumentService)
    {
        $this->userDocumentService = $userDocumentService;
    }

    /**
     * @throws ErrorException
     */
    public function index()
    {
        $documentStatuses = $this->userDocumentService->getStatuses();

        return view('default_view::admin.pages.user-document.index', [
            'documentStatuses' => $documentStatuses,
        ]);
    }

    /**
     * @return UserDocumentResource
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function indexAjax()
    {
        $offset = (int) request()->get('start');
        $limit = (int) request()->get('length');

        return new UserDocumentResource($this->userDocumentService->fetchFiltered($offset, $limit));
    }

    /**
     * @param $documentId
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($documentId, Request $request)
    {
        $userDocument = UserDocument::findOrFail($documentId);

        if (auth()->check()) {
            $this->userDocumentService->setUser(auth()->user());
        }

        $this->userDocumentService->update($userDocument, AdminUserDocumentDTO::fromRequest($request));

        return back()->with('success', `User document $documentId was updated successfully`);
    }
}
