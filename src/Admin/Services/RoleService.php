<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\Permission;
use Aparlay\Core\Admin\Repositories\PermissionRepository;
use Aparlay\Core\Admin\Repositories\RoleRepository;
use Maklad\Permission\Models\Role;

class RoleService
{
    protected RoleRepository $roleRepository;
    protected PermissionRepository $permissionRepository;

    public function __construct()
    {
        $this->roleRepository       = new RoleRepository(new Role());
        $this->permissionRepository = new PermissionRepository(new Permission());
    }

    public function all()
    {
        $roles = $this->roleRepository->all();

        foreach ($roles as $role) {
            $role->unassigned_permission = $this->permissionRepository->getUnassignedPermission($role->_id);
        }

        return $roles;
    }

    public function updateRole($role)
    {
        return request()->input('action') === 'attach' ?
            $role->givePermissionTo(request()->input('permissions')) :
            $role->revokePermissionTo(request()->input('permissions'));
    }
}
