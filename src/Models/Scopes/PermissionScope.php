<?php

namespace Aparlay\Core\Models\Scopes;

trait PermissionScope
{
    use BaseScope;

    /**
     * @param $query
     * @param $roleId
     * @return mixed
     */
    public function scopeNoPermission($query, $roleId): mixed
    {
        return $query->where('role_ids', '!=', $roleId);
    }
}
