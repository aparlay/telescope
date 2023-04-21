<?php

namespace Aparlay\Core\Admin\Repositories;

use Aparlay\Core\Admin\Models\Analytic;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class AnalyticRepository implements RepositoryInterface
{
    protected Analytic $model;

    public function __construct($model)
    {
        if (!($model instanceof Analytic)) {
            throw new InvalidArgumentException('$model should be of User type');
        }

        $this->model = $model;
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

    public function getAnalytics($fromDate, $toDate): Collection
    {
        return $this->model->filterDate($fromDate, $toDate)->orderBy('date')->take(30)->get();
    }
}
