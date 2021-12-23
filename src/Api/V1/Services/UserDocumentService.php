<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Dto\UserDocumentDto;
use Aparlay\Core\Api\V1\Traits\HasUserTrait;
use Aparlay\Core\Models\Enums\UserDocumentStatus;
use Aparlay\Core\Models\UserDocument;
use MongoDB\BSON\ObjectId;

class UserDocumentService
{
    use HasUserTrait;

    /**
     * @param UserDocumentDto $documentDto
     */
    public function store(UserDocumentDto $documentDto)
    {
        $userDocument = UserDocument::create([
            'type' => $documentDto->type,
            'status' => UserDocumentStatus::CREATED->value,
            'user_id' => new ObjectId($this->getUser()->id),
            'creator' => [
                '_id' => new ObjectId($this->getUser()->_id),
                'username' => $this->getUser()->username,
                'avatar' => $this->getUser()->avatar,
            ],
        ]);

        return $userDocument;
    }
}
