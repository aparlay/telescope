<?php

namespace Aparlay\Core\Admin\Repositories;

use Aparlay\Core\Admin\Models\Permission;
use InvalidArgumentException;

class PermissionRepository
{
    protected Permission $model;

    public function __construct($model)
    {
        if (!($model instanceof Permission)) {
            throw new InvalidArgumentException('$model should be of Permission type');
        }

        $this->model = $model;
    }

    public function getUnassignedPermission($id)
    {
        return $this->model->noPermission($id)->get();
    }
}
