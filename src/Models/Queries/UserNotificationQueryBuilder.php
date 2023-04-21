<?php

namespace Aparlay\Core\Models\Queries;

use Aparlay\Core\Models\Enums\UserNotificationCategory;
use Aparlay\Core\Models\Enums\UserNotificationStatus;
use MongoDB\BSON\ObjectId;

class UserNotificationQueryBuilder extends EloquentQueryBuilder
{
    public function payloadUserId(string $userId)
    {
        return $this->whereRaw(['payload.user._id' => $userId]);
    }

    public function visited(): self
    {
        return $this->status(UserNotificationStatus::VISITED->value);
    }

    public function notVisited(): self
    {
        return $this->status(UserNotificationStatus::NOT_VISITED->value);
    }

    public function status(int|array $status): self
    {
        return is_array($status) ? $this->whereIn('status', $status) : $this->where('status', $status);
    }

    public function likes(): self
    {
        return $this->category(UserNotificationCategory::LIKES->value);
    }

    public function comments(): self
    {
        return $this->category(UserNotificationCategory::COMMENTS->value);
    }

    public function tips(): self
    {
        return $this->category(UserNotificationCategory::TIPS->value);
    }

    public function follows(): self
    {
        return $this->category(UserNotificationCategory::FOLLOWS->value);
    }

    public function system(): self
    {
        return $this->category(UserNotificationCategory::SYSTEM->value);
    }

    public function category(int|string $category): self
    {
        if (is_string($category) && !empty($category)) {
            $category = array_search($category, UserNotificationCategory::getAllCases());
        }

        return $this->where('category', $category);
    }

    public function user(ObjectId|string $userId): self
    {
        return $this->whereId($userId, 'user_id');
    }

    public function entity(ObjectId|string $entityId, string $entityType): self
    {
        return $this->whereId($entityId, 'entity._id')->where('entity._type', $entityType);
    }

    public function tipEntity(ObjectId|string $tipId): self
    {
        return $this->entity($tipId, 'Tip');
    }

    public function mediaEntity(ObjectId|string $mediaId): self
    {
        return $this->entity($mediaId, 'Media');
    }

    public function userEntity(ObjectId|string $userId): self
    {
        return $this->entity($userId, 'User');
    }

    public function visible(): self
    {
        return $this->status([UserNotificationStatus::VISITED->value, UserNotificationStatus::NOT_VISITED->value]);
    }
}
