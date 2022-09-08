<?php

namespace Aparlay\Core\Models\Queries;

use Aparlay\Core\Models\Enums\AlertStatus;
use Illuminate\Database\Eloquent\Builder;
use MongoDB\BSON\ObjectId;

class AlertQueryBuilder extends EloquentQueryBuilder
{
    /**
     * @return $this
     */
    public function visited(): self
    {
        return $this->where('status', AlertStatus::VISITED->value);
    }

    /**
     * @return $this
     */
    public function notVisited(): self
    {
        return $this->where('status', AlertStatus::NOT_VISITED->value);
    }

    /**
     * @param  ObjectId|string  $mediaId
     * @return self
     */
    public function media(ObjectId|string $mediaId): self
    {
        return $this->whereId($mediaId, 'media_id');
    }

    /**
     * @param  ObjectId|string  $userId
     * @return self
     */
    public function user(ObjectId|string $userId): self
    {
        return $this->whereId($userId, 'user_id');
    }

    /**
     * @return self
     */
    public function userOnly(): self
    {
        return $this->where('media_id', null);
    }
}
