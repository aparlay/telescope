<?php

namespace Aparlay\Core\Models\Queries;

use Aparlay\Core\Models\Enums\UserNotificationCategory;
use Aparlay\Core\Models\Enums\UserNotificationStatus;
use MongoDB\BSON\ObjectId;

class MediaCommentQueryBuilder extends EloquentQueryBuilder
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
     * @param ObjectId|string $parentId
     * @return $this
     */
    public function parent(ObjectId | string $parentId): self
    {
        return $this->whereId($parentId, 'parent._id');
    }

    public function media(ObjectId | string $userId): self
    {
        return $this->whereId($userId, 'media_id');
    }
}
