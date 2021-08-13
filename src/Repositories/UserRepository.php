<?php

namespace Aparlay\Core\Repositories;

use Aparlay\Core\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Auth\Authenticatable;

class UserRepository implements RepositoryInterface
{
    protected User $model;

    public function __construct(User $model)
    {
        if (!($model instanceof User)) {
            throw new \InvalidArgumentException('$model should be of User type');
        }

        $this->model = $model;
    }

    public function verify(User $user)
    {
        $user->status = User::STATUS_VERIFIED;
        $user->email_verified = true;
        $user->save(['status', 'email_verified']);
    }

    /**
     * Through exception if user is suspended/banned/not found.
     *
     * @param  User|Authenticatable  $user
     *
     * @return bool
     *
     * @throws ValidationException
     */
    public function isUserEligible(User|Authenticatable $user)
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
     * @param  User  $user
     * @return bool
     */
    public function isUnverified(User $user)
    {
        /* User is considered as unverified when "OTP Setting is enabled AND user status is pending" */
        return $user->setting['otp'] && $user->status === User::STATUS_PENDING;
    }

    public function all()
    {
        // TODO: Implement all() method.
    }

    public function create(array $data)
    {
        // TODO: Implement create() method.
    }

    public function update(array $data, $id)
    {
        // TODO: Implement update() method.
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    public function find($id)
    {
        // TODO: Implement find() method.
    }
}
