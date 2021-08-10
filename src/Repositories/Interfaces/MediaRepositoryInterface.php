<?php

namespace Aparlay\Core\Repositories\Interfaces;

use Aparlay\Core\Api\V1\Models\Media;
use MongoDB\BSON\ObjectId;

interface MediaRepositoryInterface
{
    /**
     * @param ObjectId|null $userId
     * @param Media $media
     * @return bool
     */
    public function getIsVisibleBy(ObjectId|null $userId, Media $media): bool;
}
