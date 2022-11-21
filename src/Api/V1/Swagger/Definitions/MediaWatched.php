<?php

namespace Aparlay\Core\Api\V1\Swagger\Definitions;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(type="object", title="MediaWatched", description="Media Watched")
 */
class MediaWatched
{
    /**
     * @OA\Property(
     *     type="string",
     *     example="60237caf5e41025e1e3c80b1",
     * )
     */
    public $media_id;

    /**
     * @OA\Property(
     *     type="number",
     *     example=14.32423
     * )
     */
    public $duration;
}
