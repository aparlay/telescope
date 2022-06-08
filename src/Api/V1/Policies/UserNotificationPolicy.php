<?php

namespace Aparlay\Core\Api\V1\Policies;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Models\UserNotification;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;

class UserNotificationPolicy
{
    use HandlesAuthorization;

    public function view(User | Authenticatable $user, UserNotification $userNotification)
    {
        $userId = $user?->_id;

        if ((string) $userId === (string) $userNotification->user_id) {
            return Response::allow();
        }

        return Response::deny(__('You cannot read this notification'));
    }
}
