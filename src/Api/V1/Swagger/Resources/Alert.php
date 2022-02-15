<?php

namespace Aparlay\Core\Api\V1\Swagger\Resources;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema()
 *
 */
class Alert
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
     * @OA\Property(property="type", type="integer", description="user_notice=0, media_remove=20, media_notice=21", example=21)
     */
    public $type;

    /**
     * @OA\Property(property="title", type="string", example="Video Notice")
     */
    public $title;

    /**
     * @OA\Property(property="reason", type="string", example="This video get noticed due to adult content")
     */
    public $reason;

    /**
     * @OA\Property(property="status", type="integer", description="not_visited=0, visited=1", example=1)
     */
    public $status;

    /**
     * @OA\Property(property="created_at", type="string", example="1612850111566")
     */
    public $created_at;
}
