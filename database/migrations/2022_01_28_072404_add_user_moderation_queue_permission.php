<?php

use Illuminate\Database\Migrations\Migration;
use Maklad\Permission\Models\Permission;
use Maklad\Permission\Models\Role;

class AddUserModerationQueuePermission extends Migration
{
    const ROLE_LIST_USER_MODERATION = 'list users-moderation';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $superAdminRole = Role::firstOrCreate(['name' => 'super-administrator', 'guard_name' => 'admin']);
        $adminRole = Role::firstOrCreate(['name' => 'administrator', 'guard_name' => 'admin']);

        $roles = [$superAdminRole, $adminRole];

        foreach ($roles as $role) {
            $role->givePermissionTo(Permission::firstOrCreate([
                'name' => self::ROLE_LIST_USER_MODERATION,
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
