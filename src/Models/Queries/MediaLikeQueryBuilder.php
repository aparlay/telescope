<?php

namespace Aparlay\Core\Models\Queries;

use MongoDB\BSON\ObjectId;

class MediaLikeQueryBuilder extends EloquentQueryBuilder
{
    /**
     * @param  ObjectId|string  $mediaId
     * @return self
     */
    public function media(ObjectId | string $mediaId): self
    {
        return $this->whereId($mediaId, 'media_id');
    }

    /**
     * @param  ObjectId|string  $userId
     * @return self
     */
    public function user(ObjectId | string $userId): self
    {
        return $this->whereId($userId, 'user_id');
    }

    /**
     * @param  ObjectId|string  $creatorId
     * @return self
     */
    public function creator(ObjectId | string $creatorId): self
    {
        return $this->whereId($creatorId, 'creator._id');
    }
}
