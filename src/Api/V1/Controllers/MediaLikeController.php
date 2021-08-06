<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\MediaLike;
use Aparlay\Core\Api\V1\Resources\MediaLikeResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use MongoDB\BSON\ObjectId;

class MediaLikeController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @OA\Put(
     *     path="/v1/media/{id}/like",
     *     tags={"media"},
     *     summary="like a media",
     *     description="To like media you need to call this endpoint.",
     *     operationId="likeMedia",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="media id to like.",
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
     *         @OA\JsonContent(ref="#/components/schemas/MediaLike"),
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
    public function store(Media $media): Response
    {
        if (Gate::forUser(auth()->user())->denies('interact', $media->created_by)) {
            return $this->error('You cannot like this video at the moment.', [], Response::HTTP_FORBIDDEN);
        }

        $mediaLike = MediaLike::media($media->_id)->creator(auth()->user()->_id)->first();
        if (null === $mediaLike) {
            $mediaLike = new MediaLike([
                                           'creator' => ['_id' => new ObjectId(auth()->user()->_id)],
                                           'media_id' => new ObjectId($media->_id),
                                           'user_id' => new ObjectId(auth()->user()->_id),
                                       ]);
            $mediaLike->save();
            $mediaLike->refresh();

            return $this->response(new MediaLikeResource($mediaLike), '', Response::HTTP_CREATED);
        }

        return $this->response(new MediaLikeResource($mediaLike), '', Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete (
     *     path="/v1/media/{id}/like",
     *     tags={"media"},
     *     summary="un-like a media",
     *     description="To unlike media you need to call this endpoint.",
     *     operationId="unlikeMedia",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="media id to unlike.",
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
     */
    public function destroy(Media $media): Response
    {
        $mediaLike = MediaLike::media($media->_id)->creator(auth()->user()->_id)->first();
        if (null !== $mediaLike) {
            $mediaLike->delete();
        }

        return $this->response([], '', Response::HTTP_NO_CONTENT);
    }
}
