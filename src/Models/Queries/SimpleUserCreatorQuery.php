<?php

namespace Aparlay\Core\Models\Queries;

use MongoDB\BSON\ObjectId;

trait SimpleUserCreatorQuery
{
    /**
     * @param ObjectId|string  $userId
     * @return self
     */
    public function creator(ObjectId|string $userId): self
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);

        return $this->where('creator._id', $userId);
    }
}
