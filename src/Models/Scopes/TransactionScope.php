<?php


namespace Aparlay\Core\Models\Scopes;


use Aparlay\Core\Models\Alert;
use MongoDB\BSON\ObjectId;

trait TransactionScope
{

    /**
     * @param $query
     * @return mixed
     */
    public function scopeVisited($query): mixed
    {
        return $query->where('status', Alert::STATUS_VISITED);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeNotVisited($query): mixed
    {
        return $query->where('status', Alert::STATUS_NOT_VISITED);
    }

    /**
     * @param $query
     * @param $mediaId
     * @return mixed
     */
    public function scopeMedia($query, $mediaId): mixed
    {
        $mediaId = $mediaId instanceof ObjectId ? $mediaId : new ObjectId($mediaId);
        return $query->where('media_id', $mediaId);
    }

    /**
     * @param $query
     * @param $userId
     * @return mixed
     */
    public function scopeUser($query, $userId): mixed
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);
        return $query->where('user_id', $userId);
    }
}
