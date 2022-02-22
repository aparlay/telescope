<?php

use Aparlay\Core\Constants\Roles;
use Illuminate\Database\Migrations\Migration;
use Maklad\Permission\Models\Permission;
use Maklad\Permission\Models\Role;

class AddPayoutsModerationQueuePermission extends Migration
{
    const PERMISSION_QUEUE_PAYOUTS_MODERATION = 'queue payouts-moderation';
    const PERMISSION_LIST_PAYOUTS = 'list payouts';

    const PERMISSIONS_QUEUE = [
        self::PERMISSION_QUEUE_PAYOUTS_MODERATION,
    ];

    const PERMISSIONS_LIST = [
        self::PERMISSION_LIST_PAYOUTS,
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->assignListPermissions();
        $this->assignQueuePermissions();
    }

    private function assignListPermissions()
    {
        $roleNames = [
            Roles::ADMIN,
            Roles::SUPER_ADMINISTRATOR,
        ];

        $roles = Role::whereIn('name', $roleNames)
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

    private function assignQueuePermissions()
    {
        $roleNames = [
            Roles::ADMIN,
            Roles::SUPER_ADMINISTRATOR,
            Roles::SUPPORT,
        ];

        $roles = Role::whereIn('name', $roleNames)
            ->where('guard_name', 'admin')
            ->get();

        foreach ($roles as $role) {
            foreach (self::PERMISSIONS_QUEUE as $permissionName) {
                $role->givePermissionTo(Permission::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'admin',
                ]));
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
