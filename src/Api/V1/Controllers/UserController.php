<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\Block;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Requests\MeRequest;
use Aparlay\Core\Api\V1\Resources\MeResource;
use Aparlay\Core\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public $token = true;

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
     * @param  Block  $media
     * @return Response
     */
    public function me(Block $media): Response
    {
        return $this->response([], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Block  $media
     * @return Response
     */
    public function destroy(Block $media): Response
    {
        return $this->response([], Response::HTTP_OK);
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
        /** Check the update permission */
        $user = auth()->user();
        $this->authorizeResource(User::class, 'user');

        /* Update User Avatar */
        if ($request->hasFile('avatar')) {
            UserService::uploadAvatar($request, $user);
        } elseif (count($request->all())) {
            /* Update User Profile Information */
            $user->fill($request->all());
            if ($user->status == User::STATUS_VERIFIED && ! empty($request->username)) {
                $user->status = User::STATUS_ACTIVE;
            }
            $user->save();
        }

        /* Return the updated user data */
        return $this->response(
            new MeResource($user),
            Response::HTTP_OK
        );
    }
}
