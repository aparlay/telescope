<?php

use Aparlay\Core\Admin\Models\Permission;
use Aparlay\Core\Constants\Roles;
use Illuminate\Database\Migrations\Migration;
use Maklad\Permission\Models\Role;

return new class() extends Migration {
    const PERMISSION_DELETE_MEDIA_COMMENTS = 'delete media-comments';

    const PERMISSIONS_LIST = [
        self::PERMISSION_DELETE_MEDIA_COMMENTS,
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
