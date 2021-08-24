<?php

namespace Aparlay\Core\Api\V1\Policies;

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
     * @param  User  $user
     * @return Response|bool
     */
    public function view(User $user)
    {
        return (
            ($user !== null && (string) auth()->user()->_id === (string) $user->_id) ||
            auth()->user()->type === User::TYPE_ADMIN
        )
        ? Response::allow()
        : Response::deny(__('You can only update your account.'));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return Response|bool
     */
    public function create()
    {
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User|null  $user
     * @return Response|bool
     */
    public function update(User | null $user)
    {
        return ($user !== null && (string) auth()->user()->_id === (string) $user->_id)
        ? Response::allow()
        : Response::deny(__('You can only update your account.'));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @return Response|bool
     */
    public function delete(User $user)
    {
    }
}
