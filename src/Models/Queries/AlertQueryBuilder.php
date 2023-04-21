<?php

namespace Aparlay\Core\Models\Queries;

use Aparlay\Core\Models\Enums\AlertStatus;
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

    public function media(ObjectId|string $mediaId): self
    {
        return $this->whereId($mediaId, 'media_id');
    }

    public function user(ObjectId|string $userId): self
    {
        return $this->whereId($userId, 'user_id');
    }

    public function userOnly(): self
    {
        return $this->where('media_id', null);
    }
}
