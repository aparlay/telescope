<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Requests\UpdateRoleRequest;
use Aparlay\Core\Admin\Services\RoleService;
use Maklad\Permission\Models\Role;

class RoleController
{
    protected $roleService;

    public function __construct(
        RoleService $roleService
    ) {
        $this->roleService = $roleService;
    }

    public function index()
    {
        $roles = $this->roleService->all();

        return view('default_view::admin.pages.role.index', compact('roles'));
    }

    public function updateRole(Role $role, UpdateRoleRequest $request)
    {
        if ($this->roleService->updateRole($role)) {
            return back()->with(['success' => 'Successfully updated role.']);
        }

        return back()->with(['error' => 'Update role failed.']);
    }
}
