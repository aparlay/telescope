<?php

namespace Aparlay\Core\Repositories;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Validation\ValidationException;

class UserRepository
{
    public function verify(User $user)
    {
        $user->status = User::STATUS_VERIFIED;
        $user->email_verified = true;
        $user->save(['status', 'email_verified']);
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
    public function isUserEligible(User | Authenticatable $user)
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
    public function isUnverified(User $user)
    {
        /* User is considered as unverified when "OTP Setting is enabled AND user status is pending" */
        return $user->setting['otp'] && $user->status === User::STATUS_PENDING;
    }
}
