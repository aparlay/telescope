<?php

namespace Aparlay\Core\Api\V1\Swagger\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/v1/me",
 *     tags={"Core | User"},
 *     summary="Get current user data",
 *     description="Fetch current login user information.",
 *     operationId="me",
 *     security={{"bearerAuth": {}}},
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
 *         @OA\JsonContent(
 *              @OA\Property(
 *                  property="data",
 *                  type="object",
 *                  ref="#/components/schemas/Me"
 *              ),
 *              @OA\Property(
 *                  property="status",
 *                  type="string",
 *                  example="OK"
 *              ),
 *              @OA\Property(
 *                  property="code",
 *                  type="integer",
 *                  example=200
 *              )
 *         )
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
 *
 * @OA\Post (
 *     path="/v1/me/delete",
 *     tags={"Core | User"},
 *     summary="deactive a user",
 *     description="To deactive a user you need to call this endpoint.",
 *     operationId="deactiveUser",
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="X-DEVICE-ID",
 *         in="header",
 *         description="unique id of the device user is going to send this request it can be segment.com anonymousId.",
 *         required=true,
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     example="why user want to delete their accounts",
 *                     property="reason",
 *                     type="string"
 *                 ),
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=204,
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
 *
 * @OA\Patch(
 *     path="/v1/me",
 *     tags={"Core | User"},
 *     summary="update current user profile",
 *     description="To update user profile you can send your request to this endpoint.",
 *     operationId="updateProfile",
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="X-DEVICE-ID",
 *         in="header",
 *         description="unique id of the device user is going to send this request it can be segment.com anonymousId.",
 *         required=true,
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     example="A short string to show as bio in user profile",
 *                     property="bio",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     example="John Walker",
 *                     property="full_name",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     example="male",
 *                     property="gender",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     example=1,
 *                     property="visibility",
 *                     type="integer"
 *                 ),
 *                 @OA\Property(
 *                     description="file to upload",
 *                     property="avatar",
 *                     type="string",
 *                     format="file",
 *                 ),
 *             )
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
 *         @OA\JsonContent(ref="#/components/schemas/User"),
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
 *
 * @OA\Get(
 *     path="/v1/user/{user_id}",
 *     tags={"Core | User"},
 *     summary="Get the user data",
 *     description="Fetch the user information.",
 *     operationId="userView",
 *     @OA\Parameter(
 *         name="X-DEVICE-ID",
 *         in="header",
 *         description="unique id of the device user is going to send this request it can be segment.com anonymousId.",
 *         required=true,
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         description="user id or username.",
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
 *         @OA\JsonContent(
 *              @OA\Property(
 *                  property="data",
 *                  type="object",
 *                  ref="#/components/schemas/User"
 *              ),
 *              @OA\Property(
 *                  property="status",
 *                  type="string",
 *                  example="OK"
 *              ),
 *              @OA\Property(
 *                  property="code",
 *                  type="integer",
 *                  example=200
 *              )
 *         )
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
 *
 * @OA\Get(
 *     path="/v1/user/share/{username}",
 *     tags={"Core | User"},
 *     summary="Get the user data",
 *     description="Fetch the user information.",
 *     operationId="userView",
 *     @OA\Parameter(
 *         name="X-DEVICE-ID",
 *         in="header",
 *         description="unique id of the device user is going to send this request it can be segment.com anonymousId.",
 *         required=true,
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="username",
 *         in="path",
 *         description="user username.",
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
 *         @OA\JsonContent(
 *              @OA\Property(
 *                  property="data",
 *                  type="object",
 *                  ref="#/components/schemas/User"
 *              ),
 *              @OA\Property(
 *                  property="status",
 *                  type="string",
 *                  example="OK"
 *              ),
 *              @OA\Property(
 *                  property="code",
 *                  type="integer",
 *                  example=200
 *              )
 *         )
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
class UserController
{
}
