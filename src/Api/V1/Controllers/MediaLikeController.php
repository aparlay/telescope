<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\MediaLike;
use Illuminate\Http\JsonResponse;
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
     *
     * @param  Media  $media
     * @return JsonResponse
     */
    public function store(Media $media): JsonResponse
    {
        if (Gate::forUser(auth()->user())->denies('interact', $media->created_by)) {
            $this->error('You cannot like this video at the moment.', [], 403);
        }

        $mediaLike = MediaLike::media($media->_id)->creator(auth()->user()->_id)->first();
        if ($mediaLike === null) {
            $model = new MediaLike([
                'creator' => ['_id' => new ObjectId(auth()->user()->_id)],
                'media_id' => new ObjectId($media->_id),
                'user_id' => new ObjectId(auth()->user()->_id),
            ]);
            $model->save();
            return $this->response($model, '', 201);
        }

        return $this->response($mediaLike, '', 200);
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
     *
     * @param  Media  $media
     * @return JsonResponse
     */
    public function destroy(Media $media): JsonResponse
    {
        $mediaLike = MediaLike::media($media->_id)->creator(auth()->user()->_id)->firstOrFail();
        if ($mediaLike === null) {
            $mediaLike->delete();
        }

        return $this->response([], '', 204);
    }
}
