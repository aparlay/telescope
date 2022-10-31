<?php

namespace Aparlay\Core\Models\Queries;

use MongoDB\BSON\ObjectId;

class MediaCommentLikeQueryBuilder extends EloquentQueryBuilder
{
    /**
     * @param  ObjectId|string  $userId
     * @return self
     */
    public function user(ObjectId | string $userId): self
    {
        return $this->whereId($userId, 'user_id');
    }

    /**
     * @param  ObjectId|string  $userId
     *
     * @return $this
     */
    public function creator(ObjectId | string $userId): self
    {
        return $this->whereId($userId, 'creator._id');
    }

    /**
     * @param  ObjectId|string  $mediaCommentId
     *
     * @return $this
     */
    public function comment(ObjectId | string $mediaCommentId): self
    {
        return $this->whereId($mediaCommentId, 'media_comment_id');
    }
}
