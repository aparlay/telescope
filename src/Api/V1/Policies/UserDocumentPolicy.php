<?php

namespace Aparlay\Core\Api\V1\Policies;

use Aparlay\Core\Api\V1\Models\Block;
use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Models\UserDocument;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;

class UserDocumentPolicy
{
    use HandlesAuthorization;

    public function view(User | Authenticatable $user, UserDocument $userDocument)
    {
        $userId = $user?->_id;

        if ($userId === $userDocument->userObj->_id) {
            return Response::allow();
        }

        return Response::deny(__('You cannot view this document'));
    }
}
