<?php

namespace Aparlay\Core\Models\Queries;

use MongoDB\BSON\ObjectId;

class MediaCommentLikeQueryBuilder extends EloquentQueryBuilder
{
    public function user(ObjectId|string $userId): self
    {
        return $this->whereId($userId, 'user_id');
    }

    /**
     * @return $this
     */
    public function creator(ObjectId|string $userId): self
    {
        return $this->whereId($userId, 'creator._id');
    }

    /**
     * @return $this
     */
    public function comment(ObjectId|string $mediaCommentId): self
    {
        return $this->whereId($mediaCommentId, 'media_comment_id');
    }
}
