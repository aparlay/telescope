<?php

namespace Aparlay\Core\Models\Scopes;

use Aparlay\Core\Models\Enums\UserNotificationCategory;
use Aparlay\Core\Models\Enums\UserNotificationStatus;
use Illuminate\Database\Eloquent\Builder;
use MongoDB\BSON\ObjectId;

trait UserNotificationScope
{
    use BaseScope;

    public function scopeVisited(Builder $query): Builder
    {
        return $query->where('status', UserNotificationStatus::VISITED->value);
    }

    public function scopeNotVisited(Builder $query): Builder
    {
        return $query->where('status', UserNotificationStatus::NOT_VISITED->value);
    }

    public function scopeLikes(Builder $query): Builder
    {
        return $query->where('category', UserNotificationCategory::LIKES->value);
    }

    public function scopeComments(Builder $query): Builder
    {
        return $query->where('category', UserNotificationCategory::COMMENTS->value);
    }

    public function scopeTips(Builder $query): Builder
    {
        return $query->where('category', UserNotificationCategory::TIPS->value);
    }

    public function scopeFollows(Builder $query): Builder
    {
        return $query->where('category', UserNotificationCategory::FOLLOWS->value);
    }

    public function scopeSystem(Builder $query): Builder
    {
        return $query->where('category', UserNotificationCategory::SYSTEM->value);
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
