<?php

use Aparlay\Core\Admin\Models\User;
use Illuminate\Database\Migrations\Migration;
use Maklad\Permission\Models\Permission;
use Maklad\Permission\Models\Role;

class AlertRolePermission extends Migration
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

        foreach (User::where('type', User::TYPE_ADMIN)->limit(5)->get() as $user) {
            $user->assignRole('super-administrator');
        }

        if (($user = User::where('email', 'ramin@aparlay.com')->first()) !== null) {
            $user->assignRole('super-administrator');
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
