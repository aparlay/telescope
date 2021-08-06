<?php

namespace Aparlay\Core\Models\Scopes;

use Aparlay\Core\Models\Alert;
use Illuminate\Database\Eloquent\Builder;
use MongoDB\BSON\ObjectId;

trait AlertScope
{
    public function scopeVisited(Builder $query): Builder
    {
        return $query->where('status', Alert::STATUS_VISITED);
    }

    public function scopeNotVisited(Builder $query): Builder
    {
        return $query->where('status', Alert::STATUS_NOT_VISITED);
    }

    /**
     * @param  ObjectId|string  $mediaId
     */
    public function scopeMedia(Builder $query, ObjectId | string $mediaId): Builder
    {
        $mediaId = $mediaId instanceof ObjectId ? $mediaId : new ObjectId($mediaId);

        return $query->where('media_id', $mediaId);
    }

    /**
     * @param  ObjectId|string  $userId
     */
    public function scopeUser(Builder $query, ObjectId | string $userId): Builder
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);

        return $query->where('user_id', $userId);
    }
}
