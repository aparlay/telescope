<?php

namespace Aparlay\Core\Admin\Repositories;

use InvalidArgumentException;
use Maklad\Permission\Models\Role;

class RoleRepository
{
    protected Role $model;

    public function __construct($model)
    {
        if (!($model instanceof Role)) {
            throw new InvalidArgumentException('$model should be of Role type');
        }

        $this->model = $model;
    }

    public function all()
    {
        return $this->model->with('permissions')->get();
    }
}
