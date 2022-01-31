<?php

namespace Aparlay\Core\Admin\Repositories;

use Aparlay\Core\Admin\Models\User;
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

    public function revertAllToPending($currentUser, $exceptUser)
    {
        User::query()
            ->updatedBy($currentUser->_id)
            ->where('_id', '!=', $exceptUser->_id)
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

    public function updateVerificationStatus($userId, $verificationStatus)
    {
        /** @var \App\Models\User $user */
        $user = User::query()->find($userId);
        $user->verification_status = $verificationStatus;
        $user->save();

        return $user;
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
