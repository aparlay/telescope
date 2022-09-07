<?php

namespace Aparlay\Core\Models\Queries;

use MongoDB\BSON\ObjectId;

class BlockQueryBuilder extends EloquentQueryBuilder
{
    /**
     * @param  ObjectId|string  $creatorId
     * @return self
     */
    public function creator(ObjectId | string $creatorId): self
    {
        return $this->whereId($creatorId, 'creator._id');
    }

    /**
     * @param  ObjectId|string  $userId
     * @return self
     */
    public function user(ObjectId | string $userId): self
    {
        return $this->whereId($userId, 'user._id');
    }

    /**
     * @return self
     */
    public function isDeleted(): self
    {
        return $this->where('is_deleted', true);
    }

    /**
     * @return self
     */
    public function isNotDeleted(): self
    {
        return $this->where('is_deleted', false);
    }

    /**
     * @param  string  $countryAlpha2
     * @return self
     */
    public function country(string $countryAlpha2): self
    {
        return $this->where('country_alpha2', $countryAlpha2);
    }
    
    /**
     * @return self
     */
    public function countryType(): self
    {
        return $this->where('user', null);
    }

    /**
     * @return self
     */
    public function userType(): self
    {
        return $this->where('country_alpha2', null);
    }
}
