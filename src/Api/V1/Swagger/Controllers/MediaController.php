<?php

namespace Aparlay\Core\Api\V1\Swagger\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Get (
 *     path="/v1/media",
 *     tags={"Core | Media"},
 *     summary="list of media sorted by ML",
 *     description="To get a list of uploaded media by current user you need to call this endpoint.",
 *     operationId="mediaList",
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
 *     @OA\Parameter(
 *         name="type",
 *         in="query",
 *         example="following",
 *         description="type of feed.",
 *         required=false,
 *
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *
 *     @OA\Parameter(
 *         name="show_adult_content",
 *         in="query",
 *         example="ask",
 *         description="should we show adult content or filter them out? possible values for this field is 1,2,3,4,never,ask,topless,all",
 *         required=false,
 *
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *
 *     @OA\Parameter(
 *         name="filter_content_gender",
 *         in="query",
 *         example="female,male,0",
 *         description="comma separated content gender values. default is all genders of contents for the guests and selected content gender for login users. possible values for this field is 0,1,2,female,male,transgender",
 *         required=false,
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
 *                 @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/Media")),
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
 * @OA\PUT (
 *     path="/v1/media/watched",
 *     tags={"Core | Media"},
 *     summary="list of watched media",
 *     description="To store a list of watched media by current guest user you need to call this endpoint.",
 *     operationId="mediaWatched",
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
 *     @OA\Parameter(
 *         name="medias",
 *         in="query",
 *         description="list of media id and duration",
 *         required=true,
 *
 *         @OA\Schema(
 *             type="array",
 *
 *             @OA\Items(ref="#/components/schemas/MediaWatched")
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
 * @OA\Get (
 *     path="/v1/user/{user_id}/media",
 *     tags={"user"},
 *     summary="list of uploaded media by given user",
 *     description="To get a list of uploaded media by current user you need to call this endpoint.",
 *     operationId="userMediaList",
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
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         description="list of media uploaded by the given {user_id} it can be user if the current logged in user",
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
 *                 @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/Media")),
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
 * @OA\Post(
 *     path="/v1/media",
 *     tags={"Core | Media"},
 *     summary="create new media",
 *     description="To upload and create new media you need to call this endpoint.",
 *     operationId="createMedia",
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
 *     @OA\Parameter(
 *         name="file",
 *         in="query",
 *         description="file can be form-data/multipart or it can send as string of filename which is generate via upload-token endpoint.",
 *         required=true,
 *
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *
 *     @OA\Parameter(
 *         name="description",
 *         in="query",
 *         description="description of the file is a string that may contain #hashtags or @mention we capture first 20 items of each one in backend and put them into separate arrays in media object",
 *         required=true,
 *
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *
 *     @OA\Parameter(
 *         name="visibility",
 *         in="query",
 *         description="the visibility of the media it can be 1 for public which is set by default or 0 for private",
 *         required=false,
 *
 *         @OA\Schema(
 *             type="integer"
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
 *         @OA\JsonContent(
 *
 *             @OA\Property(property="data", ref="#/components/schemas/Media"),
 *             @OA\Property(property="message", format="string", example="Entity has been created successfully!"),
 *             @OA\Property(property="code", format="integer", example=201),
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
 * @OA\Get (
 *     path="/v1/media/{id}",
 *     tags={"Core | Media"},
 *     summary="view media",
 *     description="To view a media description you need to call this endpoint.",
 *     operationId="viewMedia",
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="id of the media.",
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
 *                 ref="#/components/schemas/Media"
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
 * @OA\Get (
 *     path="/v1/media/share/{slug}",
 *     tags={"Core | Media"},
 *     summary="view media by slug",
 *     description="To view a media description you need to call this endpoint.",
 *     operationId="viewMediaBySlug",
 *
 *     @OA\Parameter(
 *         name="slug",
 *         in="path",
 *         description="slug of the media.",
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
 *                 ref="#/components/schemas/Media"
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
 * @OA\Patch (
 *     path="/v1/media/{id}",
 *     tags={"Core | Media"},
 *     summary="update media",
 *     description="To update a media description you need to call this endpoint.",
 *     operationId="updateMedia",
 *     security={{"bearerAuth": {}}},
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="id of the media that is going to be updated.",
 *         required=true,
 *
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *
 *     @OA\Parameter(
 *         name="is_comments_enabled",
 *         in="query",
 *         description="Is comments enabled for this video",
 *         required=false,
 *
 *         @OA\Schema(
 *             type="boolean"
 *         )
 *     ),
 *
 *     @OA\Parameter(
 *         name="visibility",
 *         in="query",
 *         description="the visibility of the media it can be 1 for public which is set by default or 0 for private",
 *         required=false,
 *
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *
 *     @OA\Parameter(
 *         name="description",
 *         in="query",
 *         description="id of the media that is going to be updated.",
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
 *                 ref="#/components/schemas/Media"
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
 * @OA\Delete (
 *     path="/v1/media/{id}",
 *     tags={"Core | Media"},
 *     summary="delete media",
 *     description="To delete a media description you need to call this endpoint.",
 *     operationId="deleteMedia",
 *     security={{"bearerAuth": {}}},
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="id of the media that is going to be updated.",
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
 *     ),
 * )
 *
 * @OA\Post(
 *     path="/v1/media/upload/stream",
 *     tags={"Core | Media"},
 *     summary="upload a new movie",
 *     description="To upload a new media file you need to call this endpoint.",
 *     operationId="uploadMediaFileStream",
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
 *         @OA\JsonContent(
 *
 *             @OA\Property(property="data", ref="#/components/schemas/Media"),
 *             @OA\Property(property="message", format="string", example="Entity has been created successfully!"),
 *             @OA\Property(property="code", format="integer", example=201),
 *             @OA\Property(property="status", format="string", example="OK")
 *         ),
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
 *         response=400,
 *         description="Bad Request",
 *
 *         @OA\JsonContent(ref="#/components/schemas/400"),
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
 * @OA\Get(
 *     path="/v1/media/upload/split",
 *     tags={"Core | Media"},
 *     summary="upload a new movie",
 *     description="To upload a new media file you need to call this endpoint.",
 *     operationId="uploadMediaFile",
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
 *         @OA\JsonContent(
 *
 *             @OA\Property(property="data", ref="#/components/schemas/Media"),
 *             @OA\Property(property="message", format="string", example="Entity has been created successfully!"),
 *             @OA\Property(property="code", format="integer", example=201),
 *             @OA\Property(property="status", format="string", example="OK")
 *         ),
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
 *         response=400,
 *         description="Bad Request",
 *
 *         @OA\JsonContent(ref="#/components/schemas/400"),
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
class MediaController
{
}
