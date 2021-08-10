<?php

namespace Aparlay\Core\Repositories;

use Aparlay\Core\Api\V1\Models\Follow;
use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Repositories\Interfaces\MediaRepositoryInterface;
use MongoDB\BSON\ObjectId;

class MediaRepository implements MediaRepositoryInterface
{
    /**
     * @param ObjectId|null $userId
     * @param Media $media
     * @return bool
     */
    public function getIsVisibleBy(ObjectId | null $userId, Media $media): bool
    {
        if ($media->visibility === Media::VISIBILITY_PUBLIC) {
            return true;
        }

        if ($media->visibility === Media::VISIBILITY_PRIVATE && $userId === null) {
            return false;
        }

        $isFollowed = Follow::select(['created_by', '_id'])
            ->creator($userId)
            ->user($media->created_by)
            ->accepted()
            ->exists();
        if (! empty($isFollowed)) {
            return true;
        }

        return false;
    }
}
