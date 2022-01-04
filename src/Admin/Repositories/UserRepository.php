<?php

namespace Aparlay\Core\Admin\Repositories;

use Aparlay\Core\Admin\Models\User;

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

    public function all($offset, $limit, $sort)
    {
        return $this->model->sortBy($sort)
            ->skip($offset)
            ->take($limit)
            ->get();
    }

    public function countFilteredUser($filters, $dateRangeFilter = null)
    {
        $query = $this->model->filter($filters);

        if ($dateRangeFilter) {
            $query->date($dateRangeFilter['start'], $dateRangeFilter['end']);
        }

        return $query->count();
    }

    public function getFilteredUser($offset, $limit, $sort, $filters, $dateRangeFilter = null)
    {
        $query = $this->model->filter($filters)
            ->sortBy($sort)
            ->skip($offset)
            ->take($limit);

        if ($dateRangeFilter) {
            $query->date($dateRangeFilter['start'], $dateRangeFilter['end']);
        }

        return $query->get();
    }


    public function countDocs(User $user, $status)
    {
        return $user->userDocumentObjs()->status($status)->count();
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
