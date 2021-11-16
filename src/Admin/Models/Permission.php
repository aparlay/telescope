<?php

namespace Aparlay\Core\Admin\Models;

use Aparlay\Core\Models\Scopes\PermissionScope;
use Maklad\Permission\Models\Permission as BasePermission;

class Permission extends BasePermission
{
    use PermissionScope;
}
