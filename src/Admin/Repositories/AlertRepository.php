<?php

namespace Aparlay\Core\Admin\Repositories;

use Aparlay\Core\Admin\Models\Alert;
use InvalidArgumentException;

class AlertRepository implements RepositoryInterface
{
    protected Alert $model;

    public function __construct($model)
    {
        if (!($model instanceof Alert)) {
            throw new InvalidArgumentException('$model should be of Alert type');
        }

        $this->model = $model;
    }

    public function all()
    {
    }

    public function create(array $data)
    {
        return Alert::create($data);
    }

    public function firstOrCreate(array $data)
    {
        return Alert::firstOrCreate($data);
    }

    public function update(array $data, $id)
    {
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }
}
