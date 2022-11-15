<?php

namespace Aparlay\Core\Models\Queries;

use Aparlay\Core\Models\Enums\UserNotificationCategory;
use Aparlay\Core\Models\Enums\UserNotificationStatus;
use MongoDB\BSON\ObjectId;

class UserNotificationQueryBuilder extends EloquentQueryBuilder
{
    use SimpleUserCreatorQuery;

    public function visited(): self
    {
        return $this->status(UserNotificationStatus::VISITED->value);
    }

    public function notVisited(): self
    {
        return $this->status(UserNotificationStatus::NOT_VISITED->value);
    }

    public function status(int $status): self
    {
        return $this->where('status', $status);
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
        if (is_string($category) && ! empty($category)) {
            $category = array_search($category, UserNotificationCategory::getAllCases());
        }

        return $this->where('category', $category);
    }

    /**
     * @param  ObjectId|string  $userId
     * @return self
     */
    public function user(ObjectId | string $userId): self
    {
        return $this->whereId($userId, 'user_id');
    }

    /**
     * @param  string|ObjectId  $entityId
     * @param  string  $entityType
     * @return self
     */
    public function entity(ObjectId|string $entityId, string $entityType): self
    {
        return $this->whereId($entityId, 'entity._id')->where('entity._type', $entityType);
    }

    /**
     * @param  string|ObjectId  $tipId
     * @return self
     */
    public function tipEntity(ObjectId|string $tipId): self
    {
        return $this->entity($tipId, 'Tip');
    }

    /**
     * @param  string|ObjectId  $mediaId
     * @return self
     */
    public function mediaEntity(ObjectId|string $mediaId): self
    {
        return $this->entity($mediaId, 'Media');
    }

    /**
     * @param  string|ObjectId  $userId
     * @return self
     */
    public function userEntity(ObjectId|string $userId): self
    {
        return $this->entity($userId, 'User');
    }
}
