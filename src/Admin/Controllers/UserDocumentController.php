<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Dto\AdminUserDocumentDTO;
use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Services\UserDocumentService;
use Aparlay\Core\Api\V1\Models\UserDocument;
use Illuminate\Http\Request;


class UserDocumentController extends Controller
{
    protected $userDocumentService;

    public function __construct(UserDocumentService $userDocumentService)
    {
        $this->userDocumentService = $userDocumentService;
        if (auth()->check()) {
            $userDocumentService->setUser($userDocumentService);
        }
    }

    public function update($documentId, Request $request)
    {
        $userDocument = UserDocument::findOrFail($documentId);
        $userDocument = $this->userDocumentService->update($userDocument, AdminUserDocumentDTO::fromRequest($request));


        return back()->with('success', `User document $documentId was updated successfully`);

    }



}
