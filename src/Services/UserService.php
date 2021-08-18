<?php

namespace Aparlay\Core\Services;

use Aparlay\Core\Models\Login;
use Aparlay\Core\Models\User;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserService
{
    /**
     * Responsible for returning Login Entity (email or phone_number or username) based on the input username.
     *
     * @return String
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
     * Responsible to check if OTP is required to sent to the user, based on user_status and otp settings.
     * @param Request $request
     * @param User|Authenticatable $user
     * @return User|Bool
     */
    public static function uploadAvatar(Request $request, User | Authenticatable $user)
    {
        /** Upload Avatar Image on Server */
        if ($request->hasFile('avatar')) {
            $extension = $request->file('avatar')->getClientOriginalExtension();
            $avatar = uniqid($user->_id, false) . '.' . $extension;
            if (! $request->file('avatar')->storeAs(config('app.avatar.upload_directory'), $avatar)) {
                throw new Exception('Cannot upload the file.');
            }

            /* Store temporary avatar name in database */
            $user->avatar = $avatar;
            $user->save();
            $user->refresh();
        }

        return $user;
    }
}
