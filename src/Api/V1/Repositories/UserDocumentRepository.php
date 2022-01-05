<?php

namespace Aparlay\Core\Api\V1\Repositories;

use Aparlay\Core\Api\V1\Dto\UserDocumentDto;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Models\UserDocument;
use Aparlay\Core\Models\Enums\UserDocumentStatus;
use MongoDB\BSON\ObjectId;

class UserDocumentRepository
{
    /**
     * @param User $user
     * @return mixed
     */
    public function index($user)
    {
        return UserDocument::creator($user->_id)->get();
    }

    public function fetchById($id)
    {
        return UserDocument::query()->findOrFail($id);
    }

    public function create(UserDocumentDto $documentDto)
    {
        $creator = $documentDto->getUser();
        try {
            return UserDocument::create([
                'type' => $documentDto->type,
                'status' => UserDocumentStatus::PENDING->value,
                'creator' => [
                    '_id' => new ObjectId($creator->_id),
                    'username' => $creator->username,
                    'avatar' => $creator->avatar,
                ],
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }


    /**
     * @param $user
     * @return mixed
     */
    public function updateCounters($user)
    {
        $rejectedDocs = $user->userDocumentObjs()->status(UserDocumentStatus::REJECTED->value)->count();
        $approvedDocs = $user->userDocumentObjs()->status(UserDocumentStatus::APPROVED->value)->count();
        $pendingDocs = $user->userDocumentObjs()->status(UserDocumentStatus::PENDING->value)->count();

        $user->rejected_documents = $rejectedDocs;
        $user->approved_documents = $approvedDocs;
        $user->pending_documents = $pendingDocs;
        $user->save();

        return $user;
    }
}
