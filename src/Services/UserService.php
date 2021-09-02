<?php

namespace Aparlay\Core\Services;

use Aparlay\Core\Jobs\DeleteAvatar;
use Aparlay\Core\Jobs\UploadAvatar;
use Aparlay\Core\Models\Login;
use Aparlay\Core\Repositories\UserRepository;
use App\Models\User;
use console\jobs\DeleteAvatarJob;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserService
{
    /**
     * Responsible for returning Login Entity (email or phone_number or username) based on the input username.
     *
     * @param  string  $identity
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
     * Find user by identity (email/phone_number/username).
     *
     * @param  string  $username
     * @return User|null
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

    /**
     * Responsible to check if OTP is required to sent to the user, based on user_status and otp settings.
     * @param  Request  $request
     * @param  User|Authenticatable  $user
     * @return bool
     * @throws Exception
     */
    public static function uploadAvatar(Request $request, User | Authenticatable $user)
    {
        if ($request->hasFile('avatar') === false || $request->file('avatar') === null) {
            return false;
        }

        $extension = $request->file('avatar')->getClientOriginalExtension();
        $avatar = uniqid((string)$user->_id, false).'.'.$extension;
        if (($filePath = $request->file('avatar')->storeAs('avatars', $avatar, 'public')) !== false) {
            dispatch((new UploadAvatar((string)$user->_id, $filePath))->onQueue('low'));

            if (!str_contains($user->avatar, 'default_')) {
                /* delete old avatar file */
                dispatch((new DeleteAvatar((string)$user->_id, basename($user->avatar)))->onQueue('low'));
            }

            /* Store avatar name in database */
            $user->avatar = Storage::disk('public')->url('avatars/'.$avatar);
            return $user->save();
        }

        return false;
    }
}
