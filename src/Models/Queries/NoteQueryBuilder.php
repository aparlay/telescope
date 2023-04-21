<?php

namespace Aparlay\Core\Models\Queries;

use MongoDB\BSON\ObjectId;

class NoteQueryBuilder extends EloquentQueryBuilder
{
    public function user(ObjectId|string $userId): self
    {
        return $this->whereId($userId, 'user._id');
    }

    public function category(int $category): self
    {
        return $this->where('category', $category);
    }

    public function isNotDeleted(): self
    {
        return $this->where('deleted_at', null);
    }
}
