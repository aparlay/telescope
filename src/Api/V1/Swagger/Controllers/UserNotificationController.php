<?php

namespace Aparlay\Core\Api\V1\Swagger\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/v1/user-notification",
 *     tags={"Core | Notification"},
 *     summary="Fetch all user notifications for current user",
 *     description="Fetch all user notifications for current user",
 *     operationId="fetchUserNotificationList",
 *     security={{"bearerAuth": {}}},
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
 *         response=200,
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
 *         @OA\JsonContent(
 *
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/UserNotification")),
 *                 @OA\Property(property="_links", ref="#/components/schemas/ListLinks"),
 *                 @OA\Property(property="_meta", ref="#/components/schemas/Meta")
 *             ),
 *             @OA\Property(property="code", format="integer", example=200),
 *             @OA\Property(property="status", format="string", example="OK")
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
 *     ),
 * )
 *
 * @OA\Put (
 *     path="/v1/user-notification/read",
 *     tags={"Core | Notification"},
 *     summary="mark a notication as readed",
 *     description="This endpoint simply change user notification status to visited",
 *     operationId="userNotificationRead",
 *     security={{"bearerAuth": {}}},
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
 *     @OA\RequestBody(
 *         required=true,
 *
 *         @OA\JsonContent(
 *             required={"user_notification_ids"},
 *
 *             @OA\Property(
 *                  property="user_notification_ids",
 *                  type="array",
 *                  description="send empty array to mark all of them as read",
 *
 *                  @OA\Items(
 *                      type="string",
 *                      format="string",
 *                      example="62cbc0f38ba7f35c90196d35"
 *                  )
 *              ),
 *          ),
 *     ),
 *
 *     @OA\Parameter(
 *         name="user_notification_ids",
 *         in="query",
 *         description="array of user notifications to mark as readed",
 *         required=false,
 *
 *         @OA\Schema(
 *             type="array",
 *
 *              @OA\Items(
 *                 type="string",
 *                 format="string",
 *                 example="62cbc0f38ba7f35c90196d35"
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=202,
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
 *         @OA\JsonContent(
 *
 *             @OA\Property(property="code", format="integer", example=202),
 *             @OA\Property(property="status", format="string", example="ACCEPTED")
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
 *     ),
 * )
 */
class UserNotificationController
{
}
