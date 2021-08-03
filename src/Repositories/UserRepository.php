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

    /**
     * @param string $email
     * @return array|bool
     */
    public static function findByEmail(string $email)
    {
        $user = User::Where('email', $email)->first();
        if ($user) {
            return $user;
        }
        return false;
    }

    /**
     * @param string $phoneNumber
     * @return array|bool
     */
    public static function findByPhoneNumber(string $phoneNumber)
    {
        $user = User::Where('phone_number', $phoneNumber)->first();
        if ($user) {
            return $user;
        }
        return false;
    }

    /**
     * @param string $username
     * @return array|bool
     */
    public static function findByUsername(string $uername)
    {
        $user = User::Where('username', $uername)->first();
        if ($user) {
            return $user;
        }
        return false;
    }
}
