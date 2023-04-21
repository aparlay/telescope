<?php

namespace Aparlay\Core\Models\Queries;

use MongoDB\BSON\ObjectId;

class MediaCommentQueryBuilder extends EloquentQueryBuilder
{
    public function user(ObjectId|string $userId): self
    {
        return $this->whereId($userId, 'user_id');
    }

    public function creator(ObjectId|string $userId): self
    {
        return $this->whereId($userId, 'creator._id');
    }

    /**
     * @return $this
     */
    public function parent(ObjectId|string $parentId): self
    {
        return $this->whereId($parentId, 'parent._id');
    }

    /**
     * @return $this
     */
    public function media(ObjectId|string $mediaId): self
    {
        return $this->whereId($mediaId, 'media_id');
    }
}
