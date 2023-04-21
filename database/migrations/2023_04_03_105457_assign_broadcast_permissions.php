<?php

use Aparlay\Core\Admin\Models\Permission;
use Aparlay\Core\Constants\Permissions;
use Aparlay\Core\Constants\Roles;
use Illuminate\Database\Migrations\Migration;
use Maklad\Permission\Models\Role;

return new class() extends Migration {
    public const PERMISSIONS_LIST = [
        Permissions::VIEW_BROADCASTS,
        Permissions::LIST_BROADCASTS,
        Permissions::DELETE_BROADCASTS,
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $roleNames = [
            Roles::ADMINISTRATOR,
            Roles::SUPER_ADMINISTRATOR,
        ];

        $roles     = Role::whereIn('name', $roleNames)
            ->where('guard_name', 'admin')
            ->get();

        foreach ($roles as $role) {
            foreach (self::PERMISSIONS_LIST as $permissionName) {
                $role->givePermissionTo(Permission::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'admin',
                ]));
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};