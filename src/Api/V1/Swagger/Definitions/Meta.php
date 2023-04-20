<?php

namespace Aparlay\Core\Api\V1\Swagger\Definitions;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(type="object", title="Meta", description="Meta response")
 */
class Meta
{
    /**
     * @OA\Property(type="integer", example=65 )
     */
    public $total_count;

    /**
     * @OA\Property(type="integer", example=4 )
     */
    public $page_count;

    /**
     * @OA\Property(type="integer", example=1 )
     */
    public $current_page;

    /**
     * @OA\Property(type="integer", example=20 )
     */
    public $per_page;
}
