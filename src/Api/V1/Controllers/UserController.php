<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\Block;
use Aparlay\Core\Api\V1\Resources\RegisterResource;
use Aparlay\Core\Models\User;
use Aparlay\Core\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class UserController extends Controller
{
    public $token = true;

    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->authorizeResource(User::class, 'user');
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
     * @param  Request  $request
     * @return object
     */
    public function update(Request $request): object
    {
        $user = auth()->user();

        if ($user->status == User::STATUS_VERIFIED && ! empty($request->username)) {
            $user->username = $request->username;
            $user->status = User::STATUS_ACTIVE;
            $user->save();
        } elseif ($request->hasFile('avatar')) {
            UserService::uploadAvatar($request, $user);
        } else {
            $requestData = $request->all();
            $user->fill($requestData)->save();
        }

        return $this->response(
            new RegisterResource($user),
            Response::HTTP_OK
        );
    }
}
