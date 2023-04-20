<?php

namespace Aparlay\Core\Models\Scopes;

trait PermissionScope
{
    use BaseScope;

    public function scopeNoPermission($query, $roleId): mixed
    {
        return $query->where('role_ids', '!=', $roleId);
    }
}
