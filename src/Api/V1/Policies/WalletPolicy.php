<?php

namespace Aparlay\Core\Api\V1\Policies;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Payout\Models\Wallet;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;

class WalletPolicy
{
    use HandlesAuthorization;

    public function delete(User | Authenticatable $user, Wallet $wallet)
    {
        $userId = $user?->_id;

        if ($userId === $wallet->creatorObj->_id) {
            return Response::allow();
        }

        return Response::deny(__('You cannot delete this wallet'));
    }


    public function view(User | Authenticatable $user, Wallet $wallet)
    {
        $userId = $user?->_id;

        if ($userId === $wallet->creatorObj->_id) {
            return Response::allow();
        }

        return Response::deny(__('You cannot view this wallet'));
    }


}
