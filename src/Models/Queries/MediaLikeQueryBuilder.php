<?php

namespace Aparlay\Core\Models\Queries;

use MongoDB\BSON\ObjectId;

class MediaLikeQueryBuilder extends EloquentQueryBuilder
{
    public function media(ObjectId|string $mediaId): self
    {
        return $this->whereId($mediaId, 'media_id');
    }

    public function user(ObjectId|string $userId): self
    {
        return $this->whereId($userId, 'user_id');
    }

    public function creator(ObjectId|string $creatorId): self
    {
        return $this->whereId($creatorId, 'creator._id');
    }
}
