<?php

namespace Aparlay\Core\Api\V1\Policies;

use Aparlay\Core\Api\V1\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;

class UserNotificationPolicy
{
    use HandlesAuthorization;

    public function read(User|Authenticatable $user)
    {
        return Response::allow();
    }
}
