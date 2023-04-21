<?php

namespace Aparlay\Core\Models\Queries;

class UserDocumentQueryBuilder extends EloquentQueryBuilder
{
    use SimpleUserCreatorQuery;

    public function type($type): self
    {
        return $this->where('type', $type);
    }

    public function status($status): self
    {
        return $this->where('status', $status);
    }
}
