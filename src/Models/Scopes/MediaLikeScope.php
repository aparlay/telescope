<?php

namespace Aparlay\Core\Models\Scopes;

use Jenssegers\Mongodb\Eloquent\Builder;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

trait MediaLikeScope
{
    /**
     * @param ObjectId|string $mediaId
     */
    public function scopeMedia(Builder $query, ObjectId | string $mediaId): Builder
    {
        $mediaId = $mediaId instanceof ObjectId ? $mediaId : new ObjectId($mediaId);

        return $query->where('media_id', $mediaId);
    }

    /**
     * @param ObjectId|string $userId
     */
    public function scopeUser(Builder $query, ObjectId | string $userId): Builder
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);

        return $query->where('user_id', $userId);
    }

    /**
     * @param ObjectId|string $creatorId
     */
    public function scopeCreator(Builder $query, ObjectId | string $creatorId): Builder
    {
        $creatorId = $creatorId instanceof ObjectId ? $creatorId : new ObjectId($creatorId);

        return $query->where('creator._id', $creatorId);
    }

    public function scopeDate(Builder $query, UTCDateTime $start, UTCDateTime $end): Builder
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }
}
