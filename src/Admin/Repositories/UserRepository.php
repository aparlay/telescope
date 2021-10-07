<?php

namespace Aparlay\Core\Admin\Repositories;

use Aparlay\Core\Admin\Models\User;

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

    public function all()
    {
        return $this->model->desc()->paginate(config('core.admin.lists.page_count'));
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
        return $this->model->findOrFail($id);
    }

    public function getFilteredUsers($filters)
    {
        return $this->model->filter($filters)->desc()->paginate(config('core.admin.lists.page_count'));
    }

    public function getUserStatues()
    {
        return $this->model->getStatuses();
    }
}
