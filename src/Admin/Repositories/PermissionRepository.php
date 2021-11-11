<?php

namespace Aparlay\Core\Admin\Repositories;

use Maklad\Permission\Models\Permission;

class PermissionRepository
{
    protected Permission $model;

    public function __construct($model)
    {
        if (!($model instanceof Permission)) {
            throw new \InvalidArgumentException('$model should be of Permission type');
        }

        $this->model = $model;
    }

    public function getAnassignedPermission($id)
    {
        return $this->model->where('role_ids', '!=', $id)->get();
    }
}
