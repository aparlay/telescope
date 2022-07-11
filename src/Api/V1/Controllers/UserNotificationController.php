<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\UserNotification;
use Aparlay\Core\Api\V1\Requests\UserNotificationReadRequest;
use Aparlay\Core\Api\V1\Resources\UserNotificationCollection;
use Aparlay\Core\Api\V1\Resources\UserNotificationResource;
use Aparlay\Core\Api\V1\Services\UserNotificationService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserNotificationController extends Controller
{
    /**
     * @var UserNotificationService
     */
    private $userNotificationService;

    public function __construct(UserNotificationService $userNotificationService)
    {
        $this->userNotificationService = $userNotificationService;
    }

    /**
     * @param  Request  $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        if (auth()->check()) {
            $this->userNotificationService->setUser(auth()->user());
        }

        $userNotifications = $this->userNotificationService->index($request->input('category'));

        return $this->response(new UserNotificationCollection($userNotifications), '', Response::HTTP_OK);
    }

    /**
     * @param  UserNotificationReadRequest  $request
     * @return Response
     * @throws AuthorizationException
     */
    public function read(UserNotificationReadRequest $request): Response
    {
        if (auth()->check()) {
            $this->userNotificationService->setUser(auth()->user());
        }

        $this->authorize('read', [UserNotification::class]);
        $this->userNotificationService->readAll(auth()->user()->_id, $request->user_notification_ids);

        return $this->response([], '', Response::HTTP_ACCEPTED);
    }
}
