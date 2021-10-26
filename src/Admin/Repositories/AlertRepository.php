<?php

namespace Aparlay\Core\Admin\Repositories;

use Aparlay\Core\Admin\Models\Alert;

class AlertRepository
{
    protected Alert $model;

    public function __construct($model)
    {
        if (! ($model instanceof Alert)) {
            throw new \InvalidArgumentException('$model should be of User type');
        }

        $this->model = $model;
    }

    public function store($data)
    {
        return $this->model->create($data);
    }
}
