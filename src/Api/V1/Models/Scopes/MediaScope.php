<?php

namespace Aparlay\Core\Api\V1\Models\Scopes;

use Aparlay\Core\Api\V1\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class MediaScope implements Scope
{

    public function apply(Builder $builder, Model $model)
    {
        $builder->whereIn('status', [
            Media::STATUS_QUEUED,
            Media::STATUS_UPLOADED,
            Media::STATUS_IN_PROGRESS,
            Media::STATUS_COMPLETED,
            Media::STATUS_FAILED,
            Media::STATUS_CONFIRMED,
            Media::STATUS_DENIED,
            Media::STATUS_ADMIN_DELETED,
            Media::STATUS_IN_REVIEW,
            Media::STATUS_ADMIN_DELETED,
        ]);
    }
}
