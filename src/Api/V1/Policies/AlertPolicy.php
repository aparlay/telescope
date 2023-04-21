<?php

namespace Aparlay\Core\Api\V1\Policies;

use Aparlay\Core\Api\V1\Models\Alert;
use Aparlay\Core\Api\V1\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;

class AlertPolicy
{
    use HandlesAuthorization;

    public function view()
    {
    }

    public function create()
    {
    }

    public function delete()
    {
    }

    /**
     * Responsible for check the user can delete the model.
     *
     * @return Response
     */
    public function update(User|Authenticatable $user, Alert $alert)
    {
        $userId = $user?->_id;

        if ((string) $userId === (string) $alert->user_id) {
            return Response::allow();
        }

        return Response::deny(__('You cannot visit this alert'));
    }
}
