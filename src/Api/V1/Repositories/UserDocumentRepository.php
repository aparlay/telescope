<?php

namespace Aparlay\Core\Api\V1\Repositories;

use Aparlay\Core\Api\V1\Dto\UserDocumentDto;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Models\Enums\UserDocumentStatus;
use Aparlay\Core\Api\V1\Models\UserDocument;

use MongoDB\BSON\ObjectId;

class UserDocumentRepository
{

    /**
     * @param User $user
     * @return mixed
     */
    public function index(User $user)
    {
        return UserDocument::user($user->id)->get();
    }


    public function view($id)
    {
        return UserDocument::query()->findOrFail($id);
    }

    public function create(UserDocumentDto $documentDto)
    {
        $creator = $documentDto->getUser();

        try {
            return UserDocument::create([
                'type' => $documentDto->type,
                'status' => UserDocumentStatus::CREATED->value,
                'user_id' => new ObjectId($creator->id),
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
}
