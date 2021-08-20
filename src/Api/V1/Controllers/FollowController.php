<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\Follow;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Resources\FollowResource;
use Aparlay\Core\Repositories\FollowRepository;
use Aparlay\Core\Services\FollowService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use MongoDB\BSON\ObjectId;

class FollowController extends Controller
{
    // public function __construct()
    // {
    //     $this->repository = new FollowRepository(new Follow());
    // }

    /**
     * @OA\Put(
     *     path="/v1/user/{id}/follow",
     *     tags={"connect"},
     *     summary="follow a user",
     *     description="To followe a user you need to call this endpoint.",
     *     operationId="follow",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="user id to follow.",
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
     *         @OA\JsonContent(ref="#/components/schemas/Follow"),
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
    public function store(User $user): Response
    {
        if (Gate::forUser(auth()->user())->denies('interact', $user->_id)) {
            return $this->error('You cannot follow this user at the moment.', [], Response::HTTP_FORBIDDEN);
        }

        // $followed = FollowService::isfollowed($user);
        // if (!$followed) {
        //     $follow = $this->repository->followerUser($user);
        //     return $this->response(new FollowResource($follow), '', Response::HTTP_CREATED);
        // }
        // return $this->response(new FollowResource($followed), '', Response::HTTP_OK);

        $follow = FollowService::followUser($user);
        if (! $follow['status']) {
            return $this->response(new FollowResource($follow['data']), '', Response::HTTP_CREATED);
        }

        return $this->response(new FollowResource($follow['data']), '', Response::HTTP_OK);
    }

    /**
     * @OA\Delete (
     *     path="/v1/user/{id}/follow",
     *     tags={"connect"},
     *     summary="unfollow a user",
     *     description="To unfollow a user you need to call this endpoint.",
     *     operationId="unfollow",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="user id to unfollow.",
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
    public function destroy(User $user): Response
    {
        $follow = Follow::user($user->_id)->creator(auth()->user()->_id)->firstOrFail();
        $follow?->delete();

        return $this->response([], '', Response::HTTP_NO_CONTENT);
    }
}
