<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Dto\UserDeleteDTO;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Requests\MeRequest;
use Aparlay\Core\Api\V1\Resources\MeResource;
use Aparlay\Core\Api\V1\Resources\UserResource;
use Aparlay\Core\Api\V1\Services\MediaService;
use Aparlay\Core\Api\V1\Services\UserService;
use Aparlay\Core\Helpers\Country;
use Aparlay\Core\Models\Enums\UserStatus;
use Aparlay\Core\Models\Enums\UserVisibility;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\Rule;
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
     * @param  Request  $request
     * @return Response
     * @throws AuthorizationException
     */
    public function destroy(Request $request): Response
    {
        /* Check the update permission */
        $this->authorize('delete', User::class);

        $user = auth()->user();
        $this->userService->setUser($user);
        if ($this->userService->deleteAccount(UserDeleteDTO::fromRequest($request))) {
            $mediaService = app()->make(MediaService::class);
            $mediaService->setUser($user);
            $mediaService->deleteAllMediasBelongToUser();

            if (auth()->user()->getRememberToken()) {
                auth()->logout();
            }
            $cookie1 = Cookie::forget('__Secure_token');
            $cookie2 = Cookie::forget('__Secure_refresh_token');
            $cookie3 = Cookie::forget('__Secure_username');

            return $this->response([], '', Response::HTTP_NO_CONTENT)
                ->cookie($cookie1)
                ->cookie($cookie2)
                ->cookie($cookie3);
        }

        return $this->response([], '', Response::HTTP_NO_CONTENT);
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
        $user = auth()->user();

        if (auth()->check()) {
            $this->userService->setUser($user);
        }
        /* Check the update permission */
        $this->authorize('update', User::class);

        /* Update User Avatar */
        if ($request->hasFile('avatar')) {
            $this->userService->uploadAvatar($request);
        } else {
            if ($request->has('avatar') && empty($request->avatar)) {
                $request->merge(['avatar' => $this->userService->changeDefaultAvatar()]);
            }

            /* Update User Profile Information */
            $this->userService->getUser()->fill($request->all());
            if ($this->userService->getUser()->status == UserStatus::VERIFIED->value && ! empty($request->username)) {
                $this->userService->getUser()->status = UserStatus::ACTIVE->value;
            }
            $this->userService->getUser()->save();
        }
        $this->userService->getUser()->refresh();

        /* Return the updated user data */
        return $this->response(new MeResource($this->userService->getUser()), Response::HTTP_OK);
    }

    /**
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
