<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Models\Login;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Repositories\UserRepository;
use Aparlay\Core\Helpers\Cdn;
use Aparlay\Core\Jobs\DeleteAvatar;
use Aparlay\Core\Jobs\UploadAvatar;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class UserService
{
    protected UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository(new User());
    }

    /**
     * @return array
     */
    private static function onlineUserWindows(): array
    {
        $currentMinute = date('i');
        $currentMinuteWindow = $currentMinute - ($currentMinute % 5);
        $currentWindow = date('H').$currentMinuteWindow;

        $nextMinuteWindow = $currentMinuteWindow + 5;
        $nextHourWindow = date('H');
        if ($nextMinuteWindow == 60) {
            $nextMinuteWindow = '00';
            $nextHourWindow = date('H', strtotime('+1 hour'));
        }
        $nextWindow = $nextHourWindow.$nextMinuteWindow;

        return [$currentWindow, $nextWindow];
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
    public function uploadAvatar(Request $request, User|Authenticatable $user)
    {
        if (! $request->hasFile('avatar') && ! $request->file('avatar')->isValid()) {
            return false;
        }

        $extension = $request->avatar->getClientOriginalExtension();
        $avatar = uniqid((string) $user->_id, false).'.'.$extension;
        if (($fileName = $request->avatar->storePubliclyAs('avatars', $avatar, 'public')) !== false) {
            /* Store avatar name in database */
            $oldFileName = $user->avatar;
            $this->userRepository->update(['avatar' => Storage::disk('public')->url('avatars/'.$avatar)], $user->_id);

            if (! config('app.is_testing')) {
                UploadAvatar::dispatch((string) $user->_id, 'avatars/'.$avatar)->delay(10);
            }

            if (! str_contains($oldFileName, 'default_')) {
                DeleteAvatar::dispatch(basename($oldFileName))->delay(100);
            }
        }

        return false;
    }

    /**
     * Responsible to check the user is Verified.
     *
     * @param  User|Authenticatable  $user
     * @return bool
     * @throws \Illuminate\Validation\ValidationException
     */
    public function isVerified(User|Authenticatable $user): bool
    {
        $this->userRepository = new UserRepository($user);

        return $this->userRepository->isVerified();
    }

    /**
     * Responsible for change old password.
     *
     * @param  string  $password
     * @return bool
     */
    public function resetPassword(string $password): bool
    {
        return $this->userRepository->resetPassword($password);
    }

    /**
     * Responsible to check if OTP is required to sent to the user, based on user_status and otp settings.
     *
     * @param  User|Authenticatable  $user
     * @return bool
     */
    public function isUnverified(User|Authenticatable $user): bool
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
     * @param  User|Authenticatable  $user
     * @return bool
     */
    public function isUserEligible(User|Authenticatable $user): bool
    {
        $this->userRepository = new UserRepository($user);

        return $this->userRepository->isUserEligible();
    }

    /**
     * Responsible for delete user account.
     *
     * @param  User|Authenticatable  $user
     * @return bool
     */
    public function deleteAccount(User|Authenticatable $user)
    {
        $this->userRepository = new UserRepository($user);

        return $this->userRepository->deleteAccount();
    }

    /**
     * Responsible for set user avatar.
     *
     * @return string
     * @throws Exception
     */
    public function changeDefaultAvatar()
    {
        /* Set gender by default value */
        $gender = auth()->user()->gender ?? User::GENDER_MALE;

        /* Set avatar based on Gender */
        $femaleFilename = 'default_fm_'.random_int(1, 60).'.png';
        $maleFilename = 'default_m_'.random_int(1, 120).'.png';

        $filename = match ($gender) {
            User::GENDER_FEMALE => $femaleFilename,
            User::GENDER_MALE => $maleFilename,
            default => (random_int(0, 1) ? $maleFilename : $femaleFilename),
        };

        $avatar = Cdn::avatar($filename);

        return $avatar;
    }

    /**
     * Check required OTP during login.
     * @return bool
     */
    public function requireOtp(): bool
    {
        return $this->userRepository->requireOtp();
    }

    /**
     * online user implementation with redis sets
     * three category will be created
     * - all for the content creators who choose show online status to all
     * - follower for the content creators who choose show online status for followers only
     * - none for admin panel online users.
     *
     * this patter will create two sets for each 5 minutes and 10 minutes 10:00-10:05 and 10:05-10:10
     * each authenticated request check and add user to both if user
     * doesn't send any request for more than 5 minutes her account won't add to the next windows
     * and considered as offline after at most 5 minutes of inactivity
     *
     * @param  User|Authenticatable  $user
     * @return void
     */
    public static function online(User|Authenticatable $user)
    {
        Redis::pipeline(function ($pipe) use ($user) {
            [$currentWindow, $nextWindow] = self::onlineUserWindows();

            $onlineAllCurrent = config('app.cache.keys.online.all').':'.$currentWindow;
            $onlineFollowingsCurrent = config('app.cache.keys.online.followings').':'.$currentWindow;
            $onlineNoneCurrent = config('app.cache.keys.online.none').':'.$currentWindow;
            $onlineAllNext = config('app.cache.keys.online.all').':'.$nextWindow;
            $onlineFollowingsNext = config('app.cache.keys.online.followings').':'.$nextWindow;
            $onlineNoneNext = config('app.cache.keys.online.none').':'.$nextWindow;

            $now = time();
            $currentWindowExpireAt = ceil($now / 300) * 300;
            $nextWindowExpireAt = ceil($now / 600) * 600;
            if ($pipe->sCard($onlineNoneCurrent) == 0) {
                $pipe->expireAt($onlineAllCurrent, $currentWindowExpireAt);
                $pipe->expireAt($onlineFollowingsCurrent, $currentWindowExpireAt);
                $pipe->expireAt($onlineNoneCurrent, $currentWindowExpireAt);
            }

            if ($pipe->sCard($onlineNoneNext) == 0) {
                $pipe->expireAt($onlineAllNext, $nextWindowExpireAt);
                $pipe->expireAt($onlineFollowingsNext, $nextWindowExpireAt);
                $pipe->expireAt($onlineNoneNext, $nextWindowExpireAt);
            }

            switch ($user->show_online_status) {
                case \Aparlay\Core\Models\User::SHOW_ONLINE_STATUS_ALL:
                    $pipe->sAdd($onlineAllCurrent, (string) $user->_id);
                    $pipe->sAdd($onlineAllNext, (string) $user->_id);
                // no break
                case \Aparlay\Core\Models\User::SHOW_ONLINE_STATUS_FOLLOWERS:
                    $pipe->sAdd($onlineFollowingsCurrent, (string) $user->_id);
                    $pipe->sAdd($onlineFollowingsNext, (string) $user->_id);
                // no break
                case \Aparlay\Core\Models\User::SHOW_ONLINE_STATUS_NONE:
                    $pipe->sAdd($onlineNoneCurrent, (string) $user->_id);
                    $pipe->sAdd($onlineNoneNext, (string) $user->_id);
            }
        });
    }

    public static function offline(User|Authenticatable $user)
    {
        Redis::pipeline(function ($pipe) use ($user) {
            [$currentWindow, $nextWindow] = self::onlineUserWindows();

            $onlineAllCurrent = config('app.cache.keys.online.all').':'.$currentWindow;
            $onlineFollowingsCurrent = config('app.cache.keys.online.followings').':'.$currentWindow;
            $onlineNoneCurrent = config('app.cache.keys.online.none').':'.$currentWindow;
            $onlineAllNext = config('app.cache.keys.online.all').':'.$nextWindow;
            $onlineFollowingsNext = config('app.cache.keys.online.followings').':'.$nextWindow;
            $onlineNoneNext = config('app.cache.keys.online.none').':'.$nextWindow;

            switch ($user->show_online_status) {
                case \Aparlay\Core\Models\User::SHOW_ONLINE_STATUS_ALL:
                    $pipe->sRem($onlineAllCurrent, (string) $user->_id);
                    $pipe->sRem($onlineAllNext, (string) $user->_id);
                // no break
                case \Aparlay\Core\Models\User::SHOW_ONLINE_STATUS_FOLLOWERS:
                    $pipe->sRem($onlineFollowingsCurrent, (string) $user->_id);
                    $pipe->sRem($onlineFollowingsNext, (string) $user->_id);
                // no break
                case \Aparlay\Core\Models\User::SHOW_ONLINE_STATUS_NONE:
                    $pipe->sRem($onlineNoneCurrent, (string) $user->_id);
                    $pipe->sRem($onlineNoneNext, (string) $user->_id);
            }
        });
    }
}
