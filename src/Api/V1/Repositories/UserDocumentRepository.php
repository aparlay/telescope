<?php

namespace Aparlay\Core\Api\V1\Repositories;

use Aparlay\Core\Api\V1\Dto\UserDocumentDto;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Models\UserDocument;
use Aparlay\Core\Models\Enums\UserDocumentStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use MongoDB\BSON\ObjectId;

class UserDocumentRepository
{
    /**
     * @param User $user
     * @return mixed
     */
    public function index($user): LengthAwarePaginator
    {
        return UserDocument::creator($user->_id)
            ->with('alertObjs')
            ->paginate(5)
            ->withQueryString();
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
}
