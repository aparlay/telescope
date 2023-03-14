<?php

namespace Aparlay\Core\Api\V1\Swagger\Headers;

/**
 * @OA\Header(
 *     header="X-Rate-Limit-Remaining",
 *     description="the remaining number of allowed requests within the current period",
 *     @OA\Schema(
 *         type="integer",
 *         format="int32"
 *     )
 * )
 */
class XRateLimitRemaining
{
}
