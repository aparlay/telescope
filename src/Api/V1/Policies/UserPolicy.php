<?php

namespace Aparlay\Core\Api\V1\Policies;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use HandlesAuthorization;

    public function __construct(User $user)
    {
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return Response|bool
     */
    public function view(User $user)
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     *
     * @return Response|bool
     */
    public function create()
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return Response|bool
     */
    public function update(User|null $user)
    {
        if ((string) auth()->user()->_id !== (string) $user->_id) {
            return Response::deny(__('You can only update your account.'));
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return Response|bool
     */
    public function delete(User $user)
    {
        if ((string) auth()->user()->_id !== (string) $user->_id) {
            return Response::deny(__('You can only delete your account.'));
        }

        if (Media::user($user->_id)->protected()->first() !== null) {
            return Response::deny(__('You are not allowed to delete this. Please contact support for more information.'));
        }

        return Response::allow();
    }
}
