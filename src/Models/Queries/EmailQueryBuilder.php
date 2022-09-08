<?php

namespace Aparlay\Core\Models\Queries;

use Aparlay\Core\Models\Enums\AlertStatus;
use Aparlay\Core\Models\Enums\EmailStatus;
use Illuminate\Database\Eloquent\Builder;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use Str;

class EmailQueryBuilder extends EloquentQueryBuilder
{
    /**
     * @return self
     */
    public function opened(): self
    {
        return $this->where('status', EmailStatus::OPENED->value);
    }

    /**
     * @return self
     */
    public function sent(): self
    {
        return $this->where('status', EmailStatus::SENT->value);
    }

    /**
     * @return self
     */
    public function failed(): self
    {
        return $this->where('status', EmailStatus::FAILED->value);
    }

    /**
     * @param  ObjectId|string  $userId
     * @return self
     */
    public function user(ObjectId | string $userId): self
    {
        return $this->whereId($userId, 'user._id');
    }

}
