<?php

namespace Aparlay\Core\Api\V1\Models\Scopes;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Models\Enums\MediaStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class MediaScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->whereIn('status', [
            MediaStatus::QUEUED->value,
            MediaStatus::UPLOADED->value,
            MediaStatus::IN_PROGRESS->value,
            MediaStatus::COMPLETED->value,
            MediaStatus::FAILED->value,
            MediaStatus::CONFIRMED->value,
            MediaStatus::DENIED->value,
            MediaStatus::IN_REVIEW->value,
            MediaStatus::ADMIN_DELETED->value,
        ]);
    }
}
