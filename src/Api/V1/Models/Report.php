<?php

namespace Aparlay\Core\Api\V1\Models;

use Aparlay\Core\Models\Report as ReportBase;

/**
 * @OA\Schema()
 */
class Report extends ReportBase
{
    /**
     * @OA\Property(property="_id", type="string", example="60237caf5e41025e1e3c80b1")
     * @OA\Property(property="user_id", type="string", example=null)
     * @OA\Property(property="media_id", type="string", example="60237caf5e41025e1e3c80b1")
     * @OA\Property(property="comment_id", type="string", example=null)
     * @OA\Property(property="reason", type="string", example="The video is bad")
     * @OA\Property(property="created_at", type="string", example="1612850111566")
     * @OA\Property(property="updated_at", type="string", example="1612850111566")
     */
}
