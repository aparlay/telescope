<?php

namespace Aparlay\Core\Api\V1\Models;

use Aparlay\Core\Models\MediaLike as MediaLikeBase;

/**
 * Class MediaLike.
 *
 *
 * @OA\Schema()
 */
class MediaLike extends MediaLikeBase
{
    /*
     * @OA\Property(property="_id", type="string", example="60237caf5e41025e1e3c80b1")
     * @OA\Property(property="created_at", type="string", example="1612850111566")
     * @OA\Property(property="creator", ref="#/components/schemas/SimpleUser")
     * @OA\Property(property="media_id", type="string", example="60237caf5e41025e1e3c80b1")
     * @OA\Property(property="user_id", type="string", example="60237caf5e41025e1e3c80b1")
     */
}
