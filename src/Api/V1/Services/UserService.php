<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Dto\UserDeleteDTO;
use Aparlay\Core\Api\V1\Models\Login;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Repositories\UserRepository;
use Aparlay\Core\Api\V1\Traits\HasUserTrait;
use Aparlay\Core\Helpers\Cdn;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Jobs\DeleteAvatar;
use Aparlay\Core\Jobs\UploadAvatar;
use Aparlay\Core\Models\Enums\UserGender;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Jenssegers\Agent\Agent;

class UserService
{
    use HasUserTrait;
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
    public function uploadAvatar(Request $request)
    {
        $user = $this->getUser();
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
     * @throws ValidationException
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
     * @param  UserDeleteDTO  $userDeleteDTO
     * @return bool
     * @throws Exception
     */
    public function deleteAccount(UserDeleteDTO $userDeleteDTO): bool
    {
        $this->userRepository = new UserRepository($this->getUser());

        return $this->userRepository->deleteAccount($userDeleteDTO->reason);
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
        $gender = $this->getUser()->gender ?? UserGender::MALE->value;

        /* Set avatar based on Gender */
        $femaleFilename = 'default_fm_'.random_int(1, 60).'.png';
        $maleFilename = 'default_m_'.random_int(1, 120).'.png';

        $filename = match ($gender) {
            UserGender::FEMALE->value => $femaleFilename,
            UserGender::MALE->value => $maleFilename,
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
     * Verifying the user.
     *
     * @param  User|Authenticatable|null  $user
     * @param  $userAgent
     * @param  $deviceId
     * @param  $ip
     * @return void
     */
    public function logUserDevice(User|Authenticatable|null $user, $userAgent, $deviceId, $ip): void
    {
        if ($user !== null) {
            $currentUserAgentKey = md5($userAgent);
            $userAgents = $user->user_agents;
            $new = true;

            foreach ($userAgents as $key => $item) {
                if ($item['key'] === $currentUserAgentKey) {
                    $needUpdate = $item['device_id'] !== $deviceId;
                    $needUpdate = $needUpdate || $item['ip'] !== $ip;
                    $needUpdate = $needUpdate || now()->subMinutes(5)->isAfter(Carbon::createFromTimestampMsUTC($item['created_at']));

                    if ($needUpdate) {
                        $userAgents[$key] = [
                            'key' => $currentUserAgentKey,
                            'device_id' => $deviceId,
                            'user_agent' => $userAgent,
                            'ip' => $ip,
                            'created_at' => DT::utcNow(),
                        ];
                    }

                    $new = false;
                    break;
                }
            }
            if ($new) {
                $userAgents[] = [
                    'key' => $currentUserAgentKey,
                    'device_id' => $deviceId,
                    'user_agent' => $userAgent,
                    'ip' => $ip,
                    'created_at' => DT::utcNow(),
                ];
            }

            $this->userRepository->update(['user_agents' => $userAgents], $user->_id);
        }
    }
}
