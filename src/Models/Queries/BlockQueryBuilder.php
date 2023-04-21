<?php

namespace Aparlay\Core\Models\Queries;

use MongoDB\BSON\ObjectId;

class BlockQueryBuilder extends EloquentQueryBuilder
{
    public function creator(ObjectId|string $creatorId): self
    {
        return $this->whereId($creatorId, 'creator._id');
    }

    public function user(ObjectId|string $userId): self
    {
        return $this->whereId($userId, 'user._id');
    }

    public function isDeleted(): self
    {
        return $this->where('is_deleted', true);
    }

    public function isNotDeleted(): self
    {
        return $this->where('is_deleted', false);
    }

    public function country(string $countryAlpha2): self
    {
        return $this->where('country_alpha2', $countryAlpha2);
    }

    public function countryType(): self
    {
        return $this->where('user', null);
    }

    public function userType(): self
    {
        return $this->where('country_alpha2', null);
    }
}
