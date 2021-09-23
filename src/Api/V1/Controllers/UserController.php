<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Requests\MeRequest;
use Aparlay\Core\Api\V1\Resources\MeResource;
use Aparlay\Core\Api\V1\Resources\UserResource;
use Aparlay\Core\Api\V1\Services\UserService;
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
     * Remove the specified resource from storage.
     *
     * @return Response
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
        } else {
            return $user;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  MeRequest  $request
     * @return object
     * @throws ValidationException
     */
    public function update(MeRequest $request): object
    {
        $user = $this->userService->findByIdentity(auth()->user()->username);
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
        return $this->response(
            new MeResource($user),
            Response::HTTP_OK
        );
    }

    public function show(User $user): Response
    {
        if ($this->userService->isUserEligible($user)) {
            return $this->response(new UserResource($user));
        }

        return $this->error('Account not found!', [], Response::HTTP_NOT_FOUND);
    }
}
