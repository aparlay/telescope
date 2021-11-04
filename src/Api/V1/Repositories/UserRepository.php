<?php

namespace Aparlay\Core\Api\V1\Repositories;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Models\User as BaseUser;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserRepository implements RepositoryInterface
{
    protected User | BaseUser $model;

    public function __construct($model)
    {
        if (! ($model instanceof BaseUser)) {
            throw new \InvalidArgumentException('$model should be of User type');
        }

        $this->model = $model;
    }

    /**
     * Verifying the user.
     *
     * @return bool
     */
    public function verify(): bool
    {
        $this->model->status = User::STATUS_VERIFIED;
        $this->model->email_verified = true;

        return $this->model->save(['status', 'email_verified']);
    }

    /**
     * Through exception if user is suspended/banned/not found.
     *
     * @return bool
     * @throws ValidationException
     */
    public function isUserEligible(): bool
    {
        switch ($this->model->status) {
            case User::STATUS_SUSPENDED:

                abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'The user is suspended.');

                // no break
            case User::STATUS_BLOCKED:

                abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'The user has been banned.');

                // no break
            case User::STATUS_DEACTIVATED:

                abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'User account not found or does not match with password.');

                // no break
            default:
                return true;
        }
    }

    /**
     * Responsible to check if OTP is required to sent to the user, based on user_status and otp settings.
     *
     * @return bool
     */
    public function isUnverified(): bool
    {
        /* User is considered as unverified when "OTP Setting is enabled AND user status is pending" */
        return $this->model->setting['otp'] && $this->model->status === User::STATUS_PENDING;
    }

    /**
     * Responsible to check the user is Verified.
     *
     * @return bool
     * @throws ValidationException
     */
    public function isVerified(): bool
    {
        /* User is considered as verified when user status is active or verified */
        if ($this->model->status !== User::STATUS_VERIFIED && $this->model->status !== User::STATUS_ACTIVE) {
            throw ValidationException::withMessages([
                'Account' => ['Your account is not authenticated.'],
            ]);
        }

        return true;
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
        return $this->model->user($id)->update($data);
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
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return User::email($email)->first();
    }

    /**
     * find user by phone_number.
     *
     * @param string $phoneNumber
     * @return User|null
     */
    public function findByPhoneNumber(string $phoneNumber): ?User
    {
        return User::phoneNumber($phoneNumber)->first();
    }

    /**
     * find user by username.
     *
     * @param string $userName
     * @return User|null
     */
    public function findByUsername(string $userName): ?User
    {
        return User::username($userName)->first();
    }

    /**
     * Responsible for change old password.
     *
     * @param string $password
     * @return bool
     */
    public function resetPassword(string $password): bool
    {
        $this->model->password_hash = Hash::make($password);

        return $this->model->save();
    }

    /**
     * Responsible for delete user account.
     *
     * @return bool
     */
    public function deleteAccount()
    {
        $randString = random_int(1, 100);
        $this->model->email = 'del_'.$randString.'_'.$this->model->email;
        $this->model->phone_number = ! empty($this->model->phone_number) ? 'del_'.$randString.'_'.$this->model->phone_number : null;
        $this->model->status = User::STATUS_DEACTIVATED;

        return $this->model->save();
    }

    /**
     * Check required OTP during login.
     * @return bool
     */
    public function requireOtp(): bool
    {
        if ($this->isUserEligible()) {
            return $this->model->setting['otp'] || $this->model->status === User::STATUS_PENDING;
        }

        return false;
    }
}
