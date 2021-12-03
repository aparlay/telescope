<?php

use Illuminate\Database\Migrations\Migration;
use Maklad\Permission\Models\Role;
use Maklad\Permission\Models\Permission;
use Aparlay\Core\Admin\Models\Setting;

class SettingRolePermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $roles = [
            'super-administrator' => [
                'create settings',
                'edit settings',
                'delete settings',
                'list settings',
                'show settings',
            ],
            'administrator' => [
                'create settings',
                'edit settings',
                'delete settings',
                'list settings',
                'show settings',
            ],
            'support' => [
                'list settings',
                'show settings',
            ]
        ];

        foreach ($roles as $role => $permissionNames) {
            $role = Role::firstOrCreate(['name' => $role, 'guard_name' => 'admin']);
            foreach ($permissionNames as $name) {
                $role->givePermissionTo(Permission::firstOrCreate(['name' => $name, 'guard_name' => 'admin']));
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
        Setting::truncate();
    }
}
