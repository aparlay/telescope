<?php

use Aparlay\Core\Admin\Models\User;
use Illuminate\Database\Migrations\Migration;
use Maklad\Permission\Models\Permission;
use Maklad\Permission\Models\Role;

class EmailRolePermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * @throws ReflectionException
     */
    public function up()
    {
        $roles = [
            'super-administrator' => [

                'list emails',
            ],
            'administrator' => [

                'list emails',
            ],
            'support' => [

                'list emails',
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
