<?php

namespace Aparlay\Core\Api\V1\Swagger\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Put(
 *     path="/v1/alert/{id}",
 *     tags={"Core | Alert"},
 *     summary="update an alert",
 *     description="To alert you need to call this endpoint.",
 *     operationId="updateAlert",
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="alert id to update.",
 *         required=true,
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="X-DEVICE-ID",
 *         in="header",
 *         description="unique id of the device user is going to send this request it can be segment.com anonymousId.",
 *         required=true,
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="successful operation",
 *         @OA\Header(
 *             header="X-Rate-Limit-Limit",
 *             description="the maximum number of allowed requests during a period",
 *             @OA\Schema(
 *                 type="integer",
 *                 format="int32"
 *             )
 *         ),
 *         @OA\Header(
 *             header="X-Rate-Limit-Remaining",
 *             description="the remaining number of allowed requests within the current period",
 *             @OA\Schema(
 *                 type="integer",
 *                 format="int32"
 *             )
 *         ),
 *         @OA\Header(
 *             header="X-Rate-Limit-Reset",
 *             description="the number of seconds to wait before having maximum number of allowed requests again",
 *             @OA\Schema(
 *                 type="integer",
 *                 format="int32"
 *             )
 *         ),
 *         @OA\JsonContent(ref="#/components/schemas/Alert"),
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(ref="#/components/schemas/401"),
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="DATA VALIDATION FAILED",
 *         @OA\JsonContent(ref="#/components/schemas/422"),
 *     ),
 *     @OA\Response(
 *         response=429,
 *         description="TOO MANY REQUESTS",
 *         @OA\JsonContent(ref="#/components/schemas/429"),
 *     ),
 * )
 */
class AlertController
{
}
