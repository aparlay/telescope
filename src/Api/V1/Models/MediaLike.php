<?php

namespace Aparlay\Core\Api\V1\Models;

use Aparlay\Core\Api\V1\Casts\SimpleUser;
use Aparlay\Core\Models\MediaLike as MediaLikeBase;

/**
 * Class MediaLike.
 */
class MediaLike extends MediaLikeBase
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'creator' => SimpleUser::class,
    ];
}
