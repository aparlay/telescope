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

    public static function findByEmail($email)
    {
        $user = User::Where('email', $email)->first();
        if($user)
        {
            return $user->toArray();
        }
        return false;
    }

}