<?php

namespace Aparlay\Core\Admin\Models;

use Aparlay\Core\Models\Scopes\PermissionScope;
use Maklad\Permission\Models\Permission as BasePermission;
use OwenIt\Auditing\Contracts\Auditable;

class Permission extends BasePermission implements Auditable
{
    use PermissionScope, \OwenIt\Auditing\Auditable;
}
