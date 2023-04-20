<?php

use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Models\Enums\UserType;
use Illuminate\Database\Migrations\Migration;
use Maklad\Permission\Models\Permission;
use Maklad\Permission\Models\Role;

class DefineRolesPermissions extends Migration
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
                'dashboard',

                'create roles',
                'edit roles',
                'delete roles',
                'list roles',
                'show roles',

                'create alerts',
                'edit alerts',
                'delete alerts',
                'list alerts',
                'show alerts',

                'create permissions',
                'edit permissions',
                'delete permissions',
                'list permissions',
                'show permissions',

                'create users',
                'edit users',
                'delete users',
                'list users',
                'show users',

                'create medias',
                'edit medias',
                'delete medias',
                'list medias',
                'show medias',
                'upload medias',

                'list emails',
            ],
            'administrator' => [
                'dashboard',

                'create alerts',
                'edit alerts',
                'delete alerts',
                'list alerts',
                'show alerts',

                'create permissions',
                'edit permissions',
                'delete permissions',
                'list permissions',
                'show permissions',

                'create users',
                'edit users',
                'delete users',
                'list users',
                'show users',

                'create medias',
                'edit medias',
                'delete medias',
                'list medias',
                'show medias',
                'upload medias',
            ],
            'support' => [
                'dashboard',

                'edit users',
                'list users',
                'show users',

                'create medias',
                'edit medias',
                'delete medias',
                'list medias',
                'show medias',
                'upload medias',
            ],
        ];
        foreach ($roles as $role => $permissionNames) {
            $role = Role::firstOrCreate(['name' => $role, 'guard_name' => 'admin']);
            foreach ($permissionNames as $name) {
                $role->givePermissionTo(Permission::firstOrCreate(['name' => $name, 'guard_name' => 'admin']));
            }
        }

        foreach (User::where('type', UserType::ADMIN->value)->limit(5)->get() as $user) {
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
