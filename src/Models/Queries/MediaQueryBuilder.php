<?php

namespace Aparlay\Core\Models\Queries;

use Aparlay\Core\Models\Enums\MediaStatus;

final class MediaQueryBuilder extends EloquentQueryBuilder
{
    public function video(): self
    {
        return $this->where('type', 'video');
    }

    public function confirmed(): self
    {
        return $this->where('status', MediaStatus::CONFIRMED->value);
    }
}
