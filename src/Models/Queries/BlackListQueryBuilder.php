<?php

namespace Aparlay\Core\Models\Queries;

use Aparlay\Core\Models\Enums\BlackListType;

class BlackListQueryBuilder extends EloquentQueryBuilder
{
    /**
     * @return $this
     */
    public function temporaryEmailService(): self
    {
        return $this->where('type', BlackListType::TEMPORARY_EMAIL_SERVICE->value);
    }

    /**
     * @return $this
     */
    public function payload(string $payload): self
    {
        return $this->where('payload', $payload);
    }
}