<?php

namespace Aparlay\Core\Api\V1\Swagger\Parameters;

/**
 * @OA\Parameter(
 *         name="X-DEVICE-ID",
 *         in="header",
 *         description="unique id of the device user is going to send this request it can be segment.com anonymousId.",
 *         required=true,
 *         @OA\Schema(
 *             type="string"
 *         )
 *     )
 */
class XDeviceId
{

}