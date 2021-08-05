<?php

namespace Aparlay\Core\Services;

use Aparlay\Core\Models\Login;
use App\Models\User;
use Aparlay\Core\Api\V1\Requests\LoginRequest;
use Aparlay\Core\Repositories\UserRepository;
use Aparlay\Core\Services\OtpService;
use Aparlay\Core\Api\V1\Controllers;
use Illuminate\Validation\ValidationException;

class UserService
{
    /**
     * Responsible for returning Login Entity (email or phone_number or username) based on the input username
     * @param string $identity
     * @return String
     */
    public static function getIdentityType(string $identity)
    {
        /** Find identity */
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

    /**
     * Responsible to check if OTP is required to sent to the user, based on user_status and otp settings
     * @param User $user
     * @return Boolean
     */
    public static function isUnverified(User $user)
    {
        /** User is considered as unverified when "OTP Setting is enabled AND user status is pending" */
        return ($user->getUserSetting()->otp && $user->status === User::STATUS_PENDING);
    }
}
