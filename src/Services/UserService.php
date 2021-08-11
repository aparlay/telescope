<?php

namespace Aparlay\Core\Services;

use Aparlay\Core\Models\Login;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
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
                return Login::IDENTITY_EMAIL;
            case is_numeric($identity):
                return Login::IDENTITY_PHONE_NUMBER;
            default:
                return Login::IDENTITY_USERNAME;
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
                throw ValidationException::withMessages([
                    'Account' => ['This account has been suspended.'],
                ]);
            case User::STATUS_BLOCKED:
                throw ValidationException::withMessages([
                    'Account' => ['This account has been banned.'],
                ]);
            case User::STATUS_DEACTIVATED:
                throw ValidationException::withMessages([
                    'Account' => ['Your user account not found or does not match with password.'],
                ]);
            default:
                return true;

                break;
        }
    }

    /**
     * Responsible to check if OTP is required to sent to the user, based on user_status and otp settings.
     * @param User $user
     * @return bool
     */
    public static function isUnverified(User $user)
    {
        /* User is considered as unverified when "OTP Setting is enabled AND user status is pending" */
        return $user->setting['otp'] && $user->status === User::STATUS_PENDING;
    }

    /**
     * Responsible to check if OTP is required to sent to the user, based on user_status and otp settings.
     * @param Request $request
     * @param User $user
     * @return bool
     */
    public static function uploadAvatar(Request $request, User $user)
    {
        /** Upload Avatar Image on Server */
        $extension = $request->file('avatar')->getClientOriginalExtension();
        $avatar = uniqid($user->_id, false) . '.' . $extension;
        $uploadDirectory = config('app.avatar.upload_directory');
        $request->file('avatar')->storeAs($uploadDirectory, $avatar);
       
        /** Update Avatar Image on Cloude */
        // Pending: https://trello.com/c/2wS0tk7I/27-setup-cloud-backblaze-bucket-and-google-clould
        
        /** Store avatar name in database */
        $user->avatar = str_replace('//', '/', $uploadDirectory . '/' . $avatar);
        $user->save();
        return $user;
    }
}
