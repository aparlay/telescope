<?php

namespace Aparlay\Core\Api\V1\Swagger\Resources;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema()
 */
class MediaLike
{
    /**
     * @OA\Property(property="_id", type="string", example="60237caf5e41025e1e3c80b1")
     */
    public $_id;

    /**
     * @OA\Property(property="media_id", type="string", example="60237caf5e41025e1e3c80b1")
     */
    public $media_id;

    /**
     * @OA\Property(property="user_id", type="string", example="60237caf5e41025e1e3c80b1")
     */
    public $user_id;

    /**
     * @OA\Property(property="created_at", type="string", example="1612850111566")
     */
    public $created_at;

    /**
     * @OA\Property(property="creator", ref="#/components/schemas/SimpleUser")
     */
    public $creator;
}
