<?php

namespace Aparlay\Core\Models\Scopes;

use MongoDB\BSON\ObjectId;

trait ReportScope
{
    use BaseScope;

    public function scopeMedia($query, $mediaId): mixed
    {
        $mediaId = $mediaId instanceof ObjectId ? $mediaId : new ObjectId($mediaId);

        return $query->where('media_id', $mediaId);
    }

    public function scopeUser($query, $userId): mixed
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);

        return $query->where('user_id', $userId);
    }
}
