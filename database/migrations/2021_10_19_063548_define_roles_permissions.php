<?php

use Aparlay\Core\Admin\Models\User;
use Illuminate\Database\Migrations\Migration;
use Maklad\Permission\Models\Permission;
use Maklad\Permission\Models\Role;

class DefineRolesPermissions extends Migration
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
                'create roles',
                'edit roles',
                'delete roles',
                'list roles',
                'show roles',

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

                'create tips',
                'edit tips',
                'delete tips',
                'list tips',
                'show tips',

                'create subscriptions',
                'edit subscriptions',
                'delete subscriptions',
                'list subscriptions',
                'show subscriptions',

                'create orders',
                'edit orders',
                'delete orders',
                'list orders',
                'show orders',

                'create transactions',
                'edit transactions',
                'delete transactions',
                'list transactions',
                'show transactions',

                'create credit cards',
                'edit credit cards',
                'delete credit cards',
                'list credit cards',
                'show credit cards',
            ],
            'administrator' => [
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

                'edit tips',
                'delete tips',
                'list tips',
                'show tips',

                'edit subscriptions',
                'delete subscriptions',
                'list subscriptions',
                'show subscriptions',

                'edit orders',
                'delete orders',
                'list orders',
                'show orders',

                'edit transactions',
                'delete transactions',
                'list transactions',
                'show transactions',

                'edit credit cards',
                'delete credit cards',
                'list credit cards',
                'show credit cards',
            ],
            'support' => [
                'edit users',
                'list users',
                'show users',

                'create medias',
                'edit medias',
                'delete medias',
                'list medias',
                'show medias',
                'upload medias',

                'edit tips',
                'delete tips',
                'list tips',
                'show tips',

                'edit subscriptions',
                'delete subscriptions',
                'list subscriptions',
                'show subscriptions',

                'list orders',
                'show orders',

                'list transactions',
                'show transactions',

                'edit credit cards',
                'delete credit cards',
                'list credit cards',
                'show credit cards',
            ],
        ];
        foreach ($roles as $role => $permissionNames) {
            $role = Role::firstOrCreate(['name' => $role, 'guard_name' => 'admin']);
            $permissions = [];
            foreach ($permissionNames as $name) {
                $permissions[] = Permission::firstOrCreate(['name' => $name, 'guard_name' => 'admin']);
            }

            $role->syncPermissions($permissions);
        }

        foreach (User::where('type', User::TYPE_ADMIN)->limit(5)->get() as $user) {
            $user->assignRole('super-administrator');
        }

        $user = User::where('email', 'ramin@aparlay.com')->firstOrFail();
        $user->assignRole('super-administrator');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Role::get()->delete();
        Permission::get()->delete();
    }
}
