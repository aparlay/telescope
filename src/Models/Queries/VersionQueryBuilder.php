<?php

namespace Aparlay\Core\Models\Queries;

use Aparlay\Core\Models\Enums\UserNotificationCategory;
use Aparlay\Core\Models\Enums\UserNotificationStatus;
use MongoDB\BSON\ObjectId;

class VersionQueryBuilder extends EloquentQueryBuilder
{
    /**
     * @param $os
     * @return self
     */
    public function os($os): self
    {
        return $this->where('os', $os);
    }

    /**
     * @param $app
     * @return self
     */
    public function app($app): self
    {
        return $this->where('app', $app);
    }

    /**
     * @return self
     */
    public function recentFirst(): self
    {
        return $this->orderBy('version', 'desc');
    }
}
