<?php

namespace Aparlay\Core\Api\V1\Swagger\Definitions;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(type="object")
 */
class Register
{
    /**
     * @OA\Property(ref="#/components/schemas/User")
     */
    public $data;

    /**
     * @OA\Property(format="string", example="Entity has been created successfully!")
     */
    public string $message;

    /**
     * @OA\Property(format="integer", example=201)
     */
    public int $code;

    /**
     * @OA\Property(format="string", example="OK")
     */
    public string $status;
}
