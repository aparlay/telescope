<?php

namespace Aparlay\Core\Api\V1\Swagger\Definitions;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(type="object", title="List Links", description="List Links response")
 */
class ListLinks
{
    /**
     * @OA\Property(
     *     type="object",
     *     @OA\Property(property="href", type="string", example="https://api.waptap.test/v1/media/list/602e125eb2a01c3838414439?page=1" ),
     * )
     */
    public $self;

    /**
     * @OA\Property(
     *     type="object",
     *     @OA\Property(property="href", type="string", example="https://api.waptap.test/v1/media/list/602e125eb2a01c3838414439?page=1" ),
     * )
     */
    public $first;

    /**
     * @OA\Property(
     *     type="object",
     *     @OA\Property(property="href", type="string", example="https://api.waptap.test/v1/media/list/602e125eb2a01c3838414439?page=1" ),
     * )
     */
    public $last;

    /**
     * @OA\Property(
     *     type="object",
     *     @OA\Property(property="href", type="string", example="https://api.waptap.test/v1/media/list/602e125eb2a01c3838414439?page=1" ),
     * )
     */
    public $next;

    /**
     * @OA\Property(
     *     type="object",
     *     @OA\Property(property="href", type="string", example="https://api.waptap.test/v1/media/list/602e125eb2a01c3838414439?page=1" ),
     * )
     */
    public $prev;
}
