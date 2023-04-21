<?php

namespace Aparlay\Core\Models\Queries;

class ReportQueryBuilder extends EloquentQueryBuilder
{
    public function media($mediaId): self
    {
        return $this->whereId($mediaId, 'media_id');
    }

    public function user($userId): self
    {
        return $this->whereId($userId, 'user_id');
    }
}
