<?php

namespace Aparlay\Core\Api\V1\Swagger\Resources;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema()
 */
class AlertUserDocument
{
    /**
     * @OA\Property(property="_id", type="string", example="60237caf5e41025e1e3c80b1")
     */
    public $_id;

    /**
     * @OA\Property(property="type", type="integer", description="50 - user document rejected")
     */
    public $type;

    /**
     * @OA\Property(property="status", type="integer", description="not_visited=0, visited=1", example="1")
     */
    public $status;

    /**
     * @OA\Property(property="reason", type="string", example="This document is too blurry")
     */
    public $reason;

    /**
     * @OA\Property(property="created_at", type="string", example="1612850111566")
     */
    public $created_at;
}
