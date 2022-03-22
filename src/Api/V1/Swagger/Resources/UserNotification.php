<?php

namespace Aparlay\Core\Api\V1\Swagger\Resources;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema()
 */
class UserNotification
{
    /**
     * @OA\Property(property="_id", type="string", example="60237caf5e41025e1e3c80b1")
     */
    public $_id;

    /**
     * @OA\Property(property="category", type="integer", description="id_card=0, selfie=1", example="1")
     */
    public $category;

    /**
     * @OA\Property(property="category_label", type="string", description="id_card, selfie", example="media like")
     */
    public $category_label;

    /**
     * @OA\Property(property="status", type="integer", description="visited=1, not_visited=-1", example="1")
     */
    public $status;

    /**
     * @OA\Property(property="status_label", type="string", description="visited, not visited", example="visited")
     */
    public $status_label;

    /**
     * @OA\Property(property="entity", type="object", @OA\Property (ref="#/components/schemas/Media"))
     */
    public $entity;
}
