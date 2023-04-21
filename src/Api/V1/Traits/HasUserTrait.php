<?php

namespace Aparlay\Core\Api\V1\Traits;

use Aparlay\Core\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

trait HasUserTrait
{
    private User|Authenticatable $user;

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User|Authenticatable $user): void
    {
        $this->user = $user;
    }
}
