<?php

use Aparlay\Core\Constants\Roles;
use Illuminate\Database\Migrations\Migration;
use Maklad\Permission\Models\Permission;
use Maklad\Permission\Models\Role;

class AddUserModerationQueuePermission extends Migration
{
    const PERMISSION_QUEUE_USER_MODERATION = 'queue users-moderation';
    const PERMISSION_QUEUE_MEDIA_MODERATION = 'queue medias-moderation';

    const PERMISSION_LIST_MEDIA_MODERATION = 'list medias-moderation';
    const PERMISSION_LIST_USER_MODERATION = 'list users-moderation';

    const PERMISSIONS_QUEUE = [
        self::PERMISSION_QUEUE_MEDIA_MODERATION,
        self::PERMISSION_QUEUE_USER_MODERATION,
    ];

    const PERMISSIONS_LIST = [
        self::PERMISSION_LIST_MEDIA_MODERATION,
        self::PERMISSION_LIST_USER_MODERATION,
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
            Roles::ADMINISTRATOR,
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
            Roles::ADMINISTRATOR,
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
