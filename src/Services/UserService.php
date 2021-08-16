<?php

namespace Aparlay\Core\Services;

use Aparlay\Core\Models\Login;
use Aparlay\Core\Repositories\UserRepository;

class UserService
{
    /**
     * Responsible for returning Login Entity (email or phone_number or username) based on the input username.
     *
     * @return string
     */
    public static function getIdentityType(string $identity)
    {
        /* Find identity */
        switch ($identity) {
            case filter_var($identity, FILTER_VALIDATE_EMAIL):
                return Login::IDENTITY_EMAIL;
            case is_numeric($identity):
                return Login::IDENTITY_PHONE_NUMBER;
            default:
                return Login::IDENTITY_USERNAME;
        }
    }

    /**
     * Finds user by username
     *
     * @param string $username
     *
     * @return static|null
     */
    public static function findByIdentity(string $username)
    {
        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            return UserRepository::findByEmail($username);
        }

        if (is_numeric($username)) {
            return UserRepository::findByPhoneNumber($username);
        }

        return UserRepository::findByUsername($username);
    }
}
