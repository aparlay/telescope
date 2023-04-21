<?php

namespace Aparlay\Core\Api\V1\Policies;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Models\UserDocument;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;

class UserDocumentPolicy
{
    use HandlesAuthorization;

    public function view(User|Authenticatable $user, UserDocument $userDocument)
    {
        $userId = $user?->_id;

        return $userDocument->creatorObj->equalTo($userId)
            ? Response::allow()
            : Response::deny(__('You cannot view this document'));
    }
}
