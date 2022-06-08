<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\UserNotification;
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
        $collection = new UserNotificationCollection($userNotifications);

        return $this->response($collection, '', Response::HTTP_OK);
    }

    /**
     * @param  UserNotification  $userNotification
     * @return Response
     * @throws AuthorizationException
     */
    public function view(UserNotification $userNotification): Response
    {
        if (auth()->check()) {
            $this->userNotificationService->setUser(auth()->user());
        }

        $this->authorize('view', [UserNotification::class, $userNotification]);
        $userNotification = $this->userNotificationService->read($userNotification);

        return $this->response(new UserNotificationResource($userNotification), '', Response::HTTP_ACCEPTED);
    }
}
