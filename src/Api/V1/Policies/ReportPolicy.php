<?php

namespace Aparlay\Core\Api\V1\Policies;

use Aparlay\Core\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;

class ReportPolicy
{
    use HandlesAuthorization;

    /**
     * Responsible for check the user can delete the model.
     */
    public function user(User|Authenticatable $user, User $reportUser): Response
    {
        if (!Gate::forUser($user)->denies('interact', $reportUser->_id)) {
            return Response::allow();
        }

        return Response::deny(__('You cannot report this user at the moment.'));
    }

    /**
     * Responsible for check the user can delete the model.
     */
    public function comment(User|Authenticatable $user, User $mediaCreator): Response
    {
        if (!Gate::forUser($user)->denies('interact', $mediaCreator->_id)) {
            return Response::allow();
        }

        return Response::deny(__('You cannot report this video at the moment.'));
    }

    /**
     * Responsible for check the user can delete the model.
     */
    public function media(User|Authenticatable $user, User $mediaCreator): Response
    {
        if (!Gate::forUser($user)->denies('interact', $mediaCreator->_id)) {
            return Response::allow();
        }

        return Response::deny(__('You cannot report this video at the moment.'));
    }
}
