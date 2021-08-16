<?php

namespace Aparlay\Core\Repositories;

use Aparlay\Core\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserRepository implements RepositoryInterface
{
    protected User $model;

    public function __construct($model)
    {
        if (! ($model instanceof User)) {
            throw new \InvalidArgumentException('$model should be of User type');
        }

        $this->model = $model;
    }

    /**
     * Verifying the user.
     *
     * @param  User|Authenticatable  $user
     *
     * @return bool
     */
    public function verify(User | Authenticatable $user)
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
     *
     * @param  User|Authenticatable  $user
     *
     * @return bool
     */
    public function isUnverified(User | Authenticatable $user)
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

    /**
     * find user by email.
     *
     * @param string $email
     *
<<<<<<< HEAD
     * @return Array | bool
=======
     * @return array
>>>>>>> 4200184319142daaf3d583bcc80c817bec0411fb
     */
    public static function findByEmail(string $email)
    {
        $user = User::Email($email)->first();
        if ($user) {
            return $user;
        }

        return false;
    }

    /**
     * find user by phone_number.
     *
     * @param string $phoneNumber
     *
     * @return Array | bool
     */
    public static function findByPhoneNumber(string $phoneNumber)
    {
        $user = User::PhoneNumber($phoneNumber)->first();
        if ($user) {
            return $user;
        }

        return false;
    }

    /**
     * Resposible for match old password.
     *
     * @param string $password
     * @param User|Authenticatable  $user
     * @return bool
     */
    public function resetPassword(string $password, User | Authenticatable $user)
    {
        $user->password_hash = Hash::make($password);
        $user->save();
    }
}
