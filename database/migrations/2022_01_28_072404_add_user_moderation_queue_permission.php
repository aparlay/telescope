<?php

use Illuminate\Database\Migrations\Migration;
use Maklad\Permission\Models\Permission;
use Maklad\Permission\Models\Role;

class AddUserModerationQueuePermission extends Migration
{
    const PERMISSION_LIST_USER_MODERATION = 'queue users-moderation';
    const PERMISSION_QUEUE_MEDIA_MODERATION = 'queue medias-moderation';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $superAdminRole = Role::firstOrCreate(['name' => 'super-administrator', 'guard_name' => 'admin']);
        $adminRole = Role::firstOrCreate(['name' => 'administrator', 'guard_name' => 'admin']);
        $supportRole = Role::firstOrCreate(['name' => 'support', 'guard_name' => 'admin']);

        $roles = [$superAdminRole, $adminRole, $supportRole];

        foreach ($roles as $role) {
            $role->givePermissionTo(Permission::firstOrCreate([
                'name' => self::PERMISSION_LIST_USER_MODERATION,
                'guard_name' => 'admin',
            ]));

            $role->givePermissionTo(Permission::firstOrCreate([
                'name' => self::PERMISSION_QUEUE_MEDIA_MODERATION,
                'guard_name' => 'admin',
            ]));
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
