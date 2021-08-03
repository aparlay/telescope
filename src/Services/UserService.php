<?php

namespace Aparlay\Core\Services;

use App\Models\User;
use Aparlay\Core\Api\V1\Requests\LoginRequest;
use Aparlay\Core\Repositories\UserRepository;
use Aparlay\Core\Api\V1\Controllers;
use Illuminate\Validation\ValidationException;
use Validator;

class UserService
{
    /**
     * Responsible for returning Login Entity (email or phone_number or username) based on the input username
     * @param string $identity
     * @return String
     */
    public static function findIdentity(string $identity)
    {
        /** Find identity */
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
     * Through exception if user is suspended/banned/not found
     * @param User $user
     * @return ValidationException|Boolean
     */
    public static function isUserEligible(User $user)
    {
        switch ($user->status) {
            case User::STATUS_SUSPENDED:
                throw ValidationException::withMessages(['Account' => ['This account has been suspended.']]);
                break;
            case User::STATUS_BLOCKED:
                throw ValidationException::withMessages(['Account' => ['This account has been banned.']]);
                break;
            case User::STATUS_DEACTIVATED:
                throw ValidationException::withMessages(['Account' => ['Your user account not found or does 
                not match with password.']]);
                break;
            default:
                return true;
                break;
        }
    }
}
