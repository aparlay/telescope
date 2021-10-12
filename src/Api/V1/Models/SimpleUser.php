<?php
namespace Aparlay\Core\Api\V1\Models;

/**
 * Class SimpleUser
 * @package common\models
 *
 * @OA\Schema(title="SimpleUser")
 */
class SimpleUser
{
    /**
     * @var string
     * @OA\Property(example="602e125eb2a01c3838414439")
     */
    public string $_id;
    /**
     * @var string
     * @OA\Property(example="ramram")
     */
    public string $username;

    /**
     * @var string
     * @OA\Property(example="60237dacc7dd4171920af0e9_602a1aca94494.jpg")
     */
    public string $avatar;

    /**
     * @var bool
     * @OA\Property(example=true)
     */
    public bool $is_followed;

    /**
     * @var bool
     * @OA\Property(example=true)
     */
    public bool $is_liked;
}
