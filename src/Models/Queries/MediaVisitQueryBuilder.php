<?php

namespace Aparlay\Core\Models\Queries;

use MongoDB\BSON\ObjectId;

class MediaVisitQueryBuilder extends EloquentQueryBuilder
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

    /**
     * @param  string  $date
     * @return self
     */
    public function dateString(string $date): self
    {
        return $this->where('date', $date);
    }
}
