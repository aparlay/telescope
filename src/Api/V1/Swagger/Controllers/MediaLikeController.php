<?php

namespace Aparlay\Core\Api\V1\Swagger\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Put(
 *     path="/v1/media/{id}/like",
 *     tags={"Core | Media"},
 *     summary="like a media",
 *     description="To like media you need to call this endpoint.",
 *     operationId="likeMedia",
 *     security={{"bearerAuth": {}}},
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="media id to like.",
 *         required=true,
 *
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *
 *     @OA\Parameter(
 *         name="X-DEVICE-ID",
 *         in="header",
 *         description="unique id of the device user is going to send this request it can be segment.com anonymousId.",
 *         required=true,
 *
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=201,
 *         description="successful operation",
 *
 *         @OA\Header(
 *             header="X-Rate-Limit-Limit",
 *             description="the maximum number of allowed requests during a period",
 *
 *             @OA\Schema(
 *                 type="integer",
 *                 format="int32"
 *             )
 *         ),
 *
 *         @OA\Header(
 *             header="X-Rate-Limit-Remaining",
 *             description="the remaining number of allowed requests within the current period",
 *
 *             @OA\Schema(
 *                 type="integer",
 *                 format="int32"
 *             )
 *         ),
 *
 *         @OA\Header(
 *             header="X-Rate-Limit-Reset",
 *             description="the number of seconds to wait before having maximum number of allowed requests again",
 *
 *             @OA\Schema(
 *                 type="integer",
 *                 format="int32"
 *             )
 *         ),
 *
 *         @OA\JsonContent(ref="#/components/schemas/MediaLike"),
 *     ),
 *
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *
 *         @OA\JsonContent(ref="#/components/schemas/401"),
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="DATA VALIDATION FAILED",
 *
 *         @OA\JsonContent(ref="#/components/schemas/422"),
 *     ),
 *
 *     @OA\Response(
 *         response=429,
 *         description="TOO MANY REQUESTS",
 *
 *         @OA\JsonContent(ref="#/components/schemas/429"),
 *     ),
 * )
 *
 * @OA\Delete (
 *     path="/v1/media/{id}/like",
 *     tags={"Core | Media"},
 *     summary="un-like a media",
 *     description="To unlike media you need to call this endpoint.",
 *     operationId="unlikeMedia",
 *     security={{"bearerAuth": {}}},
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="media id to unlike.",
 *         required=true,
 *
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *
 *     @OA\Parameter(
 *         name="X-DEVICE-ID",
 *         in="header",
 *         description="unique id of the device user is going to send this request it can be segment.com anonymousId.",
 *         required=true,
 *
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=204,
 *         description="successful operation",
 *
 *         @OA\Header(
 *             header="X-Rate-Limit-Limit",
 *             description="the maximum number of allowed requests during a period",
 *
 *             @OA\Schema(
 *                 type="integer",
 *                 format="int32"
 *             )
 *         ),
 *
 *         @OA\Header(
 *             header="X-Rate-Limit-Remaining",
 *             description="the remaining number of allowed requests within the current period",
 *
 *             @OA\Schema(
 *                 type="integer",
 *                 format="int32"
 *             )
 *         ),
 *
 *         @OA\Header(
 *             header="X-Rate-Limit-Reset",
 *             description="the number of seconds to wait before having maximum number of allowed requests again",
 *
 *             @OA\Schema(
 *                 type="integer",
 *                 format="int32"
 *             )
 *         ),
 *     ),
 *
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *
 *         @OA\JsonContent(ref="#/components/schemas/401"),
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="DATA VALIDATION FAILED",
 *
 *         @OA\JsonContent(ref="#/components/schemas/422"),
 *     ),
 *
 *     @OA\Response(
 *         response=429,
 *         description="TOO MANY REQUESTS",
 *
 *         @OA\JsonContent(ref="#/components/schemas/429"),
 *     )
 * )
 */
class MediaLikeController
{
}
