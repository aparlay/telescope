<?php

namespace Aparlay\Core\Api\V1\Swagger\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Post(
 *     path="/v1/user-document",
 *     tags={"user-document"},
 *     summary="create new user document for verification: id card or selfie",
 *     description="To upload and create new user verication document you need to call this endpoint.",
 *     operationId="createUserDocument",
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
 *     @OA\Parameter(
 *         name="file",
 *         in="query",
 *         description="file can be form-data/multipart or it can send as string of filename which is generate via upload-token endpoint.",
 *         required=true,
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="type",
 *         in="query",
 *         description="type of sending verification document, it could be 0=card or 1=selfie",
 *         required=true,
 *         @OA\Schema(
 *             type="int"
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=201,
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
 *             @OA\Property(property="data", ref="#/components/schemas/UserDocument"),
 *             @OA\Property(property="message", format="string", example="Entity has been created successfully!"),
 *             @OA\Property(property="code", format="integer", example=201),
 *             @OA\Property(property="status", format="string", example="OK")
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
 * @OA\GET(
 *     path="/v1/user-document/{document_id}",
 *     tags={"user-document"},
 *     summary="Fetch a signle user documents for current user",
 *     description="Fetch a single user documents for current user",
 *     operationId="fetchUsserDocument",
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="X-DEVICE-ID",
 *         in="header",
 *         description="unique id of the device user is going to send this request it can be segment.com anonymousId.",
 *         required=true,
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),     *
 *     @OA\Response(
 *         response=201,
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
 *        @OA\JsonContent(
 *             @OA\Property(property="data", type="object",
 *                 ref="#/components/schemas/UserDocument"
 *             ),
 *             @OA\Property(property="code", format="integer", example=200),
 *             @OA\Property(property="status", format="string", example="OK")
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
 * @OA\GET(
 *     path="/v1/user-document",
 *     tags={"user-document"},
 *     summary="Fetch all user documents for current user",
 *     description="Fetch all user documents for current user",
 *     operationId="fetchUsserDocument",
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="X-DEVICE-ID",
 *         in="header",
 *         description="unique id of the device user is going to send this request it can be segment.com anonymousId.",
 *         required=true,
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),     *
 *     @OA\Response(
 *         response=201,
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
 *             @OA\Property(property="data", type="array", @OA\Items (ref="#/components/schemas/UserDocument")),
 *             @OA\Property(property="code", format="integer", example=200),
 *             @OA\Property(property="status", format="string", example="OK")
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
 */
class UserDocumentController
{
}
