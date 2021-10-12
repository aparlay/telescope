<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Requests\MeRequest;
use Aparlay\Core\Api\V1\Resources\MeResource;
use Aparlay\Core\Api\V1\Resources\UserResource;
use Aparlay\Core\Api\V1\Services\UserService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public $token = true;
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->response([], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/v1/me",
     *     tags={"user"},
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
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function me(): Response
    {
        $user = auth()->user();

        return $this->response(new MeResource($user), Response::HTTP_OK);
    }

    /**
     * @OA\Delete (
     *     path="/v1/me",
     *     tags={"user"},
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
     * Remove the specified resource from storage.
     *
     * @return Response
     * @throws AuthorizationException
     */
    public function destroy(): Response
    {
        /* Check the update permission */
        $this->authorize('delete', User::class);

        $user = auth()->user();

        if ($this->userService->deleteAccount($user)) {
            $cookie1 = Cookie::forget('__Secure_token');
            $cookie2 = Cookie::forget('__Secure_refresh_token');
            $cookie3 = Cookie::forget('__Secure_username');

            return $this->response([], '', Response::HTTP_NO_CONTENT)
                ->cookie($cookie1)
                ->cookie($cookie2)
                ->cookie($cookie3);
        }

        return $user;
    }

    /**
     * @OA\Patch(
     *     path="/v1/me",
     *     tags={"user"},
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
     * Remove the specified resource from storage.
     *
     * @param  MeRequest  $request
     * @return object
     * @throws ValidationException
     */
    public function update(MeRequest $request): object
    {
        $user = auth()->user();
        /* Check the update permission */
        $this->authorize('update', User::class);

        /* Update User Avatar */
        if ($request->hasFile('avatar')) {
            $this->userService->uploadAvatar($request, $user);
        } else {
            if ($request->has('avatar') && empty($request->avatar)) {
                $request->merge(['avatar' => $this->userService->changeDefaultAvatar($request)]);
            }
            /* Update User Profile Information */
            $user->fill($request->all());
            if ($user->status == User::STATUS_VERIFIED && ! empty($request->username)) {
                $user->status = User::STATUS_ACTIVE;
            }
            $user->save();
            $user->refresh();
        }

        /* Return the updated user data */
        return $this->response(new MeResource($user), Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/v1/user/{user_id}",
     *     tags={"user"},
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
     * @param  User  $user
     * @return Response
     */
    public function show(User $user): Response
    {
        if ($this->userService->isUserEligible($user)) {
            return $this->response(new UserResource($user));
        }

        return $this->error('Account not found!', [], Response::HTTP_NOT_FOUND);
    }
}
