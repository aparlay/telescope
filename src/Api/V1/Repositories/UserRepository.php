<?php

namespace Aparlay\Core\Api\V1\Repositories;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Notifications\UserDeactivateAccount;
use Aparlay\Core\Models\Enums\UserStatus;
use Aparlay\Core\Models\User as BaseUser;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class UserRepository
{
    protected User|BaseUser $model;

    public function __construct($model)
    {
        if (!($model instanceof BaseUser)) {
            throw new InvalidArgumentException('$model should be of User type');
        }

        $this->model = $model;
    }

    /**
     * Verifying the user.
     */
    public function verify(): bool
    {
        $this->model->status         = UserStatus::VERIFIED->value;
        $this->model->email_verified = true;

        return $this->model->save(['status', 'email_verified']);
    }

    /**
     * Through exception if user is suspended/banned/not found.
     */
    public function isUserEligible(): bool
    {
        switch ($this->model->status) {
            case UserStatus::BLOCKED->value:

                abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'The user has been banned.');

                // no break
            case UserStatus::DEACTIVATED->value:

                abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'User account not found.');

                // no break
            default:
                return true;
        }
    }

    /**
     * Check user's login eligibility.
     */
    public function isUserEligibleForLogin(): bool
    {
        switch ($this->model->status) {
            case UserStatus::BLOCKED->value:

                abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Cannot login, user banned.');

                // no break
            case UserStatus::DEACTIVATED->value:

                abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Cannot login, user account not found.');

                // no break
            case UserStatus::SUSPENDED->value:

                abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Cannot login, user suspended.');

                // no break
            default:
                return true;
        }
    }

    /**
     * Responsible to check if OTP is required to sent to the user, based on user_status and otp settings.
     */
    public function isUnverified(): bool
    {
        /* User is considered as unverified when "OTP Setting is enabled AND user status is pending" */
        return $this->model->setting['otp'] && $this->model->status === UserStatus::PENDING->value;
    }

    /**
     * Responsible to check the user is Verified.
     *
     * @throws ValidationException
     */
    public function isVerified(): bool
    {
        /* User is considered as verified when user status is active or verified */
        if (!in_array($this->model->status, [UserStatus::VERIFIED->value, UserStatus::ACTIVE->value], true)) {
            throw ValidationException::withMessages([
                'Account' => ['Your account is not authenticated.'],
            ]);
        }

        return true;
    }

    public function update(array $data, $id)
    {
        return $this->model->user($id)->update($data);
    }

    /**
     * find user by email.
     */
    public function findByEmail(string $email): ?User
    {
        return User::email($email)->first();
    }

    /**
     * find user by phone_number.
     */
    public function findByPhoneNumber(string $phoneNumber): ?User
    {
        return User::phoneNumber($phoneNumber)->first();
    }

    /**
     * find user by username.
     */
    public function findByUsername(string $userName): ?User
    {
        return User::username($userName)->first();
    }

    /**
     * Responsible for change old password.
     */
    public function resetPassword(string $password): bool
    {
        $this->model->password_hash = Hash::make($password);

        return $this->model->save();
    }

    /**
     * Responsible for delete user account.
     *
     * @throws Exception
     */
    public function deleteAccount(string $reason = ''): bool
    {
        $randString                       = random_int(1, 100);
        $this->model->email               = 'del_' . $randString . '_' . $this->model->email;
        $this->model->phone_number        = !empty($this->model->phone_number) ? 'del_' . $randString . '_' . $this->model->phone_number : null;
        $this->model->status              = UserStatus::DEACTIVATED->value;
        $this->model->deactivation_reason = $reason;
        if ($this->model->save()) {
            $this->model->unsearchable();
            $this->model->notify(new UserDeactivateAccount());

            return true;
        }

        return false;
    }

    /**
     * Check required OTP during login.
     */
    public function requireOtp(): bool
    {
        if ($this->isUserEligible()) {
            return $this->model->setting['otp'] || $this->model->status === UserStatus::PENDING->value;
        }

        return false;
    }
}
