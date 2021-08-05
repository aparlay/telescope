<?php

namespace Aparlay\Core\Models\Scopes;

use Aparlay\Core\Models\Alert;
use MongoDB\BSON\ObjectId;

trait TransactionScope
{
    /**
     * @param $query
     */
    public function scopeVisited($query): mixed
    {
        return $query->where('status', Alert::STATUS_VISITED);
    }

    /**
     * @param $query
     */
    public function scopeNotVisited($query): mixed
    {
        return $query->where('status', Alert::STATUS_NOT_VISITED);
    }

    /**
     * @param $query
     * @param $mediaId
     */
    public function scopeMedia($query, $mediaId): mixed
    {
        $mediaId = $mediaId instanceof ObjectId ? $mediaId : new ObjectId($mediaId);

        return $query->where('media_id', $mediaId);
    }

    /**
     * @param $query
     * @param $userId
     */
    public function scopeUser($query, $userId): mixed
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);

        return $query->where('user_id', $userId);
    }
}
