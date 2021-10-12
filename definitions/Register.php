<?php

namespace Aparlay\Core\definitions;


/**
 *
 * @OA\Schema(type="object")
 */
class Register
{
    /**
     * @OA\Property(ref="#/components/schemas/User")
     */
    public $data;
    /**
     * @var string
     * @OA\Property(format="string", example="Entity has been created successfully!")
     */
    public string $message;
    /**
     * @var int
     * @OA\Property(format="integer", example=201)
     */
    public int $code;
    /**
     * @var string
     * @OA\Property(format="string", example="OK")
     */
    public string $status;
}
