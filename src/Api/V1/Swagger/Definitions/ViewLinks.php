<?php

namespace Aparlay\Core\Api\V1\Swagger\Definitions;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(type="object", title="ViewLinks", description="View Links response")
 */
class ViewLinks
{
    /**
     * @OA\Property(
     *     type="object",
     *     @OA\Property(property="href", type="string", example="https://api.waptap.test/v1/media/602f5306613ed7487830eb14" ),
     * )
     */
    public $self;

    /**
     * @OA\Property(
     *     type="object",
     *     @OA\Property(property="href", type="string", example="https://api.waptap.test/v1/media/list/602f5306613ed7487830eb14" ),
     * )
     */
    public $index;
}
