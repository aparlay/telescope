<?php

namespace Aparlay\Core\Models\Queries;

use Aparlay\Core\Models\Enums\UserNotificationCategory;
use Aparlay\Core\Models\Enums\UserNotificationStatus;
use MongoDB\BSON\ObjectId;

class ReportQueryBuilder extends EloquentQueryBuilder
{
    /**
     * @param $mediaId
     * @return self
     */
    public function media($mediaId): self
    {
        return $this->whereId($mediaId, 'media_id');
    }

    /**
     * @param $userId
     * @return self
     */
    public function user($userId): self
    {
        return $this->whereId($userId, 'user_id');
    }
}
