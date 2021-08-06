<?php

namespace Aparlay\Core\Services;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Validation\ValidationException;

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
                return 'email';
            case is_numeric($identity):
                return 'phone_number';
            default:
                return 'username';
        }
    }

    /**
     * Through exception if user is suspended/banned/not found.
     *
     * @param User|Authenticatable $user
     *
     * @return bool
     *
     * @throws ValidationException
     */
    public static function isUserEligible(User | Authenticatable $user)
    {
        switch ($user->status) {
            case User::STATUS_SUSPENDED:
                throw ValidationException::withMessages(['Account' => ['This account has been suspended.']]);
            case User::STATUS_BLOCKED:
                throw ValidationException::withMessages(['Account' => ['This account has been banned.']]);
            case User::STATUS_DEACTIVATED:
                throw ValidationException::withMessages(['Account' => ['Your user account not found or does not match with password.']]);
            default:
                return true;

                break;
        }
    }
}
