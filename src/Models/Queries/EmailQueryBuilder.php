<?php

namespace Aparlay\Core\Models\Queries;

use Aparlay\Core\Models\Enums\EmailStatus;
use MongoDB\BSON\ObjectId;

class EmailQueryBuilder extends EloquentQueryBuilder
{
    public function delivered(): self
    {
        return $this->where('status', EmailStatus::DELIVERED->value);
    }

    public function deferred(): self
    {
        return $this->where('status', EmailStatus::DEFERRED->value);
    }

    public function bounced(): self
    {
        return $this->where('status', EmailStatus::BOUNCED->value);
    }

    public function sent(): self
    {
        return $this->where('status', EmailStatus::SENT->value);
    }

    public function failed(): self
    {
        return $this->where('status', EmailStatus::FAILED->value);
    }

    public function user(ObjectId|string $userId): self
    {
        return $this->whereId($userId, 'user._id');
    }

    public function email(ObjectId|string $emailId): self
    {
        return $this->whereId($emailId, '_id');
    }

    public function to(string $email): self
    {
        return $this->where('to', $email);
    }

    public function processed(): self
    {
        return $this->whereIn('status', [
            EmailStatus::DELIVERED->value,
            EmailStatus::DEFERRED->value,
            EmailStatus::BOUNCED->value,
        ]);
    }
}
