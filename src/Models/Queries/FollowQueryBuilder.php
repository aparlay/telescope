<?php

namespace Aparlay\Core\Models\Queries;

use Aparlay\Core\Models\Enums\AlertStatus;
use Aparlay\Core\Models\Enums\EmailStatus;
use Aparlay\Core\Models\Enums\FollowStatus;
use Illuminate\Database\Eloquent\Builder;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use Str;

class FollowQueryBuilder extends EloquentQueryBuilder
{
    /**
     * @param  ObjectId|string  $creatorId
     * @return self
     */
    public function creator(ObjectId | string $creatorId): self
    {
        $creatorId = $creatorId instanceof ObjectId ? $creatorId : new ObjectId($creatorId);

        return $this->where('creator._id', $creatorId);
    }

    /**
     * @param  ObjectId|string  $userId
     * @return self
     */
    public function user(ObjectId | string $userId): self
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);

        return $this->where('user._id', $userId);
    }

    /**
     * @return self
     */
    public function deleted(): self
    {
        return $this->where('is_deleted', true);
    }

    /**
     * @return self
     */
    public function notDeleted(): self
    {
        return $this->where('is_deleted', false);
    }

    /**
     * @param  int  $status
     * @return self
     */
    public function status(int $status): self
    {
        return $this->where('status', $status);
    }

    /**
     * @return self
     */
    public function accepted(): self
    {
        return $this->where('status', FollowStatus::ACCEPTED->value);
    }
}
