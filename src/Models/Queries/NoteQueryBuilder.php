<?php

namespace Aparlay\Core\Models\Queries;

use MongoDB\BSON\ObjectId;

class NoteQueryBuilder extends EloquentQueryBuilder
{
    /**
     * @param  ObjectId|string  $userId
     * @return self
     */
    public function user(ObjectId | string $userId): self
    {
        return $this->whereId($userId, 'user._id');
    }

    /**
     * @param int $category
     * @return self
     */
    public function category(int $category): self
    {
        return $this->where('category', $category);
    }

    /**
     * @return self
     */
    public function isNotDeleted(): self
    {
        return $this->where('deleted_at', null);
    }
}
