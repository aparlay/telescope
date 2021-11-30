<?php

namespace Aparlay\Core\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use MongoDB\BSON\ObjectId;

trait MediaVisitScope
{
    use BaseScope;

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

    public function scopeDate(Builder $query, string $date): Builder
    {
        return $query->where('date', $date);
    }
}
