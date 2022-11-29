<?php

namespace Aparlay\Core\Admin\Repositories;

use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Events\UserVerificationStatusChangedEvent;
use Aparlay\Core\Models\Enums\UserVerificationStatus;

class UserRepository
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
     * @return int
     */
    public function countPending()
    {
        return User::query()
            ->where('verification_status', UserVerificationStatus::PENDING->value)
            ->count();
    }

    /**
     * @return bool
     */
    public function hasPending()
    {
        return User::query()
            ->where('verification_status', UserVerificationStatus::PENDING->value)
            ->first() !== null;
    }

    /**
     * @param $user
     * @return mixed
     */
    public function firstUnderReview($user)
    {
        return User::query()
            ->where('verification_status', UserVerificationStatus::UNDER_REVIEW->value)
            ->updatedBy($user->_id)
            ->latest()
            ->first();
    }

    public function revertAllToPending($currentUser)
    {
        User::query()
            ->updatedBy($currentUser->_id)
            ->where('verification_status', UserVerificationStatus::UNDER_REVIEW->value)
            ->update(['verification_status' => UserVerificationStatus::PENDING->value]);
    }

    public function firstNextPending($nextUserId)
    {
        $query = User::query()
            ->where('_id', '>', $nextUserId)
            ->where('verification_status', UserVerificationStatus::PENDING->value)
            ->orderBy('_id', 'ASC');

        return $query->first();
    }

    public function firstPrevPending($nextUserId)
    {
        return User::query()
            ->where('_id', '<', $nextUserId)
            ->where('verification_status', UserVerificationStatus::PENDING->value)
            ->orderBy('_id', 'DESC')
            ->first();
    }

    public function firstPending()
    {
        return User::query()
            ->where('verification_status', UserVerificationStatus::PENDING->value)
            ->latest()
            ->first();
    }

    public function all($offset, $limit, $sort)
    {
        return $this->model->sortBy($sort)
            ->skip($offset)
            ->take($limit)
            ->get();
    }

    /**
     * @param $user
     * @return mixed
     */
    public function setToUnderReview($user)
    {
        $user->verification_status = UserVerificationStatus::UNDER_REVIEW->value;
        $user->save();

        return $user;
    }

    public function updateVerificationStatus($adminUser, $user, int $verificationStatus)
    {
        $oldVerificationStatus = $user->verification_status;
        $user->verification_status = $verificationStatus;
        $user->save();

        UserVerificationStatusChangedEvent::dispatchIf(
            ($oldVerificationStatus !== $verificationStatus) &&
            (in_array(UserVerificationStatus::VERIFIED->value, [$verificationStatus, $oldVerificationStatus])),
            $adminUser,
            $user,
            $verificationStatus
        );

        return $user;
    }

    /**
     * @param \Aparlay\Core\Admin\Models\User $user
     * @param string $settingKey
     * @param mixed $settingValue
     * @return bool
     */
    public function updateSetting(User $user, string $settingKey, mixed $settingValue)
    {
        $user['setting.' . $settingKey] = $settingValue;
        return $user->save();
    }

    public function countFilteredUser($text, $filters, $dateRangeFilter = null)
    {
        $query = $this->model->textSearch($text)->filter($filters);

        if ($dateRangeFilter) {
            $query->date($dateRangeFilter['start'], $dateRangeFilter['end']);
        }

        return $query->count();
    }

    public function getFilteredUser($text, $filters, $offset, $limit, $sort, $dateRangeFilter = null)
    {
        $query = $this->model->filter($filters)
            ->textSearch($text)
            ->sortBy($sort)
            ->skip($offset)
            ->take($limit);

        if ($dateRangeFilter) {
            $query->date($dateRangeFilter['start'], $dateRangeFilter['end']);
        }

        return $query->get();
    }

    public function create(array $data)
    {
        // TODO: Implement create() method.
    }

    public function update(array $data, $id)
    {
        return $this->find($id)->fill($data)->save();
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function getUserStatuses()
    {
        return $this->model->getStatuses();
    }

    public function getVisibilities()
    {
        return $this->model->getVisibilities();
    }

    public function countCollection()
    {
        return $this->model->count();
    }
}
