<?php

use Illuminate\Database\Migrations\Migration;
use Maklad\Permission\Models\Permission;
use Maklad\Permission\Models\Role;

class AlertRolePermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @throws ReflectionException
     *
     * @return void
     */
    public function up()
    {
        $roles = [
            'super-administrator' => [

                'create alerts',
            ],
            'administrator' => [

                'create alerts',
            ],
            'support' => [

                'create alerts',
            ],
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
        Role::truncate();
        Permission::truncate();
    }
}
