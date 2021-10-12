<?php

namespace Aparlay\Core\Api\V1\Models;

use Aparlay\Core\Models\Block as BlockBase;

/**
 * @OA\Schema()
 */
class Block extends BlockBase
{
    /*
     * @OA\Property(property="_id", type="string", example="60237caf5e41025e1e3c80b1")
     * @OA\Property(property="created_at", type="string", example="1612850111566")
     * @OA\Property(property="creator", ref="#/components/schemas/SimpleUser")
     * @OA\Property(property="user", ref="#/components/schemas/SimpleUser")
     */
}
