<?php

namespace Aparlay\Core\Services;

use Aparlay\Core\Models\Login;
use App\Models\User;
use Aparlay\Core\Api\V1\Requests\LoginRequest;
use Aparlay\Core\Repositories\UserRepository;
use Aparlay\Core\Services\OtpService;
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
     * @param LoginRequest $request
     * @return Boolean
     */
    public static function isOTPRequired(User $user, LoginRequest $request)
    {
        /** OTP is required in case "OTP Setting is enabled AND user status is pending AND otp not found in request" */
        return ($user->getUserSetting()->otp && $user->status === User::STATUS_PENDING && !$request->otp);
    }

    /**
     * Responsible for change status, based on varified otp
     * @param User $user
     * @param LoginRequest $request
     * @return Boolean
     */
    public static function verified(User $user, LoginRequest $request)
    {
        if ($user->status === User::STATUS_PENDING) {
            $user = UserService::findByProvidedIdentity($request->username);
            $user->status = User::STATUS_VERIFIED;
            $user->email_verified = true;
            $user->save(['status', 'email_verified']);
        }
    }

    /**
     * Finds user by username
     *
     * @param string $usernameField
     * @return static|null
     */
    public static function findByProvidedIdentity(string $usernameField)
    {
        switch ($usernameField) {
            case filter_var($usernameField, FILTER_VALIDATE_EMAIL):
                return UserRepository::findByEmail($usernameField);
            case is_numeric($usernameField):
                return UserRepository::findByPhoneNumber($usernameField);
            default:
                return UserRepository::findByUsername($usernameField);
        }
    }
}
