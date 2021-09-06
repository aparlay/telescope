<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Models\Login;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Repositories\UserRepository;
use Aparlay\Core\Jobs\DeleteAvatar;
use Aparlay\Core\Jobs\UploadAvatar;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserService
{
    protected UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository(new User());
    }

    /**
     * Responsible for returning Login Entity (email or phone_number or username) based on the input username.
     *
     * @param  string  $identity
     * @return string
     */
    public function getIdentityType(string $identity)
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
    public function findByIdentity(string $username)
    {
        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            return $this->userRepository->findByEmail($username);
        }

        if (is_numeric($username)) {
            return $this->userRepository->findByPhoneNumber($username);
        }

        return $this->userRepository->findByUsername($username);
    }

    /**
     * Responsible to check if OTP is required to sent to the user, based on user_status and otp settings.
     * @param  Request  $request
     * @param  User|Authenticatable  $user
     * @return bool
     * @throws Exception
     */
    public function uploadAvatar(Request $request, User | Authenticatable $user)
    {
        if (! $request->hasFile('avatar') && ! $request->file('avatar')->isValid()) {
            return false;
        }

        $extension = $request->avatar->getClientOriginalExtension();
        $avatar = uniqid((string) $user->_id, false).'.'.$extension;
        if (($fileName = $request->avatar->storeAs('avatars', $avatar, 'public')) !== false) {
            /* Store avatar name in database */
            $oldFileName = $user->avatar;
            $user->avatar = Storage::disk('public')->url('avatars/'.$avatar);
            $user->save();
            dispatch((new UploadAvatar((string) $user->_id, $fileName))->delay(10)->onQueue('high'));
            if (! str_contains($oldFileName, 'default_')) {
                $deleteOldFiles = new DeleteAvatar((string) $user->_id, basename($oldFileName));
                dispatch($deleteOldFiles->delay(100)->onQueue('low'));
            }
        }

        return false;
    }

    /**
     * Responsible to check the user is Verified.
     *
     * @param User|Authenticatable $user
     * @return bool
     */
    public function isVerified(User | Authenticatable $user): bool
    {
        $this->userRepository = new UserRepository($user);
        return $this->userRepository->isVerified();
    }

    /**
     * Responsible for change old password.
     *
     * @param string $password
     * @return bool
     */
    public function resetPassword(string $password): bool
    {
        return $this->userRepository->resetPassword($password);
    }

    /**
     * Responsible to check if OTP is required to sent to the user, based on user_status and otp settings.
     *
     * @param User|Authenticatable $user
     * @return bool
     */
    public function isUnverified(User | Authenticatable $user): bool
    {
        $this->userRepository = new UserRepository($user);
        return $this->userRepository->isUnverified();
    }

    /**
     * Verifying the user.
     *
     * @return bool
     */
    public function verify(): bool
    {
        return $this->userRepository->verify();
    }

    /**
     * Through exception if user is suspended/banned/not found.
     *
     * @param User|Authenticatable $user
     * @return bool
     */
    public function isUserEligible(User | Authenticatable $user): bool
    {
        $this->userRepository = new UserRepository($user);
        return $this->userRepository->isUserEligible();
    }

    /**
     * Responsible for delete user account.
     *
     * @param User|Authenticatable $user
     * @return bool
     */
    public function deleteAccount(User | Authenticatable $user)
    {
        $this->userRepository = new UserRepository($user);
        return $this->userRepository->deleteAccount();
    }
}
