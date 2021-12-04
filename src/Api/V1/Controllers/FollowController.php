<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Resources\FollowResource;
use Aparlay\Core\Api\V1\Services\FollowService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class FollowController extends Controller
{
    protected $followService;

    public function __construct(FollowService $followService)
    {
        $this->followService = $followService;
    }

    /**
     */
    public function store(User $user): Response
    {
        if (Gate::forUser(auth()->user())->denies('interact', $user->_id)) {
            return $this->error('You cannot follow this user at the moment.', [], Response::HTTP_FORBIDDEN);
        }

        $response = $this->followService->follow($user);

        return $this->response(new FollowResource($response['data']), '', $response['statusCode']);
    }

    /**
     */
    public function destroy(User $user): Response
    {
        // Unfollow the user or throw exception if not followed
        $response = $this->followService->unfollow($user);

        return $this->response($response, '', Response::HTTP_NO_CONTENT);
    }
}
