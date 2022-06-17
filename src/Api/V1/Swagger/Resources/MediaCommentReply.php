<?php

namespace Aparlay\Core\Api\V1\Swagger\Resources;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema()
 */
class MediaCommentReply
{
    /**
     * @OA\Property(property="_id", type="string", example="60237caf5e41025e1e3c80b1")
     */
    public $_id;

    /**
     * @OA\Property(property="media_id", type="string", example="60237caf5e41025e1e3c80b1")
     */
    public $media_id;

    /**
     * @OA\Property(property="text", type="string", example="In this universe there might grow roses which sing"),
     */
    public $text;

    /**
     * @OA\Property(property="is_liked", type="boolean", description="Is liked by current user", example="true")
     */
    public $is_liked;

    /**
     * @OA\Property(property="likes_count", type="int", description="Number of likes for this comment", example="5")
     */
    public $likes_count;

    /**
     * @OA\Property(property="replies_count", type="int", description="Number of replies for this comment", example="10")
     */
    public $replies_count;

    /**
     * @OA\Property(property="parent_id", type="string", example="60237caf5e41025e1e3c80b1", description="Only replies have parent_id")
     */
    public $parent_id;

    /**
     * @OA\Property(property="created_at", type="string", example="1612850111566")
     */
    public $created_at;

    /**
     * @OA\Property(property="creator", ref="#/components/schemas/SimpleUser")
     */
    public $creator;
}
