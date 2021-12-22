<?php

namespace Aparlay\Core\Api\V1\Traits;

use Aparlay\Core\Api\V1\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

trait HasUserTrait
{
    /**
     * @var Authenticatable|User
     */
    public Authenticatable|User $user;

    public function getUser(): Authenticatable|User
    {
        return $this->user;
    }

    /**
     * @param Authenticatable|User $user
     */
    public function setUser(Authenticatable|User $user): void
    {
        $this->user = $user;
    }
}
