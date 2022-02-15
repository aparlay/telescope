<?php

namespace Aparlay\Core\Api\V1\Swagger\Resources;

use OpenApi\Annotations as OA;

/**
 * Class Follow.
 *
 * @OA\Schema()
 */
class Follow
{
    /**
     * @OA\Property(property="_id", type="string", example="60237caf5e41025e1e3c80b1")
     */
    public $_id;

    /**
     * @OA\Property(property="status", type="integer", example=1, description="ACCEPTED = 1, PENDING = 0")
     */
    public $status;

    /**
     * @OA\Property(property="created_at", type="string", example="1612850111566")
     */
    public $created_at;

    /**
     * @OA\Property(property="creator", ref="#/components/schemas/SimpleUser")
     */
    public $creator;

    /**
     * @OA\Property(property="user", ref="#/components/schemas/SimpleUser")
     */
    public $user;
}
