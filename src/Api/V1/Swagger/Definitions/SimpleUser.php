<?php

namespace Aparlay\Core\Api\V1\Swagger\Definitions;

use OpenApi\Annotations as OA;

/**
 * Class SimpleUser.
 *
 * @OA\Schema(title="SimpleUser")
 */
class SimpleUser
{
    /**
     * @OA\Property(example="602e125eb2a01c3838414439")
     */
    public string $_id;

    /**
     * @OA\Property(example="ramram")
     */
    public string $username;

    /**
     * @OA\Property(example="60237dacc7dd4171920af0e9_602a1aca94494.jpg")
     */
    public string $avatar;

    /**
     * @OA\Property(example=true)
     */
    public bool $is_followed;

    /**
     * @OA\Property(example=true)
     */
    public bool $is_liked;
}
