<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Requests\UserNotificationRequest;
use Aparlay\Core\Api\V1\Resources\UserNotificationCollection;
use Aparlay\Core\Api\V1\Resources\UserNotificationResource;
use Aparlay\Core\Api\V1\Services\UserNotificationService;
use Aparlay\Core\Models\UserNotification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response;

class UserNotificationController extends Controller
{
    /**
     * @var UserNotificationService
     */
    private $userNotificationService;

    public function __construct(UserNotificationService $documentService)
    {
        $this->userNotificationService = $documentService;
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
        if (auth()->check()) {
            $this->userNotificationService->setUser(auth()->user());
        }
        $userNotifications = $this->userNotificationService->index();
        $collection = new UserNotificationCollection($userNotifications);

        return $this->response($collection, '', Response::HTTP_OK);
    }

    /**
     * @param  UserNotification  $userNotification
     * @return Response
     * @throws AuthorizationException
     */
    public function read(UserNotification $userNotification): Response
    {
        if (auth()->check()) {
            $this->userNotificationService->setUser(auth()->user());
        }

        $this->authorize('read', [UserNotification::class, $userNotification]);
        $userNotification = $this->userNotificationService->read($userNotification);

        return $this->response(new UserNotificationResource($userNotification), '', Response::HTTP_ACCEPTED);
    }
}
