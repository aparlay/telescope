<?php

namespace Aparlay\Core\Api\V1\Repositories;

use Aparlay\Core\Api\V1\Dto\UserDocumentDto;
use Aparlay\Core\Api\V1\Models\Block;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Traits\HasUserTrait;
use Aparlay\Core\Jobs\UploadAvatar;
use Aparlay\Core\Jobs\UploadFileJob;
use Aparlay\Core\Models\Enums\UserDocumentStatus;
use Aparlay\Core\Models\UserDocument;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\ObjectId;

class UserDocumentRepository
{
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
