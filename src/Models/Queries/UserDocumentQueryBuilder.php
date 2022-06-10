<?php

namespace Aparlay\Core\Models\Queries;

use Aparlay\Core\Models\Enums\UserNotificationCategory;
use Aparlay\Core\Models\Enums\UserNotificationStatus;
use Illuminate\Database\Eloquent\Builder;
use MongoDB\BSON\ObjectId;

/**
 *
 */
class UserDocumentQueryBuilder extends EloquentQueryBuilder
{
    use SimpleUserCreatorQuery;

    /**
     * @param $type
     * @return self
     */
    public function type($type): self
    {
        return $this->where('type', $type);
    }

    /**
     * @param $status
     * @return self
     */
    public function status($status): self
    {
        return $this->where('status', $status);
    }
}
