<?php

namespace Aparlay\Core\Models\Queries;

use Aparlay\Core\Models\Enums\FollowStatus;
use MongoDB\BSON\ObjectId;

class FollowQueryBuilder extends EloquentQueryBuilder
{
    public function creator(ObjectId|string $creatorId): self
    {
        $creatorId = $creatorId instanceof ObjectId ? $creatorId : new ObjectId($creatorId);

        return $this->where('creator._id', $creatorId);
    }

    public function user(ObjectId|string $userId): self
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);

        return $this->where('user._id', $userId);
    }

    public function deleted(): self
    {
        return $this->where('is_deleted', true);
    }

    public function notDeleted(): self
    {
        return $this->where('is_deleted', false);
    }

    public function status(int $status): self
    {
        return $this->where('status', $status);
    }

    public function accepted(): self
    {
        return $this->where('status', FollowStatus::ACCEPTED->value);
    }
}
