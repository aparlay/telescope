<?php

namespace Aparlay\Core\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use MongoDB\BSON\ObjectId;

trait UserDocumentScope
{
    use BaseScope;

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }


    /**
     * @param  Builder  $query
     * @param  ObjectId|string  $userId
     * @return Builder
     */
    public function scopeUser(Builder $query, ObjectId | string $userId): Builder
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);

        return $query->where('user_id', $userId);
    }
}
