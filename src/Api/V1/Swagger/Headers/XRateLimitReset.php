<?php

namespace Aparlay\Core\Api\V1\Swagger\Headers;

/**
 * @OA\Header(
 *             header="X-Rate-Limit-Reset",
 *             description="the number of seconds to wait before having maximum number of allowed requests again",
 *
 *             @OA\Schema(
 *                 type="integer",
 *                 format="int32"
 *             )
 *         )
 */
class XRateLimitReset
{
}
