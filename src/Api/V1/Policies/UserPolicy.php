<?php

namespace Aparlay\Core\Api\V1\Policies;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Repositories\UserRepository;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use HandlesAuthorization;

    public $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \Aparlay\Core\Api\V1\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user)
    {
        if (in_array(auth()->user()->status, User::getStatuses())) {
            return Response::deny(__('Account not found!'));
        }
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \Aparlay\Core\Api\V1\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create()
    {
        $userId = $user->_id;

        return ($userId === null || (string) auth()->user()->_id !== (string) $userId)
        ? Response::allow()
        : Response::deny(__('You can only update your account.'));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \Aparlay\Core\Api\V1\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user)
    {
        $userId = $user->_id;

        return ($userId === null || (string) auth()->user()->_id !== (string) $userId)
        ? Response::allow()
        : Response::deny(__('You can only update your account.'));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \Aparlay\Core\Api\V1\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user)
    {
        $userId = $user->_id;

        if ($user->is_protected) {
            return Response::deny(__('Account is protected and you cannot delete it.'));
        }

        return ($userId === null || (string) auth()->user()->_id !== (string) $userId)
        ? Response::allow()
        : Response::deny(__('You can only delete your account.'));
    }
}
