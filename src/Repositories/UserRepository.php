<?php

namespace Aparlay\Core\Repositories;

use Aparlay\Core\Models\User;

class UserRepository
{   
    protected $user = null;

    public function getAllUsers()
    {
        return User::all();
    }

}